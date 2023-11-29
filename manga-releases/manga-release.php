<?php
/**
 * Manga Release functions.
 *
 * @package MangaRelease
 */

namespace CAB\MangaRelease;

/**
 * Generate a slug for the manga title.
 *
 * @param string $title The manga title.
 * @return string
 */
function get_manga_title_slug( $title ) {
	// Strip all non-ASCII characters from title.
	$result = preg_replace( '/[\x00-\x1F\x80-\xFF]/', '', $title );
	// Remove certain phrases from beginning of title.
	$result = preg_replace( '/\A(a|an|the) /i', '', $result );
	// Create dashed version of title with unique number at end (to prevent duplicates).
	$result = sanitize_title_with_dashes( $result ) . '-' . wp_unique_id();
	return $result;
}

/**
 * Get the slug for the manga date.
 *
 * @param string $taxonomy The taxonomy name.
 * @param string $date     The date name.
 * @return string
 */
function get_manga_date_slug( $taxonomy, $date ) {
	$terms = get_the_terms( get_the_ID(), $taxonomy );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return;
	}

	$dates = wp_list_pluck( $terms, 'term_id' );
	$key   = array_search( $date, $dates, true );
	if ( false !== $key ) {
		unset( $dates[ $key ] );
	}
	$dates = array_values( $dates );
	return get_term_by( 'term_id', $dates[0], $taxonomy )->slug;
}

/**
 * Get publisher name and manga count.
 *
 * @param array $publishers The array of manga counts by publisher.
 * @return void
 */
function get_manga_publishers( &$publishers ) {
	$publisher_name = get_the_terms( get_the_ID(), 'manga_company' )[0]->name;
	if ( ! array_key_exists( $publisher_name, $publishers ) ) {
		$publishers[ $publisher_name ] = 1;
	} else {
		$publishers[ $publisher_name ] = intval( $publishers[ $publisher_name ] ) + 1;
	}
}

/**
 * Display manga release list.
 *
 * @return void
 */
function display_manga_list() {
	$selected_manga_date = intval( get_post_meta( get_the_ID(), 'manga_choice', true ) );
	$ordered_manga_dates = array();
	$manga_taxonomy      = 'manga_release_date';

	if ( empty( $selected_manga_date ) ) {
		return;
	}

	$selected_manga_children = get_terms(
		array(
			'taxonomy'   => $manga_taxonomy,
			'hide_empty' => true,
			'child_of'   => $selected_manga_date,
		)
	);

	if ( is_wp_error( $selected_manga_children ) || empty( $selected_manga_children ) || ! is_array( $selected_manga_children ) ) {
		return;
	}

	foreach ( $selected_manga_children as $selected_manga_child ) {
		$ordered_manga_dates[ $selected_manga_child->slug ] = $selected_manga_child->name;
	}

	natcasesort( $ordered_manga_dates );

	$manga_args = array(
		'post_type'      => 'manga',
		'posts_per_page' => 1000, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
		'no_found_rows'  => true,
		'orderby'        => 'title',
		'order'          => 'ASC',
		'tax_query'      => array(
			array(
				'taxonomy' => $manga_taxonomy,
				'field'    => 'id',
				'terms'    => $selected_manga_date,
			),
		),
	);

	$manga_posts           = new \WP_Query( $manga_args );
	$manga_count_total     = count( $manga_posts->posts );
	$manga_by_date         = array();
	$manga_publishers_list = array();

	update_post_thumbnail_cache( $manga_posts );

	if ( $manga_posts->have_posts() ) :

		while ( $manga_posts->have_posts() ) :
			$manga_posts->the_post();

			$post_date  = get_manga_date_slug( $manga_taxonomy, $selected_manga_date );
			$post_title = get_manga_title_slug( get_the_title() );

			ob_start();
			get_template_part( 'template-parts/manga', 'item' );
			$item = ob_get_clean();

			$manga_by_date[ $post_date ][ $post_title ] = $item;

			get_manga_publishers( $manga_publishers_list );

		endwhile;

	endif;
	wp_reset_postdata();

	get_template_part(
		'template-parts/manga',
		'filters',
		array(
			'total'      => $manga_count_total,
			'dates'      => $ordered_manga_dates,
			'publishers' => $manga_publishers_list,
		)
	);

	if ( ! empty( $manga_by_date ) ) {
		ksort( $manga_by_date, SORT_NATURAL );

		echo '<div class="release-section-wrapper">';
		foreach ( $manga_by_date as $date_key => $post_date ) {
			echo '<section class="release-section">';
			echo '<h2>' . esc_html( $ordered_manga_dates[ $date_key ] ) . '</h2>';
			ksort( $post_date, SORT_NATURAL );

			echo '<ul class="releases">';

			foreach ( $post_date as $item ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $item;
			}
			echo '</ul>';

			echo '</section>';
		}

		echo '</div>';
	}
}
