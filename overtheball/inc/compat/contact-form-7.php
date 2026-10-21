<?php
/**
 * Contact Form 7 compatibility: themed form styles (assets/css/plugins.css),
 * Contact page template integration and optional on-demand asset loading.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * Optionally stop CF7 loading its files on every page.
 */
function otb_cf7_conditional_assets() {
	if ( ! defined( 'WPCF7_VERSION' ) || ! otb_option( 'cf7_conditional_assets' ) ) {
		return;
	}
	add_filter( 'wpcf7_load_js', '__return_false' );
	add_filter( 'wpcf7_load_css', '__return_false' );
}
add_action( 'after_setup_theme', 'otb_cf7_conditional_assets' );

/**
 * Enqueue CF7 files on views that contain a form.
 */
function otb_cf7_enqueue_when_needed() {
	if ( ! defined( 'WPCF7_VERSION' ) || ! otb_option( 'cf7_conditional_assets' ) || ! is_singular() ) {
		return;
	}

	$post     = get_post();
	$has_form = is_page_template( 'page-templates/template-contact.php' )
		|| ( $post && ( has_shortcode( $post->post_content, 'contact-form-7' ) || has_block( 'contact-form-7/contact-form-selector', $post ) ) );

	if ( ! $has_form ) {
		return;
	}
	if ( function_exists( 'wpcf7_enqueue_scripts' ) ) {
		wpcf7_enqueue_scripts();
	}
	if ( function_exists( 'wpcf7_enqueue_styles' ) ) {
		wpcf7_enqueue_styles();
	}
}
add_action( 'wp_enqueue_scripts', 'otb_cf7_enqueue_when_needed', 20 );

/**
 * Give CF7 submit buttons the theme button class.
 *
 * @param string $html Form HTML.
 * @return string
 */
function otb_cf7_submit_class( $html ) {
	return str_replace( 'wpcf7-submit', 'wpcf7-submit btn btn--primary', $html );
}
add_filter( 'wpcf7_form_elements', 'otb_cf7_submit_class' );
