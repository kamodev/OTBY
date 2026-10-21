<?php
/**
 * Shortcodes exposing theme sections anywhere — including Elementor's
 * Shortcode widget and the block editor's Shortcode block.
 *
 *   [otb_section name="hero|features|categories|products|weights|promo|about|testimonials|blog|gallery|newsletter"]
 *   [otb_social]  [otb_contact]  [otb_newsletter]
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * Capture a template part as a string.
 *
 * @param string $slug Template slug.
 * @return string
 */
function otb_capture_template( $slug ) {
	ob_start();
	get_template_part( $slug );
	return (string) ob_get_clean();
}

/**
 * [otb_section name="…"]
 *
 * @param array $atts Attributes.
 * @return string
 */
function otb_section_shortcode( $atts ) {
	$atts    = shortcode_atts( array( 'name' => '' ), $atts, 'otb_section' );
	$name    = sanitize_key( $atts['name'] );
	$allowed = array_diff( array_keys( otb_home_section_labels() ), array( 'content' ) );

	if ( ! in_array( $name, $allowed, true ) ) {
		return '';
	}
	return otb_capture_template( 'template-parts/home/' . $name );
}
add_shortcode( 'otb_section', 'otb_section_shortcode' );

/**
 * [otb_social]
 *
 * @return string
 */
function otb_social_shortcode() {
	ob_start();
	otb_social_links( 'otb-social--shortcode' );
	return (string) ob_get_clean();
}
add_shortcode( 'otb_social', 'otb_social_shortcode' );

/**
 * [otb_contact]
 *
 * @return string
 */
function otb_contact_shortcode() {
	ob_start();
	otb_contact_list( array( 'class' => 'otb-contact--cards' ) );
	return (string) ob_get_clean();
}
add_shortcode( 'otb_contact', 'otb_contact_shortcode' );

/**
 * [otb_newsletter] — the newsletter strip.
 *
 * @return string
 */
function otb_newsletter_shortcode() {
	return otb_capture_template( 'template-parts/footer/newsletter' );
}
add_shortcode( 'otb_newsletter', 'otb_newsletter_shortcode' );
