<?php
/**
 * Inline SVG icon set.
 *
 * UI icons are 24×24 stroke icons in the spirit of Feather (MIT); social
 * icons are simplified filled marks. Everything inherits currentColor.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * Inner SVG markup for every icon.
 *
 * @return array name => [ 'type' => stroke|fill, 'svg' => inner markup ].
 */
function otb_icon_library() {
	return array(
		// Interface.
		'search'        => array( 'stroke', '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/>' ),
		'user'          => array( 'stroke', '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/>' ),
		'bag'           => array( 'stroke', '<path d="M5 8h14l-1 13H6L5 8Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>' ),
		'menu'          => array( 'stroke', '<path d="M3 6h18M3 12h18M3 18h18"/>' ),
		'close'         => array( 'stroke', '<path d="M18 6 6 18M6 6l12 12"/>' ),
		'chevron-down'  => array( 'stroke', '<path d="m6 9 6 6 6-6"/>' ),
		'chevron-left'  => array( 'stroke', '<path d="m15 18-6-6 6-6"/>' ),
		'chevron-right' => array( 'stroke', '<path d="m9 18 6-6-6-6"/>' ),
		'arrow-right'   => array( 'stroke', '<path d="M5 12h14M13 6l6 6-6 6"/>' ),
		'arrow-up'      => array( 'stroke', '<path d="M12 19V5M6 11l6-6 6 6"/>' ),
		'filter'        => array( 'stroke', '<path d="M4 6h16M7 12h10M10 18h4"/>' ),
		'plus'          => array( 'stroke', '<path d="M12 5v14M5 12h14"/>' ),
		'minus'         => array( 'stroke', '<path d="M5 12h14"/>' ),
		'check'         => array( 'stroke', '<path d="m5 12 5 5 9-10"/>' ),
		'info'          => array( 'stroke', '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/>' ),
		'share'         => array( 'stroke', '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/>' ),
		'grid'          => array( 'stroke', '<rect x="4" y="4" width="7" height="7" rx="1"/><rect x="13" y="4" width="7" height="7" rx="1"/><rect x="4" y="13" width="7" height="7" rx="1"/><rect x="13" y="13" width="7" height="7" rx="1"/>' ),
		'calculator'    => array( 'stroke', '<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M8 7h8M8 12h.01M12 12h.01M16 12h.01M8 16h.01M12 16h.01M16 16h.01"/>' ),
		'quote'         => array( 'stroke', '<path d="M10 7H6a2 2 0 0 0-2 2v4h4v4H5M20 7h-4a2 2 0 0 0-2 2v4h4v4h-3"/>' ),

		// Shop & craft.
		'truck'         => array( 'stroke', '<path d="M3 6h11v10H3zM14 9h4l3 3v4h-7"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>' ),
		'yarn'          => array( 'stroke', '<circle cx="11" cy="12" r="8"/><path d="M5.2 6.6c4 1.4 7.6 5.2 9.6 11.2M3.4 10.4c4.4.4 8.4 3.4 9.8 9.4M8.6 4.4c3.4 2 6.4 6 7.4 11.6"/><path d="M19 12.6c1.8 1.6 2.4 4.2 1.6 6.6"/>' ),
		'needles'       => array( 'stroke', '<path d="M6 18 19 5M6 6l13 13"/><circle cx="4.5" cy="19.5" r="1.5"/><circle cx="4.5" cy="4.5" r="1.5"/>' ),
		'hook'          => array( 'stroke', '<path d="M4 20 16.5 7.5"/><path d="M16.5 7.5c-.8-1.6-.3-3.6 1.4-4.3 1.6-.6 3.2.4 3.1 2-.1 1.2-1.3 1.6-2 .8"/>' ),
		'scissors'      => array( 'stroke', '<circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M8.1 8.1 20 20M8.1 15.9 20 4"/>' ),
		'lock'          => array( 'stroke', '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>' ),
		'heart'         => array( 'stroke', '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1.1L12 21l7.8-7.5 1-1.1a5.5 5.5 0 0 0 0-7.8Z"/>' ),
		'gift'          => array( 'stroke', '<rect x="3" y="8" width="18" height="4" rx="1"/><path d="M5 12v9h14v-9M12 8v13M12 8S10.5 3 8 4s0 4 4 4ZM12 8s1.5-5 4-4 0 4-4 4Z"/>' ),
		'leaf'          => array( 'stroke', '<path d="M5 19C5 10 10 5 20 4c-1 10-6 15-15 15Z"/><path d="M5 19 13 11"/>' ),
		'star'          => array( 'stroke', '<path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1-4.4-4.3 6.1-.9Z"/>' ),
		'sparkle'       => array( 'stroke', '<path d="M12 3c.6 4.6 2.4 6.4 7 7-4.6.6-6.4 2.4-7 7-.6-4.6-2.4-6.4-7-7 4.6-.6 6.4-2.4 7-7Z"/><path d="M19 16v4M17 18h4"/>' ),
		'package'       => array( 'stroke', '<path d="M21 8 12 3 3 8v8l9 5 9-5V8Z"/><path d="m3 8 9 5 9-5M12 13v8"/>' ),
		'returns'       => array( 'stroke', '<path d="M3 12a9 9 0 0 1 15.5-6.2L21 8M21 3v5h-5M21 12a9 9 0 0 1-15.5 6.2L3 16M3 21v-5h5"/>' ),
		'shield'        => array( 'stroke', '<path d="M12 3 4 6v6c0 5 3.4 8.4 8 9 4.6-.6 8-4 8-9V6l-8-3Z"/><path d="m9 12 2 2 4-4"/>' ),
		'award'         => array( 'stroke', '<circle cx="12" cy="9" r="6"/><path d="m8.5 14-1.5 7 5-3 5 3-1.5-7"/>' ),
		'palette'       => array( 'stroke', '<path d="M12 3a9 9 0 0 0 0 18c1.1 0 1.7-.9 1.4-1.8-.4-1.2.4-2.2 1.6-2.2H17a4 4 0 0 0 4-4c0-5.5-4-10-9-10Z"/><circle cx="7.5" cy="11" r="1"/><circle cx="10" cy="7" r="1"/><circle cx="15" cy="7.5" r="1"/>' ),

		// Contact.
		'phone'         => array( 'stroke', '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>' ),
		'mail'          => array( 'stroke', '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>' ),
		'map-pin'       => array( 'stroke', '<path d="M12 21s-7-6.2-7-12a7 7 0 0 1 14 0c0 5.8-7 12-7 12Z"/><circle cx="12" cy="9" r="2.5"/>' ),
		'clock'         => array( 'stroke', '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>' ),

		// Social.
		'facebook'      => array( 'fill', '<path d="M14 8.5V6.8c0-.8.5-1.3 1.3-1.3H17V2.2h-2.6C11.7 2.2 10.4 4 10.4 6.4v2.1H8v3.3h2.4V22H14V11.8h2.6l.4-3.3Z"/>' ),
		'instagram'     => array( 'fill', '<path fill-rule="evenodd" d="M8 2h8a6 6 0 0 1 6 6v8a6 6 0 0 1-6 6H8a6 6 0 0 1-6-6V8a6 6 0 0 1 6-6Zm0 2a4 4 0 0 0-4 4v8a4 4 0 0 0 4 4h8a4 4 0 0 0 4-4V8a4 4 0 0 0-4-4H8Zm4 3a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm5.4-3.6a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4Z"/>' ),
		'pinterest'     => array( 'fill', '<path d="M12 2a10 10 0 0 0-3.6 19.3c-.1-.8-.2-2 0-2.9l1.2-5s-.3-.6-.3-1.5c0-1.4.8-2.5 1.8-2.5.9 0 1.3.7 1.3 1.5 0 .9-.6 2.2-.9 3.4-.2 1 .5 1.9 1.6 1.9 1.9 0 3.3-2 3.3-4.9 0-2.6-1.8-4.3-4.5-4.3-3 0-4.8 2.3-4.8 4.6 0 .9.3 1.9.8 2.4.1.1.1.2.1.3l-.3 1.2c0 .2-.2.2-.4.1-1.4-.6-2.2-2.6-2.2-4.2 0-3.4 2.5-6.6 7.2-6.6 3.8 0 6.7 2.7 6.7 6.3 0 3.7-2.3 6.7-5.6 6.7-1.1 0-2.1-.6-2.5-1.2l-.7 2.6c-.2 1-.9 2.2-1.4 2.9A10 10 0 1 0 12 2Z"/>' ),
		'ravelry'       => array( 'fill', '<path fill-rule="evenodd" d="M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20Zm0 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16Z"/><path d="M9 8h2v1.4c.6-1 1.6-1.6 3-1.6h.6V10H14c-1.8 0-3 1-3 3v4H9V8Z"/>' ),
		'youtube'       => array( 'fill', '<path d="M21.6 7.2a2.6 2.6 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4a2.6 2.6 0 0 0-1.8 1.8C2 8.8 2 12 2 12s0 3.2.4 4.8a2.6 2.6 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.6 2.6 0 0 0 1.8-1.8c.4-1.6.4-4.8.4-4.8s0-3.2-.4-4.8ZM10 15V9l5.2 3L10 15Z"/>' ),
		'tiktok'        => array( 'fill', '<path d="M16.6 2h-3.3v13.4a2.9 2.9 0 1 1-2.9-2.9c.3 0 .6 0 .8.1V9.2a6.3 6.3 0 1 0 5.4 6.2V8.6a8 8 0 0 0 4.4 1.4V6.7c-2.5 0-4.4-2-4.4-4.7Z"/>' ),
		'x'             => array( 'fill', '<path d="M17.8 3h3.1l-6.8 7.7L22 21h-6.2l-4.9-6.4L5.3 21H2.2l7.3-8.3L2 3h6.4l4.4 5.8L17.8 3Zm-1.1 16.2h1.7L7.4 4.7H5.6l11.1 14.5Z"/>' ),
		'etsy'          => array( 'fill', '<path fill-rule="evenodd" d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm3.5 3.5v11h7.3l.5-2.6h-.7c-.4 1-.9 1.4-2.3 1.4h-1.8c-.6 0-.8-.3-.8-.8v-3.7h2c.9 0 1.1.3 1.3 1h.6V9.9h-.6c-.2.8-.4 1-1.3 1h-2V7.6h2.2c1.3 0 1.6.4 2 1.6h.6l-.1-2.7H8.5Z"/>' ),
	);
}

