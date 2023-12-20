<?php
/**
 * Patreon tier and member functions.
 *
 * @package PatreonList
 */

namespace CAB\PatreonList;

// If token or campaign id are not set, bail.
if ( ! defined( 'CAB_PATREON_ACCESS_TOKEN' ) || ! defined( 'CAB_PATREON_CAMPAIGN_ID' ) ) {
	return;
}

// Set Patreon API Endpoint.
define( 'CAB_PATREON_API_ENDPOINT', 'https://www.patreon.com/api/oauth2/v2/campaigns/' . CAB_PATREON_CAMPAIGN_ID );

/**
 * Make call to Patreon API using specified arguments.
 *
 * @param array  $args The api arguments.
 * @param string $slug The trailing slug.
 * @return array
 */
function get_remote_api_data( $args = array(), $slug = '' ) {

	$defaults = array(
		'page[size]'     => '',
		'include'        => '',
		'fields[tier]'   => '',
		'fields[member]' => '',
	);
	$args     = array_filter( shortcode_atts( $defaults, $args ) );
	$endpoint = add_query_arg(
		$args,
		CAB_PATREON_API_ENDPOINT . $slug
	);

	$api_response = wp_remote_get(
		$endpoint,
		array( 'headers' => array( 'Authorization' => 'Bearer ' . CAB_PATREON_ACCESS_TOKEN ) ),
	);

	if ( is_wp_error( $api_response ) || 200 !== wp_remote_retrieve_response_code( $api_response ) ) {
		return false;
	}

	return json_decode( wp_remote_retrieve_body( $api_response ), true );
}

/**
 * Check if stored timestamp has expired.
 *
 * @param integer $minutes Amount of time before expiration in minutes.
 * @return boolean
 */
function is_timestamp_expired( $minutes ) {
	$tier_timestamp    = get_option( 'patreon_tier_timestamp', 0 );
	$current_timestamp = gmdate( 'U' );
	return $tier_timestamp < ( $current_timestamp - $minutes * MINUTE_IN_SECONDS );
}

/**
 * Grab Patreon Member data from API.
 *
 * @param array $old_data Previously stored member data.
 * @return array
 */
function get_patreon_members( $old_data = array() ) {
	$members = get_remote_api_data(
		array(
			'page[size]'     => 200,
			'include'        => 'currently_entitled_tiers',
			'fields[tier]'   => 'title',
			'fields[member]' => 'full_name',
		),
		'/members'
	);

	if ( ! $members ) {
		return $old_data;
	}

	$member_data = $members['data'] ?? null;

	if ( empty( $member_data ) || ! is_array( $member_data ) ) {
		return $old_data;
	}

	update_option( 'patreon_member_array', $member_data, false );
	update_option( 'patreon_tier_timestamp', gmdate( 'U' ), false );

	return $member_data;
}

/**
 * Grab Patreon Tier data from API.
 *
 * @param array $old_data Previously stored tier data.
 * @return array
 */
function get_patreon_tiers( $old_data = array() ) {
	$tiers = get_remote_api_data(
		array(
			'include'      => 'tiers',
			'fields[tier]' => 'amount_cents,title,description',
		),
	);

	if ( ! $tiers ) {
		return $old_data;
	}

	$tier_data = $tiers['included'] ?? null;

	if ( empty( $tier_data ) || ! is_array( $tier_data ) ) {
		return $old_data;
	}

	$tier_new = array();

	foreach ( $tier_data as $tier ) {
		$tier_new[ $tier['id'] ] = array(
			'title'       => $tier['attributes']['title'] ?? '',
			'description' => $tier['attributes']['description'] ?? '',
			'amount'      => $tier['attributes']['amount_cents'] ? ( intval( $tier['attributes']['amount_cents'] ) / 100 ) : '',
		);
	}

	update_option( 'patreon_tier_array', $tier_new, false );
	update_option( 'patreon_tier_timestamp', gmdate( 'U' ), false );

	return $tier_new;
}

/**
 * Display Patreon tiers with members.
 *
 * @param array $atts An array of attributes passed.
 * @return void
 */
function display_patrons( $atts = array() ) {
	$defaults = array(
		'minutes' => 60,
	);

	$atts    = shortcode_atts( $defaults, $atts );
	$tiers   = get_option( 'patreon_tier_array', array() );
	$members = get_option( 'patreon_member_array', array() );

	// Timestamp expired, re-run all updates.
	if ( is_timestamp_expired( intval( $atts['minutes'] ) ) ) {
		$tiers   = get_patreon_tiers( $tiers );
		$members = get_patreon_members( $members );
	}

	// No saved tier array.
	if ( empty( $tiers ) ) {
		$tiers = get_patreon_tiers();
	}

	// No saved member array.
	if ( empty( $members ) ) {
		$members = get_patreon_members();
	}

	// Tiers array is empty, bail.
	if ( empty( $tiers ) ) {
		return;
	}

	ob_start();

	echo '<div class="patreon-list">';
	foreach ( $tiers as $tier_id => $tier_array ) {

		$tier_title       = $tier_array['title'];
		$tier_description = $tier_array['description'];
		$tier_amount      = $tier_array['amount'];
		$member_names     = array();
		?>

		<div class="patreon-tier">
		<h2 class="patreon-tier__title"><span><?php echo esc_html( $tier_title ); ?></span></h2>
		<?php
		if ( ! empty( $tier_amount ) ) {
			?>
			<p class="patreon-tier__amount">For $<?php echo esc_html( $tier_amount ); ?>+ per month:</p>
			<?php
		}
		?>
		<div class="patreon-tier__description"><?php echo wp_kses_post( strip_tags( $tier_description, '<ul><li>' ) ); ?></div>

		<?php
		if ( ! empty( $members ) ) {
			foreach ( $members as $member ) {

				$member_key = $member['relationships']['currently_entitled_tiers']['data'][0]['id'] ?? null;

				if ( ! $member_key || intval( $member_key ) !== intval( $tier_id ) ) {
					continue;
				}

				$full_name                  = $member['attributes']['full_name'] ? trim( $member['attributes']['full_name'] ) : '';
				$name                       = explode( ' ', $full_name );
				$lname                      = end( $name );
				$member_names[ $full_name ] = $lname;
			}
		}

		if ( ! empty( $member_names ) ) {
			natcasesort( $member_names );
			echo '<ul class="patreon-tier__list">';
			foreach ( $member_names as $key => $value ) {
				echo '<li class="patreon-tier__item">' . esc_html( $key ) . '</li>';
			}
			echo '</ul>';
		}
		echo '</div>';
	}
	echo '</div>';

	$output = ob_get_clean();
	return $output;
}

add_shortcode( 'cab_patreon_tier_list', __NAMESPACE__ . '\display_patrons' );
