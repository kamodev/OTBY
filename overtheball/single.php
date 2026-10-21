<?php
/**
 * Single post.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! otb_elementor_location( 'single' ) ) {
	get_template_part( 'template-parts/page-header' );
	otb_content_open( 'site-main--single' );

	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content/single', get_post_type() );

		if ( otb_option( 'single_nav' ) && 'post' === get_post_type() ) {
			the_post_navigation(
				array(
					'prev_text' => '<span class="nav-subtitle">' . otb_get_icon( 'chevron-left', array( 'size' => 16 ) ) . esc_html__( 'Previous', 'overtheball' ) . '</span><span class="nav-title">%title</span>',
					'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next', 'overtheball' ) . otb_get_icon( 'chevron-right', array( 'size' => 16 ) ) . '</span><span class="nav-title">%title</span>',
				)
			);
		}

		if ( 'post' === get_post_type() ) {
			otb_related_posts();
		}

		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	}

	otb_content_close();
}

get_footer();