/**
 * Return an inline SVG icon.
 *
 * @param string $name  Icon name.
 * @param array  $args  Optional. 'class', 'size', 'title'.
 * @return string
 */
function otb_get_icon( $name, $args = array() ) {
	$icons = otb_icon_library();
	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	$args = wp_parse_args(
		$args,
		array(
			'class' => '',
			'size'  => 20,
			'title' => '',
		)
	);

	list( $type, $inner ) = $icons[ $name ];

	$paint = 'fill' === $type
		? 'fill="currentColor"'
		: 'fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"';

	$a11y = $args['title']
		? 'role="img" aria-label="' . esc_attr( $args['title'] ) . '"'
		: 'aria-hidden="true" focusable="false"';

	return sprintf(
		'<svg class="otb-icon otb-icon--%1$s %2$s" width="%3$d" height="%3$d" viewBox="0 0 24 24" %4$s %5$s>%6$s</svg>',
		esc_attr( $name ),
		esc_attr( $args['class'] ),
		(int) $args['size'],
		$paint,
		$a11y,
		$inner
	);
}

/**
 * Echo an inline SVG icon. Markup comes only from the static library above.
 *
 * @param string $name Icon name.
 * @param array  $args Optional args.
 */
function otb_icon( $name, $args = array() ) {
	echo otb_get_icon( $name, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static, trusted SVG.
}

/**
 * Icon choices for Customizer selects (feature icons).
 *
 * @return array
 */
function otb_icon_choices() {
	$skip    = array_merge( array_keys( otb_social_networks() ), array( 'menu', 'close', 'chevron-down', 'chevron-left', 'chevron-right', 'arrow-up', 'minus', 'plus', 'filter' ) );
	$choices = array();
	foreach ( array_keys( otb_icon_library() ) as $name ) {
		if ( ! in_array( $name, $skip, true ) ) {
			$choices[ $name ] = ucwords( str_replace( '-', ' ', $name ) );
		}
	}
	return $choices;
}
