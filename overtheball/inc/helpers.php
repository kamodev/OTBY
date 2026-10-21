<?php
/**
 * Small, dependency-free helpers used throughout the theme.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * Read a theme setting, falling back to the registered default.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function otb_option( $key ) {
	return get_theme_mod( $key, otb_default( $key ) );
}

/**
 * Whether WooCommerce is active.
 *
 * @return bool
 */
function otb_is_woocommerce_active() {
	return class_exists( 'WooCommerce' );
}

/**
 * Whether the current request is any WooCommerce page.
 *
 * @return bool
 */
function otb_is_woocommerce_page() {
	if ( ! otb_is_woocommerce_active() ) {
		return false;
	}
	return is_woocommerce() || is_cart() || is_checkout() || is_account_page();
}

/**
 * Shop page URL (or the home URL when WooCommerce is inactive).
 *
 * @return string
 */
function otb_shop_url() {
	if ( otb_is_woocommerce_active() ) {
		$url = wc_get_page_permalink( 'shop' );
		if ( $url ) {
			return $url;
		}
	}
	return home_url( '/' );
}

/**
 * Convert a hex color to an "r, g, b" triplet string.
 *
 * @param string $hex Hex color.
 * @return string
 */
function otb_hex_to_rgb( $hex ) {
	$rgb = otb_hex_to_rgb_array( $hex );
	return implode( ', ', $rgb );
}

/**
 * Convert a hex color to an array of integer channels.
 *
 * @param string $hex Hex color.
 * @return int[]
 */
function otb_hex_to_rgb_array( $hex ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( ! preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
		return array( 0, 0, 0 );
	}
	return array(
		hexdec( substr( $hex, 0, 2 ) ),
		hexdec( substr( $hex, 2, 2 ) ),
		hexdec( substr( $hex, 4, 2 ) ),
	);
}

/**
 * Mix two hex colors.
 *
 * @param string $color  Base color.
 * @param string $with   Color mixed in.
 * @param float  $amount Share of $with, 0–1.
 * @return string
 */
function otb_mix_colors( $color, $with, $amount ) {
	$a   = otb_hex_to_rgb_array( $color );
	$b   = otb_hex_to_rgb_array( $with );
	$out = '#';
	for ( $i = 0; $i < 3; $i++ ) {
		$out .= str_pad( dechex( (int) round( $a[ $i ] * ( 1 - $amount ) + $b[ $i ] * $amount ) ), 2, '0', STR_PAD_LEFT );
	}
	return strtoupper( $out );
}

/**
 * Relative luminance of a hex color (WCAG definition).
 *
 * @param string $hex Hex color.
 * @return float
 */
function otb_luminance( $hex ) {
	$channels = array_map(
		static function ( $c ) {
			$c = $c / 255;
			return $c <= 0.03928 ? $c / 12.92 : pow( ( $c + 0.055 ) / 1.055, 2.4 );
		},
		otb_hex_to_rgb_array( $hex )
	);
	return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
}

/**
 * Pick black-ish or white text for a background color.
 *
 * @param string $background Hex background.
 * @return string
 */
function otb_contrast_color( $background ) {
	return otb_luminance( $background ) > 0.42 ? '#1B1525' : '#FFFFFF';
}

/**
 * Font catalog. "local" fonts ship with the theme; the rest load from Google Fonts.
 *
 * @return array
 */
