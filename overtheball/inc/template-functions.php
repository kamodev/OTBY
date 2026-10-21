<?php
/**
 * Layout logic: body classes, sidebar resolution, per-page overrides.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * ID of the post whose per-page settings apply to the current request.
 *
 * @return int
 */
function otb_context_post_id() {
	if ( otb_is_woocommerce_active() && is_shop() ) {
		return (int) wc_get_page_id( 'shop' );
	}
	if ( is_home() && ! is_front_page() ) {
		return (int) get_option( 'page_for_posts' );
	}
	if ( is_singular() ) {
		return (int) get_queried_object_id();
	}
	return 0;
}

/**
 * Per-page setting from the "Page Layout" meta box.
 *
 * @param string $key Meta key without the _otb_ prefix.
 * @return string
 */
function otb_page_meta( $key ) {
	$post_id = otb_context_post_id();
	if ( ! $post_id ) {
		return '';
	}
	return (string) get_post_meta( $post_id, '_otb_' . $key, true );
}

/**
 * Sidebar position and widget area for the current request.
 *
 * @return array{position: string, sidebar: string}
 */
function otb_get_layout() {
	static $layout = null;
	if ( null !== $layout ) {
		return $layout;
	}

	$position = 'none';
	$sidebar  = '';

	if ( otb_is_woocommerce_active() && ( is_cart() || is_checkout() || is_account_page() ) ) {
		$position = 'none';
	} elseif ( otb_is_woocommerce_active() && is_product() ) {
		$position = otb_option( 'sidebar_product' );
		$sidebar  = 'sidebar-product';
	} elseif ( otb_is_woocommerce_active() && is_woocommerce() ) {
		$position = otb_option( 'sidebar_shop' );
		$sidebar  = 'sidebar-shop';
	} elseif ( is_search() ) {
		$position = otb_option( 'sidebar_search' );
		$sidebar  = 'sidebar-blog';
	} elseif ( is_singular( 'post' ) ) {
		$position = otb_option( 'sidebar_single' );
		$sidebar  = 'sidebar-blog';
	} elseif ( is_page() || is_singular() ) {
		$position = otb_option( 'sidebar_page' );
		$sidebar  = 'sidebar-page';
	} elseif ( is_home() || is_archive() ) {
		$position = otb_option( 'sidebar_blog' );
		$sidebar  = 'sidebar-blog';
	}

	$override = otb_page_meta( 'sidebar' );
	if ( in_array( $override, array( 'left', 'right', 'none' ), true ) ) {
		$position = $override;
	}

	if ( is_page_template( array( 'page-templates/template-full-width.php', 'page-templates/template-canvas.php', 'page-templates/template-contact.php' ) ) ) {
		$position = 'none';
	}

	if ( 'none' === $position || ! $sidebar || ! is_active_sidebar( $sidebar ) ) {
		$position = 'none';
		$sidebar  = '';
	}

	$layout = array(
		'position' => $position,
		'sidebar'  => $sidebar,
	);

	return $layout;
}

/**
 * Whether the header should be rendered.
 *
 * @return bool
 */
function otb_show_header() {
	if ( is_page_template( 'page-templates/template-canvas.php' ) ) {
		return false;
	}
	return '1' !== otb_page_meta( 'hide_header' );
}

/**
 * Whether the footer should be rendered.
 *
 * @return bool
 */
function otb_show_footer() {
	if ( is_page_template( 'page-templates/template-canvas.php' ) ) {
		return false;
	}
	return '1' !== otb_page_meta( 'hide_footer' );
}

/**
 * Whether the header floats transparently over the first section.
 *
 * @return bool
 */
function otb_is_transparent_header() {
	$meta = otb_page_meta( 'transparent_header' );
	if ( 'on' === $meta ) {
		return true;
	}
	if ( 'off' === $meta ) {
		return false;
	}
	// Light header text needs a photo behind it, so the first slide must have one.
	return is_front_page() && otb_option( 'header_transparent_home' ) && otb_home_sections_active() && otb_section_enabled( 'hero' ) && absint( otb_option( 'hero_1_image' ) );
}

/**
 * Whether the title/breadcrumb banner shows on the current request.
 *
 * @return bool
 */
function otb_show_page_header() {
	if ( is_front_page() || ! otb_option( 'page_header_enable' ) ) {
		return false;
	}
	if ( '1' === otb_page_meta( 'hide_title' ) || otb_elementor_hides_title( otb_context_post_id() ) ) {
		return false;
	}
	if ( is_page_template( array( 'page-templates/template-canvas.php' ) ) ) {
		return false;
	}
	if ( otb_is_woocommerce_active() && is_product() ) {
		return false;
	}
	return true;
}

/**
 * Whether the content area should be full-bleed (no container), e.g. for Elementor.
 *
 * @return bool
 */
function otb_is_full_bleed() {
	if ( 'full' === otb_page_meta( 'content_width' ) ) {
		return true;
	}
	return is_page_template( array( 'page-templates/template-canvas.php' ) );
}

