<?php
/**
 * Theme setup: supports, menus, image sizes, editor integration.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme supports, menus and image sizes.
 */
function otb_setup() {
	load_theme_textdomain( 'overtheball', OTB_DIR . 'languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'link-color' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'               => 380,
			'width'                => 806,
			'flex-height'          => true,
			'flex-width'           => true,
			'unlink-homepage-logo' => false,
		)
	);
	// Elementor Header & Footer Builder.
	add_theme_support( 'header-footer-elementor' );

	add_editor_style( array( 'assets/fonts/fonts.css', 'assets/css/editor-style.css' ) );

	register_nav_menus(
		array(
			'primary'       => __( 'Primary Menu', 'overtheball' ),
			'topbar'        => __( 'Top Bar Menu', 'overtheball' ),
			'mobile'        => __( 'Mobile Menu (falls back to Primary)', 'overtheball' ),
			'footer'        => __( 'Footer Links', 'overtheball' ),
			'footer-bottom' => __( 'Footer Bottom / Legal Menu', 'overtheball' ),
		)
	);

	add_image_size( 'otb-hero', 2000, 1200, false );
	add_image_size( 'otb-wide', 1200, 800, true );
	add_image_size( 'otb-card', 800, 560, true );
	add_image_size( 'otb-portrait', 640, 800, true );
	add_image_size( 'otb-square', 600, 600, true );
}
add_action( 'after_setup_theme', 'otb_setup' );

/**
 * Content width for embeds.
 */
function otb_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'otb_content_width', 820 ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
}
add_action( 'after_setup_theme', 'otb_content_width', 0 );

/**
 * Make the image sizes selectable in the editor.
 *
 * @param array $sizes Sizes.
 * @return array
 */
function otb_image_size_names( $sizes ) {
	return array_merge(
		$sizes,
		array(
			'otb-wide'     => __( 'Wide (3:2)', 'overtheball' ),
			'otb-portrait' => __( 'Portrait (4:5)', 'overtheball' ),
			'otb-square'   => __( 'Square', 'overtheball' ),
		)
	);
}
add_filter( 'image_size_names_choose', 'otb_image_size_names' );

/**
 * Keep the editor color palette and layout widths in sync with the Customizer.
 *
 * @param WP_Theme_JSON_Data $theme_json Theme JSON data.
 * @return WP_Theme_JSON_Data
 */
function otb_filter_theme_json( $theme_json ) {
	$palette = array(
		array(
			'slug'  => 'primary',
			'name'  => __( 'Violet', 'overtheball' ),
			'color' => otb_option( 'color_primary' ),
		),
		array(
			'slug'  => 'primary-dark',
			'name'  => __( 'Deep Violet', 'overtheball' ),
			'color' => otb_option( 'color_primary_dark' ),
		),
		array(
			'slug'  => 'accent',
			'name'  => __( 'Accent', 'overtheball' ),
			'color' => otb_option( 'color_accent' ),
		),
		array(
			'slug'  => 'ink',
			'name'  => __( 'Ink', 'overtheball' ),
			'color' => otb_option( 'color_heading' ),
		),
		array(
			'slug'  => 'text',
			'name'  => __( 'Text', 'overtheball' ),
			'color' => otb_option( 'color_text' ),
		),
		array(
			'slug'  => 'lavender',
			'name'  => __( 'Lavender Mist', 'overtheball' ),
			'color' => otb_option( 'color_surface' ),
		),
		array(
			'slug'  => 'cream',
			'name'  => __( 'Cream', 'overtheball' ),
			'color' => otb_option( 'color_cream' ),
		),
		array(
			'slug'  => 'border',
			'name'  => __( 'Border', 'overtheball' ),
			'color' => otb_option( 'color_border' ),
		),
		array(
			'slug'  => 'white',
			'name'  => __( 'White', 'overtheball' ),
			'color' => '#FFFFFF',
		),
	);

	return $theme_json->update_with(
		array(
			'version'  => 3,
			'settings' => array(
				'color'  => array( 'palette' => $palette ),
				'layout' => array(
					'wideSize' => absint( otb_option( 'container_width' ) ) . 'px',
				),
			),
		)
	);
}
add_filter( 'wp_theme_json_data_theme', 'otb_filter_theme_json' );

