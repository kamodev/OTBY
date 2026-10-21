<?php
/**
 * CSS generated from Customizer settings (CSS custom properties + breakpoint rules).
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * CSS custom property declarations for :root.
 *
 * @return array var => value.
 */
function otb_css_variables() {
	$colors = array(
		'--otb-primary'           => 'color_primary',
		'--otb-primary-dark'      => 'color_primary_dark',
		'--otb-accent'            => 'color_accent',
		'--otb-heading'           => 'color_heading',
		'--otb-text'              => 'color_text',
		'--otb-muted'             => 'color_muted',
		'--otb-bg'                => 'color_bg',
		'--otb-surface'           => 'color_surface',
		'--otb-cream'             => 'color_cream',
		'--otb-border'            => 'color_border',
		'--otb-sale'              => 'color_sale',
		'--otb-announce-bg'       => 'color_announce_bg',
		'--otb-announce-text'     => 'color_announce_text',
		'--otb-topbar-bg'         => 'color_topbar_bg',
		'--otb-topbar-text'       => 'color_topbar_text',
		'--otb-header-bg'         => 'color_header_bg',
		'--otb-header-text'       => 'color_header_text',
		'--otb-footer-bg'         => 'color_footer_bg',
		'--otb-footer-text'       => 'color_footer_text',
		'--otb-footer-heading'    => 'color_footer_heading',
		'--otb-footer-link-hover' => 'color_footer_link_hover',
		'--otb-boxed-bg'          => 'boxed_bg',
	);

	$vars = array();
	foreach ( $colors as $var => $key ) {
		$value        = sanitize_hex_color( otb_option( $key ) );
		$vars[ $var ] = $value ? $value : otb_default( $key );
	}

	$vars['--otb-on-primary'] = otb_contrast_color( $vars['--otb-primary'] );

	$button_radius = array(
		'pill'    => '999px',
		'rounded' => '10px',
		'square'  => '0px',
	);
	$shape         = otb_option( 'button_shape' );

	$vars += array(
		'--otb-font-body'      => otb_font_stack( 'body' ),
		'--otb-font-heading'   => otb_font_stack( 'heading' ),
		'--otb-font-script'    => otb_font_stack( 'script' ),
		'--otb-fs-base'        => absint( otb_option( 'font_size_base' ) ) . 'px',
		'--otb-h1'             => absint( otb_option( 'h1_size' ) ) . 'px',
		'--otb-h2'             => absint( otb_option( 'h2_size' ) ) . 'px',
		'--otb-heading-weight' => absint( otb_option( 'heading_weight' ) ),
		'--otb-nav-spacing'    => round( absint( otb_option( 'nav_letter_spacing' ) ) / 100, 2 ) . 'em',
		'--otb-container'      => absint( otb_option( 'container_width' ) ) . 'px',
		'--otb-radius'         => absint( otb_option( 'radius' ) ) . 'px',
		'--otb-btn-radius'     => isset( $button_radius[ $shape ] ) ? $button_radius[ $shape ] : '999px',
		'--otb-logo-w'         => absint( otb_option( 'logo_width' ) ) . 'px',
		'--otb-logo-w-m'       => absint( otb_option( 'logo_width_mobile' ) ) . 'px',
		'--otb-header-pad'     => absint( otb_option( 'header_padding' ) ) . 'px',
		'--otb-shop-cols'      => max( 1, absint( otb_option( 'shop_columns' ) ) ),
		'--otb-shop-cols-m'    => max( 1, absint( otb_option( 'shop_columns_mobile' ) ) ),
	);

	return apply_filters( 'otb_css_variables', $vars );
}

/**
 * Build the inline stylesheet.
 *
 * @param bool $editor Only output variables (for the block editor).
 * @return string
 */
function otb_dynamic_css( $editor = false ) {
	$declarations = '';
	foreach ( otb_css_variables() as $var => $value ) {
		$declarations .= $var . ':' . $value . ';';
	}

	// Values come from sanitized settings; strip anything that could close the rule.
	$declarations = str_replace( array( '<', '>', '{', '}' ), '', $declarations );

	if ( $editor ) {
		return ':root,.editor-styles-wrapper{' . $declarations . '}';
	}

	$css = ':root{' . $declarations . '}';

	if ( ! otb_option( 'script_accents' ) ) {
		$css .= '.eyebrow{font-family:var(--otb-font-body);font-size:.8rem;letter-spacing:.14em;text-transform:uppercase;font-weight:600;color:var(--otb-primary)}';
	}

	$bp = max( 480, absint( otb_option( 'mobile_breakpoint' ) ) );

	// Desktop/mobile switches and the compact mobile header live behind the configurable breakpoint.
	$css .= '@media (min-width:' . $bp . 'px){.otb-mobile-only{display:none!important}}';
	$css .= '@media (max-width:' . ( $bp - 1 ) . 'px){'
		. '.otb-desktop-only{display:none!important}'
		. '.site-header .site-header__main{grid-template-columns:1fr auto 1fr;grid-template-areas:"left brand right";gap:12px}'
		. '.site-header .site-header__left,.site-header .site-header__right{display:flex}'
		. '.site-header{--otb-logo-current:var(--otb-logo-w-m)}'
		. '.sidebar--drawer{position:fixed;inset:0 auto 0 0;z-index:300;width:min(360px,88vw);background:var(--otb-bg);padding:0 20px 24px;overflow-y:auto;transform:translateX(-105%);transition:transform .3s ease;box-shadow:var(--otb-shadow-lg)}'
		. '.sidebar--drawer.is-open{transform:none}'
		. '.otb-filter-toggle{display:inline-flex}'
		. '.content-wrap--sidebar-left.has-drawer-sidebar,.content-wrap--sidebar-right.has-drawer-sidebar{grid-template-columns:minmax(0,1fr)}'
		. '}';

	return $css;
}
