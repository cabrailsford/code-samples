<?php
/**
 * Featured article functions.
 *
 * @package FeaturedArticles
 */

namespace CAB\FeaturedArticles;

/**
 * Grab featured articles and loop over them.
 *
 * @return void
 */
function cab_featured_articles() {
	$articles = get_option( 'cab_featured_articles' );
	if ( empty( $articles ) ) {
		$articles = cab_build_featured_articles();
	}
	// Remove an article id from the array each day.
	$offset   = intval( ( time() - $articles['datetime'] ) / DAY_IN_SECONDS );
	$post_ids = array_slice( $articles['post_ids'], $offset );

	// If array count is less than 6, re-run the query.
	if ( 6 > count( $post_ids ) ) {
		$articles = cab_build_featured_articles();
		$post_ids = $articles['post_ids'];
	}
	$loop = new WP_Query(
		array(
			'posts_per_page'      => 6,
			'orderby'             => 'post__in',
			'post__in'            => $post_ids,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	if ( $loop->have_posts() ) :
		echo '<h2>' . esc_html__( 'Featured Articles', 'cab-featured-articles' ) . '</h2>';
		while ( $loop->have_posts() ) :
			$loop->the_post();
			the_title( '<h3><a href="' . esc_url( get_the_permalink() ) . '">', '</a></h3>' );
		endwhile;
	endif;
	wp_reset_postdata();
}
/**
 * Build featured articles and save to option.
 *
 * @return array
 */
function cab_build_featured_articles() {
	$loop = new WP_Query(
		array(
			'posts_per_page'      => 300, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
			'date_query'          => array( array( 'before' => '180 days ago' ) ),
			'fields'              => 'ids',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	$ids  = $loop->posts;
	shuffle( $ids );
	$articles = array(
		'post_ids' => $ids,
		'datetime' => time(),
	);
	update_option( 'cab_featured_articles', $articles, false );
	return $articles;
}
