<?php
/**
 * WooCommerce wrapper template (shop, categories, single products).
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

get_header();

$otb_location = is_singular( 'product' ) ? 'single' : 'archive';

if ( ! otb_elementor_location( $otb_location ) ) {
	get_template_part( 'template-parts/page-header' );
	otb_content_open( is_singular( 'product' ) ? 'site-main--product' : 'site-main--shop' );

	if ( is_singular( 'product' ) ) {
		otb_breadcrumbs();
	}

	woocommerce_content();

	otb_content_close();
}

get_footer();
