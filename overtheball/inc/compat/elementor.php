<?php
/**
 * Elementor & Elementor Pro compatibility.
 *
 * - Registers every Theme Builder location (header, footer, single, archive).
 * - Header/footer/single templates defer to Elementor Pro when a template is assigned.
 * - "Elementor Full Width" / "Elementor Canvas" and the theme's Blank Canvas template all work.
 * - Theme colors are exposed as CSS variables (var(--otb-primary) etc.) usable in Elementor.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * Let Elementor Pro's Theme Builder replace a location.
 *
 * @param string $location header|footer|single|archive.
 * @return bool True when Elementor rendered the location.
 */
function otb_elementor_location( $location ) {
	return function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( $location );
}

/**
 * Register Theme Builder locations.
 *
 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $manager Locations manager.
 */
function otb_elementor_register_locations( $manager ) {
	$manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'otb_elementor_register_locations' );

/**
 * Elementor pages built with the editor get a stretched content area by default,
 * so sections can span the full width.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function otb_elementor_body_classes( $classes ) {
	if ( is_singular() && otb_is_elementor_page() ) {
		$classes[] = 'otb-elementor-page';
	}
	return $classes;
}
add_filter( 'body_class', 'otb_elementor_body_classes' );

/**
 * Load theme styles inside the Elementor editor preview so widgets match.
 */
function otb_elementor_preview_styles() {
	wp_enqueue_style( 'otb-plugins', OTB_URI . 'assets/css/plugins.css', array(), OTB_VERSION );
}
add_action( 'elementor/preview/enqueue_styles', 'otb_elementor_preview_styles' );

/**
 * Whether Elementor's "Hide Title" page setting is on for a post.
 *
 * Elementor's own CSS hides "h1.entry-title"; the theme also drops the
 * whole title banner so no empty band is left behind.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function otb_elementor_hides_title( $post_id ) {
	if ( ! $post_id || ! did_action( 'elementor/loaded' ) ) {
		return false;
	}
	$settings = get_post_meta( $post_id, '_elementor_page_settings', true );
	return is_array( $settings ) && isset( $settings['hide_title'] ) && 'yes' === $settings['hide_title'];
}
