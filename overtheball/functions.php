<?php
/**
 * Over The Ball theme bootstrap.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

define( 'OTB_VERSION', '1.0.0' );
define( 'OTB_DIR', trailingslashit( get_template_directory() ) );
define( 'OTB_URI', trailingslashit( get_template_directory_uri() ) );

$otb_includes = array(
	'inc/defaults.php',
	'inc/helpers.php',
	'inc/icons.php',
	'inc/setup.php',
	'inc/widgets.php',
	'inc/enqueue.php',
	'inc/nav-menus.php',
	'inc/template-functions.php',
	'inc/template-tags.php',
	'inc/meta-boxes.php',
	'inc/shortcodes.php',
	'inc/customizer/customizer.php',
	'inc/customizer/dynamic-css.php',
	'inc/image-importer.php',
	'inc/admin/theme-page.php',
	'inc/compat/elementor.php',
	'inc/compat/contact-form-7.php',
	'inc/compat/mailpoet.php',
);

foreach ( $otb_includes as $otb_file ) {
	require_once OTB_DIR . $otb_file;
}

if ( class_exists( 'WooCommerce' ) ) {
	require_once OTB_DIR . 'inc/compat/woocommerce.php';
	require_once OTB_DIR . 'inc/compat/woocommerce-yarn.php';
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once OTB_DIR . 'inc/cli.php';
}
