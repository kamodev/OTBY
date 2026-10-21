<?php
/**
 * Template Name: Full Width
 * Template Post Type: page, post, product
 *
 * Wide content area with no sidebar.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part( 'template-parts/page-header' );
otb_content_open( 'site-main--full-width' );

while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/content/page' );
}

otb_content_close();
get_footer();