function otb_fonts() {
	$sans   = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
	$serif  = 'Georgia, "Times New Roman", Times, serif';
	$script = '"Brush Script MT", "Segoe Script", cursive';
	$full   = 'ital,wght@0,400;0,500;0,600;0,700;1,400';

	return array(
		'body'    => array(
			'Jost'          => array(
				'stack' => $sans,
				'local' => true,
			),
			'Nunito Sans'   => array(
				'stack' => $sans,
				'spec'  => $full,
			),
			'Lato'          => array(
				'stack' => $sans,
				'spec'  => 'ital,wght@0,400;0,700;1,400',
			),
			'Karla'         => array(
				'stack' => $sans,
				'spec'  => $full,
			),
			'Mulish'        => array(
				'stack' => $sans,
				'spec'  => $full,
			),
			'Work Sans'     => array(
				'stack' => $sans,
				'spec'  => $full,
			),
			'Open Sans'     => array(
				'stack' => $sans,
				'spec'  => $full,
			),
			'Source Sans 3' => array(
				'stack' => $sans,
				'spec'  => $full,
			),
			'Raleway'       => array(
				'stack' => $sans,
				'spec'  => $full,
			),
			'Montserrat'    => array(
				'stack' => $sans,
				'spec'  => $full,
			),
			'Inter'         => array(
				'stack' => $sans,
				'spec'  => 'wght@400;500;600;700',
			),
			'Poppins'       => array(
				'stack' => $sans,
				'spec'  => $full,
			),
			'DM Sans'       => array(
				'stack' => $sans,
				'spec'  => $full,
			),
			'Quicksand'     => array(
				'stack' => $sans,
				'spec'  => 'wght@400;500;600;700',
			),
			'System'        => array(
				'stack'  => $sans,
				'system' => true,
			),
		),
		'heading' => array(
			'Cormorant Garamond' => array(
				'stack' => $serif,
				'local' => true,
			),
			'Playfair Display'   => array(
				'stack' => $serif,
				'spec'  => $full,
			),
			'Lora'               => array(
				'stack' => $serif,
				'spec'  => $full,
			),
			'Libre Baskerville'  => array(
				'stack' => $serif,
				'spec'  => 'ital,wght@0,400;0,700;1,400',
			),
			'DM Serif Display'   => array(
				'stack' => $serif,
				'spec'  => 'ital@0;1',
			),
			'Fraunces'           => array(
				'stack' => $serif,
				'spec'  => $full,
			),
			'EB Garamond'        => array(
				'stack' => $serif,
				'spec'  => $full,
			),
			'Marcellus'          => array(
				'stack' => $serif,
				'spec'  => '',
			),
			'Cinzel'             => array(
				'stack' => $serif,
				'spec'  => 'wght@400;500;600;700',
			),
			'Prata'              => array(
				'stack' => $serif,
				'spec'  => '',
			),
			'Jost'               => array(
				'stack' => $sans,
				'local' => true,
			),
			'Josefin Sans'       => array(
				'stack' => $sans,
				'spec'  => $full,
			),
			'Montserrat'         => array(
				'stack' => $sans,
				'spec'  => $full,
			),
			'Poppins'            => array(
				'stack' => $sans,
				'spec'  => $full,
			),
			'System Serif'       => array(
				'stack'  => $serif,
				'system' => true,
			),
		),
		'script'  => array(
			'Allura'              => array(
				'stack' => $script,
				'local' => true,
			),
			'Great Vibes'         => array(
				'stack' => $script,
				'spec'  => '',
			),
			'Corinthia'           => array(
				'stack' => $script,
				'spec'  => '',
			),
			'Parisienne'          => array(
				'stack' => $script,
				'spec'  => '',
			),
			'Italianno'           => array(
				'stack' => $script,
				'spec'  => '',
			),
			'Pinyon Script'       => array(
				'stack' => $script,
				'spec'  => '',
			),
			'Mrs Saint Delafield' => array(
				'stack' => $script,
				'spec'  => '',
			),
			'Alex Brush'          => array(
				'stack' => $script,
				'spec'  => '',
			),
			'Sacramento'          => array(
				'stack' => $script,
				'spec'  => '',
			),
			'Dancing Script'      => array(
				'stack' => $script,
				'spec'  => '',
			),
			'Heading Italic'      => array(
				'stack'  => '',
				'system' => true,
			),
		),
	);
}

/**
 * Choices array (name => name) for a font role.
 *
 * @param string $role body|heading|script.
 * @return array
 */
function otb_font_choices( $role ) {
	$fonts = otb_fonts();
	$names = array_keys( $fonts[ $role ] );
	return array_combine( $names, $names );
}

/**
 * Full CSS font-family value for the chosen font of a role.
 *
 * @param string $role body|heading|script.
 * @return string
 */
function otb_font_stack( $role ) {
	$fonts  = otb_fonts();
	$chosen = otb_option( 'font_' . $role );

	if ( ! isset( $fonts[ $role ][ $chosen ] ) ) {
		$chosen = otb_default( 'font_' . $role );
	}

	$font = $fonts[ $role ][ $chosen ];

	if ( 'script' === $role && 'Heading Italic' === $chosen ) {
		return 'var(--otb-font-heading)';
	}

	if ( ! empty( $font['system'] ) ) {
		return $font['stack'];
	}

	if ( empty( $font['local'] ) && ! otb_option( 'google_fonts' ) ) {
		return $font['stack'];
	}

	return '"' . $chosen . '", ' . $font['stack'];
}

/**
 * Social networks supported by the theme.
 *
 * @return array key => label.
 */
function otb_social_networks() {
	return array(
		'facebook'  => __( 'Facebook', 'overtheball' ),
		'instagram' => __( 'Instagram', 'overtheball' ),
		'pinterest' => __( 'Pinterest', 'overtheball' ),
		'ravelry'   => __( 'Ravelry', 'overtheball' ),
		'youtube'   => __( 'YouTube', 'overtheball' ),
		'tiktok'    => __( 'TikTok', 'overtheball' ),
		'x'         => __( 'X (Twitter)', 'overtheball' ),
		'etsy'      => __( 'Etsy', 'overtheball' ),
	);
}

