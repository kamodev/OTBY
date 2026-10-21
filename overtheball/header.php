<?php
/**
 * Site header.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'overtheball' ); ?></a>

	<?php
	if ( otb_show_header() ) {
		if ( function_exists( 'hfe_header_enabled' ) && hfe_header_enabled() ) {
			hfe_render_header();
		} elseif ( ! otb_elementor_location( 'header' ) ) {
			get_template_part( 'template-parts/header/site-header' );
		}
	}
	?>

	<?php // Not "content": WooCommerce ships legacy "#content" selectors that would override the theme layout. ?>
	<div id="site-content" class="site-content">
