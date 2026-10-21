<?php
/**
 * Navigation menu helpers: submenu toggles, mega menus and fallbacks.
 *
 * Mega menus: add the CSS class "mega-menu" to a top-level menu item
 * (Appearance → Menus → Screen Options → CSS Classes). Its second-level
 * items become column headings and third-level items their links.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add an accessible toggle button after parent items in theme menus.
 *
 * @param string   $item_output Item HTML.
 * @param WP_Post  $item        Menu item.
 * @param int      $depth       Depth.
 * @param stdClass $args        Menu args.
 * @return string
 */
function otb_menu_toggle_buttons( $item_output, $item, $depth, $args ) {
	if ( empty( $args->otb_toggles ) ) {
		return $item_output;
	}
	if ( in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
		$item_output .= sprintf(
			'<button type="button" class="submenu-toggle" aria-expanded="false" aria-label="%1$s">%2$s</button>',
			/* translators: %s: menu item title. */
			esc_attr( sprintf( __( 'Show submenu for %s', 'overtheball' ), wp_strip_all_tags( $item->title ) ) ),
			otb_get_icon( 'chevron-down', array( 'size' => 16 ) )
		);
	}
	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'otb_menu_toggle_buttons', 10, 4 );

/**
 * Mark mega menu parents with a helper class and limit them to the top level.
 *
 * @param string[] $classes Classes.
 * @param WP_Post  $item    Menu item.
 * @param stdClass $args    Menu args.
 * @param int      $depth   Depth.
 * @return string[]
 */
function otb_menu_item_classes( $classes, $item, $args, $depth ) {
	if ( in_array( 'mega-menu', $classes, true ) ) {
		if ( 0 === $depth && isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
			$classes[] = 'otb-mega';
		}
	}
	return $classes;
}
add_filter( 'nav_menu_css_class', 'otb_menu_item_classes', 10, 4 );

/**
 * Render a theme menu location.
 *
 * @param string $location   Theme location.
 * @param array  $args       Extra wp_nav_menu args.
 */
function otb_nav_menu( $location, $args = array() ) {
	$defaults = array(
		'theme_location' => $location,
		'container'      => false,
		'menu_class'     => 'menu menu--' . $location,
		'fallback_cb'    => false,
		'depth'          => 3,
		'otb_toggles'    => true,
	);
	wp_nav_menu( array_merge( $defaults, $args ) );
}

/**
 * Fallback for the primary/mobile menu when none is assigned:
 * Shop (with top product categories) followed by top-level pages.
 *
 * @param array $args Menu args passed by wp_nav_menu.
 */
function otb_primary_menu_fallback( $args = array() ) {
	$menu_class = isset( $args['menu_class'] ) ? $args['menu_class'] : 'menu';
	$exclude    = array();
	$items      = '';

	if ( otb_is_woocommerce_active() ) {
		foreach ( array( 'shop', 'cart', 'checkout', 'myaccount' ) as $page ) {
			$exclude[] = wc_get_page_id( $page );
		}

		$cats = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'parent'     => 0,
				'hide_empty' => true,
				'number'     => 8,
				'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
			)
		);

		$children = '';
		if ( ! is_wp_error( $cats ) ) {
			foreach ( $cats as $cat ) {
				$children .= sprintf( '<li class="menu-item"><a href="%s">%s</a></li>', esc_url( get_term_link( $cat ) ), esc_html( $cat->name ) );
			}
		}

		$items .= sprintf(
			'<li class="menu-item%1$s"><a href="%2$s">%3$s</a>%4$s</li>',
			$children ? ' menu-item-has-children' : '',
			esc_url( otb_shop_url() ),
			esc_html__( 'Shop', 'overtheball' ),
			$children ? '<button type="button" class="submenu-toggle" aria-expanded="false" aria-label="' . esc_attr__( 'Show submenu for Shop', 'overtheball' ) . '">' . otb_get_icon( 'chevron-down', array( 'size' => 16 ) ) . '</button><ul class="sub-menu">' . $children . '</ul>' : ''
		);
	}

	$pages = get_pages(
		array(
			'parent'      => 0,
			'sort_column' => 'menu_order,post_title',
			'exclude'     => array_filter( array_merge( $exclude, array( (int) get_option( 'page_on_front' ) ) ) ),
			'number'      => 6,
		)
	);

	foreach ( $pages as $page ) {
		$items .= sprintf( '<li class="menu-item"><a href="%s">%s</a></li>', esc_url( get_permalink( $page ) ), esc_html( get_the_title( $page ) ) );
	}

	if ( $items ) {
		printf( '<ul class="%s">%s</ul>', esc_attr( $menu_class ), $items ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	}
}