/**
 * Social links that have a URL set.
 *
 * @return array network => url.
 */
function otb_active_social_links() {
	$links = array();
	foreach ( array_keys( otb_social_networks() ) as $network ) {
		$url = otb_option( 'social_' . $network );
		if ( $url ) {
			$links[ $network ] = $url;
		}
	}
	return $links;
}

/**
 * Craft Yarn Council standard weight categories.
 *
 * @return array slug => [label, cyc, detail].
 */
function otb_yarn_weights() {
	return array(
		'lace'        => array(
			'label'  => __( 'Lace', 'overtheball' ),
			'cyc'    => 0,
			'detail' => __( 'Light fingering, crochet thread', 'overtheball' ),
			'needle' => __( 'US 000–1 · 1.5–2.25 mm', 'overtheball' ),
		),
		'fingering'   => array(
			'label'  => __( 'Fingering', 'overtheball' ),
			'cyc'    => 1,
			'detail' => __( 'Sock, baby, super fine', 'overtheball' ),
			'needle' => __( 'US 1–3 · 2.25–3.25 mm', 'overtheball' ),
		),
		'sport'       => array(
			'label'  => __( 'Sport', 'overtheball' ),
			'cyc'    => 2,
			'detail' => __( 'Fine, baby', 'overtheball' ),
			'needle' => __( 'US 3–5 · 3.25–3.75 mm', 'overtheball' ),
		),
		'dk'          => array(
			'label'  => __( 'DK', 'overtheball' ),
			'cyc'    => 3,
			'detail' => __( 'Light worsted', 'overtheball' ),
			'needle' => __( 'US 5–7 · 3.75–4.5 mm', 'overtheball' ),
		),
		'worsted'     => array(
			'label'  => __( 'Worsted', 'overtheball' ),
			'cyc'    => 4,
			'detail' => __( 'Aran, afghan, medium', 'overtheball' ),
			'needle' => __( 'US 7–9 · 4.5–5.5 mm', 'overtheball' ),
		),
		'bulky'       => array(
			'label'  => __( 'Bulky', 'overtheball' ),
			'cyc'    => 5,
			'detail' => __( 'Chunky, craft, rug', 'overtheball' ),
			'needle' => __( 'US 9–11 · 5.5–8 mm', 'overtheball' ),
		),
		'super-bulky' => array(
			'label'  => __( 'Super Bulky', 'overtheball' ),
			'cyc'    => 6,
			'detail' => __( 'Roving, super chunky', 'overtheball' ),
			'needle' => __( 'US 11–17 · 8–12.75 mm', 'overtheball' ),
		),
		'jumbo'       => array(
			'label'  => __( 'Jumbo', 'overtheball' ),
			'cyc'    => 7,
			'detail' => __( 'Jumbo, roving', 'overtheball' ),
			'needle' => __( 'US 17+ · 12.75 mm+', 'overtheball' ),
		),
	);
}

/**
 * Attachment image URL with a bundled fallback.
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $size          Image size.
 * @param string $fallback      Path relative to assets/images/.
 * @return string
 */
function otb_image_url( $attachment_id, $size = 'full', $fallback = '' ) {
	if ( $attachment_id ) {
		$src = wp_get_attachment_image_url( (int) $attachment_id, $size );
		if ( $src ) {
			return $src;
		}
	}
	return $fallback ? OTB_URI . 'assets/images/' . $fallback : '';
}

/**
 * Replace {year}, {site} and {copy} tokens in footer text.
 *
 * @param string $text Raw text.
 * @return string
 */
function otb_replace_tokens( $text ) {
	return strtr(
		(string) $text,
		array(
			'{year}' => gmdate( 'Y' ),
			'{site}' => get_bloginfo( 'name' ),
			'{copy}' => '&copy;',
		)
	);
}

/**
 * Split a comma separated ID list into unique positive integers.
 *
 * @param string|array $raw IDs.
 * @return int[]
 */
function otb_parse_id_list( $raw ) {
	if ( is_array( $raw ) ) {
		$raw = implode( ',', $raw );
	}
	$ids = array_filter( array_map( 'absint', explode( ',', (string) $raw ) ) );
	return array_values( array_unique( $ids ) );
}

/**
 * Whether a post is being rendered by the Elementor builder.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function otb_is_elementor_page( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( ! $post_id || ! did_action( 'elementor/loaded' ) ) {
		return false;
	}
	return 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
}
