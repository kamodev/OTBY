<?php
/**
 * Main template: blog index, archives and search results.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ( is_archive() || is_home() || is_search() ) && otb_elementor_location( 'archive' ) ) {
	get_footer();
	return;
}

get_template_part( 'template-parts/page-header' );
otb_content_open( 'site-main--archive' );

if ( have_posts() ) {
	$otb_layout  = is_search() ? 'list' : otb_option( 'blog_layout' );
	$otb_columns = 'grid' === $otb_layout ? absint( otb_option( 'blog_columns' ) ) : 1;
	$otb_part    = 'classic' === $otb_layout ? 'content' : 'card';

	echo '<div class="post-grid post-grid--' . esc_attr( $otb_columns ) . ' post-layout--' . esc_attr( $otb_layout ) . '">';
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content/' . $otb_part, get_post_type() );
	}
	echo '</div>';

	otb_pagination();
} else {
	get_template_part( 'template-parts/content/none' );
}

otb_content_close();
get_footer();
