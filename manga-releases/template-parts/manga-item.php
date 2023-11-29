<?php
/**
 * Manga Release item template part.
 *
 * @package MangaRelease
 */

namespace CAB\MangaRelease;

$image           = intval( get_post_meta( get_the_ID(), 'manga_featured_img', true ) );
$manga_link      = get_post_meta( get_the_ID(), 'manga_group_link', true );
$release_type    = get_post_meta( get_the_ID(), 'manga_group_type', true );
$release_authors = get_post_meta( get_the_ID(), 'manga_group_authors', true );
$release_company = get_post_meta( get_the_ID(), 'manga_group_company', true );
$release_company = get_term_by( 'term_id', $release_company, 'manga_company' );
?>

<li class="release-single">
	<?php
	if ( $manga_link ) {
		echo '<a href="' . esc_url( $link ) . '" target="_blank" rel="noopener" aria-hidden="true" tabindex="-1">';
	}
	if ( $image ) {
		echo wp_get_attachment_image( $image, 'medium_large', false, array( 'class' => 'release-single__image' ) );
	} else {
		echo '<img src="' . esc_url( get_theme_file_uri( '/assets/images/cover-not-available.jpg' ) ) . '" alt="Cover not available" class="release-single__image" width="422" height="604">';
	}
	if ( $manga_link ) {
		echo '</a>';
	}
	?>
	<div class="release-single__content release-content">
		<?php
		if ( ! empty( $release_company ) ) {
			echo '<span class="release-single__company release-company">' . esc_html( $release_company->name ) . '</span>';
		}

		if ( $link ) {
			echo '<a href="' . esc_url( $manga_link ) . '" target="_blank" rel="noopener" class="release-single__link release-link">';
		}

		the_title();

		if ( $link ) {
			echo '</a>';
		}

		if ( $release_authors ) {
			echo '<span class="release-single__author release-author">' . esc_html__( 'By ', 'cab-manga-release' ) . esc_html( get_post_meta( get_the_ID(), 'manga_group_authors', true ) ) . '</span>';
		}
		?>
	</div>
	<span class="release-single__type release-type type-<?php esc_attr( $release_type ); ?>"><?php esc_html( $release_type ); ?></span>
</li>
