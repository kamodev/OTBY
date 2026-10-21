<?php
/**
 * Styles, scripts and font loading.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fonts in use, split into bundled (local) and Google families.
 *
 * @return array{local: string[], google: string[]}
 */
function otb_fonts_in_use() {
	$fonts = otb_fonts();
	$used  = array(
		'local'  => array(),
		'google' => array(),
	);

	foreach ( array( 'body', 'heading', 'script' ) as $role ) {
		$name = otb_option( 'font_' . $role );
		if ( ! isset( $fonts[ $role ][ $name ] ) ) {
			$name = otb_default( 'font_' . $role );
		}
		$font = $fonts[ $role ][ $name ];

		if ( ! empty( $font['system'] ) ) {
			continue;
		}
		if ( ! empty( $font['local'] ) ) {
			$used['local'][] = $name;
		} elseif ( otb_option( 'google_fonts' ) ) {
			$family                  = str_replace( ' ', '+', $name );
			$used['google'][ $name ] = $font['spec'] ? $family . ':' . $font['spec'] : $family;
		}
	}

	$used['local'] = array_values( array_unique( $used['local'] ) );
	return $used;
}

/**
 * Google Fonts stylesheet URL, or '' when nothing needs to load remotely.
 *
 * @return string
 */
function otb_google_fonts_url() {
	$used = otb_fonts_in_use();
	if ( empty( $used['google'] ) ) {
		return '';
	}
	// Google expects repeated "family" params, which add_query_arg cannot express.
	return 'https://fonts.googleapis.com/css2?family=' . implode( '&family=', array_values( $used['google'] ) ) . '&display=swap';
}

/**
 * Front-end assets.
 */
function otb_enqueue_assets() {
	$used = otb_fonts_in_use();
	$deps = array();

	if ( $used['local'] ) {
		wp_enqueue_style( 'otb-fonts', OTB_URI . 'assets/fonts/fonts.css', array(), OTB_VERSION );
		$deps[] = 'otb-fonts';
	}

	$google = otb_google_fonts_url();
	if ( $google ) {
		wp_enqueue_style( 'otb-google-fonts', $google, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		$deps[] = 'otb-google-fonts';
	}

	wp_enqueue_style( 'otb-main', OTB_URI . 'assets/css/main.css', $deps, OTB_VERSION );
	wp_add_inline_style( 'otb-main', otb_dynamic_css() );

	if ( otb_is_woocommerce_active() ) {
		wp_enqueue_style( 'otb-woocommerce', OTB_URI . 'assets/css/woocommerce.css', array( 'otb-main' ), OTB_VERSION );
	}

	if ( defined( 'WPCF7_VERSION' ) || defined( 'MAILPOET_VERSION' ) || did_action( 'elementor/loaded' ) ) {
		wp_enqueue_style( 'otb-plugins', OTB_URI . 'assets/css/plugins.css', array( 'otb-main' ), OTB_VERSION );
	}

	wp_enqueue_script(
		'otb-main',
		OTB_URI . 'assets/js/main.js',
		array(),
		OTB_VERSION,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
	wp_localize_script(
		'otb-main',
		'otbSettings',
		array(
			'breakpoint'    => absint( otb_option( 'mobile_breakpoint' ) ),
			'announceSpeed' => absint( otb_option( 'announce_speed' ) ),
			'heroAutoplay'  => absint( otb_option( 'hero_autoplay' ) ),
			'stickyHeader'  => (bool) otb_option( 'header_sticky' ),
			'shrinkHeader'  => (bool) otb_option( 'header_shrink' ),
			'animations'    => (bool) otb_option( 'animations' ),
			'cartBehavior'  => otb_option( 'cart_behavior' ),
			'i18n'          => array(
				'expand'   => __( 'Expand submenu', 'overtheball' ),
				'collapse' => __( 'Collapse submenu', 'overtheball' ),
				/* translators: %s: number of skeins. */
				'skeins'   => __( 'You will need about %s skeins.', 'overtheball' ),
				'skein'    => __( 'You will need about 1 skein.', 'overtheball' ),
				'qtyMinus' => __( 'Decrease quantity', 'overtheball' ),
				'qtyPlus'  => __( 'Increase quantity', 'overtheball' ),
				'close'    => __( 'Close', 'overtheball' ),
			),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'otb_enqueue_assets' );

/**
 * Flag JS support early so reveal animations never hide content without JS.
 */
function otb_js_class() {
	wp_print_inline_script_tag( "document.documentElement.classList.add('otb-js');" );
}
add_action( 'wp_head', 'otb_js_class', 1 );

/**
 * Preconnect to Google Fonts and preload the bundled latin font files.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation type.
 * @return array
 */
function otb_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type && otb_google_fonts_url() ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'otb_resource_hints', 10, 2 );

/**
 * Preload the latin subset of each bundled font in use.
 */
function otb_preload_fonts() {
	if ( ! otb_option( 'preload_fonts' ) ) {
		return;
	}
	$files = array(
		'Jost'               => 'jost-latin.woff2',
		'Cormorant Garamond' => 'cormorant-garamond-latin.woff2',
		'Allura'             => 'allura-latin.woff2',
	);
	foreach ( otb_fonts_in_use()['local'] as $name ) {
		if ( isset( $files[ $name ] ) ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( OTB_URI . 'assets/fonts/' . $files[ $name ] )
			);
		}
	}
}
add_action( 'wp_head', 'otb_preload_fonts', 2 );

/**
 * Block editor: load the selected fonts and the dynamic variables.
 */
function otb_block_editor_assets() {
	$google = otb_google_fonts_url();
	if ( $google ) {
		wp_enqueue_style( 'otb-editor-google-fonts', $google, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}
	wp_register_style( 'otb-editor-vars', false, array(), OTB_VERSION );
	wp_enqueue_style( 'otb-editor-vars' );
	wp_add_inline_style( 'otb-editor-vars', otb_dynamic_css( true ) );
}
add_action( 'enqueue_block_editor_assets', 'otb_block_editor_assets' );

/**
 * Pass the dynamic variables into the editor iframe as well.
 *
 * @param array $settings Editor settings.
 * @return array
 */
function otb_editor_settings_styles( $settings ) {
	if ( ! isset( $settings['styles'] ) || ! is_array( $settings['styles'] ) ) {
		$settings['styles'] = array();
	}
	$settings['styles'][] = array( 'css' => otb_dynamic_css( true ) );
	return $settings;
}
add_filter( 'block_editor_settings_all', 'otb_editor_settings_styles' );