/**
 * Excerpt length from the Customizer.
 *
 * @param int $length Default length.
 * @return int
 */
function otb_excerpt_length( $length ) {
	if ( is_admin() ) {
		return $length;
	}
	return max( 5, absint( otb_option( 'excerpt_length' ) ) );
}
add_filter( 'excerpt_length', 'otb_excerpt_length' );

/**
 * Plain ellipsis for excerpts.
 *
 * @return string
 */
function otb_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'otb_excerpt_more' );

/**
 * Optionally remove the emoji detection script.
 */
function otb_maybe_disable_emojis() {
	if ( ! otb_option( 'disable_emojis' ) ) {
		return;
	}
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'otb_maybe_disable_emojis' );

/**
 * Register the block pattern category used by patterns/*.php.
 */
function otb_register_pattern_category() {
	register_block_pattern_category(
		'overtheball',
		array( 'label' => __( 'Over The Ball', 'overtheball' ) )
	);
}
add_action( 'init', 'otb_register_pattern_category' );

/**
 * First activation: install the bundled logo and site icon, and tune
 * plugin defaults so the theme's palette and fonts take effect.
 */
function otb_after_switch_theme() {
	if ( ! get_theme_mod( 'custom_logo' ) ) {
		$logo_id = otb_sideload_bundled_image( 'logo.png', __( 'Over The Ball Yarns logo', 'overtheball' ) );
		if ( $logo_id ) {
			set_theme_mod( 'custom_logo', $logo_id );
		}
	}

	if ( ! get_option( 'site_icon' ) ) {
		$icon_id = otb_sideload_bundled_image( 'site-icon.png', __( 'Over The Ball Yarns icon', 'overtheball' ) );
		if ( $icon_id ) {
			update_option( 'site_icon', $icon_id );
		}
	}

	// Let the theme's colors and fonts drive Elementor widgets by default.
	if ( false === get_option( 'elementor_disable_color_schemes' ) ) {
		update_option( 'elementor_disable_color_schemes', 'yes' );
	}
	if ( false === get_option( 'elementor_disable_typography_schemes' ) ) {
		update_option( 'elementor_disable_typography_schemes', 'yes' );
	}
}
add_action( 'after_switch_theme', 'otb_after_switch_theme' );

/**
 * Copy an image bundled in assets/images/ into the media library.
 *
 * Re-uses an earlier copy when one exists, so re-activating the theme
 * never creates duplicates.
 *
 * @param string $file  File name inside assets/images/.
 * @param string $title Attachment title.
 * @return int Attachment ID or 0.
 */
function otb_sideload_bundled_image( $file, $title ) {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_otb_bundled', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $file, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}

	$path = OTB_DIR . 'assets/images/' . $file;
	if ( ! file_exists( $path ) ) {
		return 0;
	}

	$id = otb_import_file_to_media( $path, $title );
	if ( $id ) {
		update_post_meta( $id, '_otb_bundled', $file );
	}
	return $id;
}

/**
 * Copy a local file into the uploads folder and register it as an attachment.
 *
 * @param string $path  Absolute file path.
 * @param string $title Attachment title.
 * @return int Attachment ID or 0.
 */
function otb_import_file_to_media( $path, $title = '' ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = wp_tempnam( basename( $path ) );
	if ( ! $tmp || ! copy( $path, $tmp ) ) {
		return 0;
	}

	$file_array = array(
		'name'     => sanitize_file_name( basename( $path ) ),
		'tmp_name' => $tmp,
	);

	$id = media_handle_sideload( $file_array, 0, $title );

	if ( is_wp_error( $id ) ) {
		wp_delete_file( $tmp );
		return 0;
	}

	if ( $title ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $title );
	}

	return (int) $id;
}