/**
 * Body classes reflecting layout options.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function otb_body_classes( $classes ) {
	$layout = otb_get_layout();

	$classes[] = 'otb-header-' . sanitize_html_class( otb_option( 'header_layout' ) );
	$classes[] = 'otb-sidebar-' . $layout['position'];
	$classes[] = 'otb-btn-' . sanitize_html_class( otb_option( 'button_shape' ) );

	if ( 'boxed' === otb_option( 'site_layout' ) ) {
		$classes[] = 'otb-boxed';
	}
	if ( otb_option( 'header_sticky' ) ) {
		$classes[] = 'otb-sticky-header';
	}
	if ( otb_is_transparent_header() ) {
		$classes[] = 'otb-transparent-header';
	}
	if ( ! otb_option( 'animations' ) ) {
		$classes[] = 'otb-no-animations';
	}
	if ( otb_option( 'button_uppercase' ) ) {
		$classes[] = 'otb-btn-upper';
	}
	if ( otb_option( 'nav_uppercase' ) ) {
		$classes[] = 'otb-nav-upper';
	}
	if ( otb_is_full_bleed() ) {
		$classes[] = 'otb-full-bleed';
	}
	if ( ! otb_show_page_header() ) {
		$classes[] = 'otb-no-page-header';
	}
	if ( otb_option( 'mailpoet_theme_styles' ) ) {
		$classes[] = 'otb-mailpoet-styled';
	}
	if ( otb_is_woocommerce_active() ) {
		$classes[] = 'otb-card-btn-' . sanitize_html_class( otb_option( 'card_button' ) );
		if ( otb_option( 'catalog_mode' ) ) {
			$classes[] = 'otb-catalog-mode';
		}
		if ( is_checkout() && ! is_order_received_page() && otb_option( 'checkout_minimal' ) ) {
			$classes[] = 'otb-checkout-minimal';
		}
	}
	if ( is_singular() && ! is_front_page() && has_post_thumbnail() ) {
		$classes[] = 'has-featured-image';
	}

	return $classes;
}
add_filter( 'body_class', 'otb_body_classes' );

/**
 * Pingback discovery header on singular content.
 */
function otb_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'otb_pingback_header' );

/**
 * Wrap archive title prefixes ("Category:") so they can be styled as an eyebrow.
 *
 * @param string $prefix Prefix.
 * @return string
 */
function otb_archive_title_prefix( $prefix ) {
	return $prefix ? '<span class="archive-title-prefix">' . $prefix . '</span>' : $prefix;
}
add_filter( 'get_the_archive_title_prefix', 'otb_archive_title_prefix' );

/**
 * Whether theme homepage sections render on the front page.
 *
 * @return bool
 */
function otb_home_sections_active() {
	if ( ! otb_option( 'home_sections' ) ) {
		return false;
	}
	if ( 'page' === get_option( 'show_on_front' ) && otb_is_elementor_page( (int) get_option( 'page_on_front' ) ) ) {
		return false;
	}
	return true;
}

/**
 * Ordered list of homepage section keys.
 *
 * @return string[]
 */
function otb_home_section_order() {
	$valid = array_keys( otb_home_section_labels() );
	$order = array_filter( array_map( 'trim', explode( ',', (string) otb_option( 'home_order' ) ) ) );
	$order = array_values( array_intersect( $order, $valid ) );
	// Append sections missing from a saved order (e.g. added in a theme update).
	return array_values( array_unique( array_merge( $order, $valid ) ) );
}

/**
 * Homepage section labels.
 *
 * @return array
 */
function otb_home_section_labels() {
	return array(
		'hero'         => __( 'Hero slider', 'overtheball' ),
		'features'     => __( 'Feature highlights', 'overtheball' ),
		'categories'   => __( 'Shop by category', 'overtheball' ),
		'products'     => __( 'Products', 'overtheball' ),
		'weights'      => __( 'Shop by weight', 'overtheball' ),
		'promo'        => __( 'Promo banner', 'overtheball' ),
		'about'        => __( 'About / story', 'overtheball' ),
		'testimonials' => __( 'Testimonials', 'overtheball' ),
		'blog'         => __( 'Latest posts', 'overtheball' ),
		'gallery'      => __( 'Picture gallery', 'overtheball' ),
		'newsletter'   => __( 'Newsletter signup', 'overtheball' ),
		'content'      => __( 'Page content', 'overtheball' ),
	);
}

/**
 * Whether a homepage section is switched on.
 *
 * @param string $section Section key.
 * @return bool
 */
function otb_section_enabled( $section ) {
	if ( 'content' === $section ) {
		return true;
	}
	$key = 'newsletter' === $section ? 'home_newsletter_enable' : $section . '_enable';
	return (bool) otb_option( $key );
}

/**
 * Whether the footer newsletter strip should render on this request.
 *
 * @return bool
 */
function otb_show_newsletter_strip() {
	if ( ! otb_option( 'newsletter_enable' ) ) {
		return false;
	}
	if ( otb_is_woocommerce_active() && ( is_cart() || is_checkout() ) ) {
		return false;
	}
	// The homepage already has its own newsletter section.
	if ( is_front_page() && otb_home_sections_active() && otb_section_enabled( 'newsletter' ) ) {
		return false;
	}
	return true;
}

/**
 * Open the content wrapper (container + main) for standard templates.
 *
 * @param string $extra_class Extra class for <main>.
 */
function otb_content_open( $extra_class = '' ) {
	$layout = otb_get_layout();
	$wrap   = otb_is_full_bleed() ? 'content-wrap content-wrap--full' : 'container content-wrap';
	if ( 'sidebar-shop' === $layout['sidebar'] && otb_option( 'shop_filter_drawer' ) ) {
		$wrap .= ' has-drawer-sidebar';
	}
	printf(
		'<div class="%1$s content-wrap--sidebar-%2$s"><main id="primary" class="site-main %3$s">',
		esc_attr( $wrap ),
		esc_attr( $layout['position'] ),
		esc_attr( $extra_class )
	);
}

/**
 * Close the content wrapper and print the sidebar when one applies.
 */
function otb_content_close() {
	echo '</main>';
	get_sidebar();
	echo '</div>';
}
