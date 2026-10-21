<?php
/**
 * Default page template.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! otb_elementor_location( 'single' ) ) {
	get_template_part( 'template-parts/page-header' );
	otb_content_open( 'site-main--page' );

	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content/page' );

		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	}

	otb_content_close();
}

get_footer();
