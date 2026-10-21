<?php
/**
 * WooCommerce integration: layout, product cards, header cart, mini-cart
 * drawer, free shipping progress, sticky add to cart, catalog mode.
 *
 * Uses hooks only (no template overrides) so WooCommerce updates never
 * leave outdated theme templates behind.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * Declare WooCommerce support and gallery features.
 */
function otb_woocommerce_setup() {
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 600,
			'single_image_width'    => 900,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'default_columns' => 4,
				'min_columns'     => 1,
				'max_columns'     => 6,
			),
		)
	);
	if ( otb_option( 'gallery_zoom' ) ) {
		add_theme_support( 'wc-product-gallery-zoom' );
	}
	if ( otb_option( 'gallery_lightbox' ) ) {
		add_theme_support( 'wc-product-gallery-lightbox' );
	}
	if ( otb_option( 'gallery_slider' ) ) {
		add_theme_support( 'wc-product-gallery-slider' );
	}
}
add_action( 'after_setup_theme', 'otb_woocommerce_setup' );

/**
 * Re-wire WooCommerce template hooks once settings are available.
 */
function otb_woocommerce_hooks() {
	// The theme renders its own wrappers, title banner, breadcrumbs and sidebar.
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

	// Product card.
	add_action( 'woocommerce_before_shop_loop_item_title', 'otb_loop_media_open', 1 );
	add_action( 'woocommerce_before_shop_loop_item_title', 'otb_loop_hover_image', 11 );
	add_action( 'woocommerce_before_shop_loop_item_title', 'otb_loop_badges', 12 );
	add_action( 'woocommerce_before_shop_loop_item_title', 'otb_loop_media_close', 30 );
	add_action( 'woocommerce_shop_loop_item_title', 'otb_loop_category', 5 );

	if ( ! otb_option( 'card_show_rating' ) ) {
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	}
	if ( 'none' === otb_option( 'card_button' ) ) {
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	}

	// Shop toolbar.
	add_action( 'woocommerce_before_shop_loop', 'otb_shop_toolbar_open', 15 );
	add_action( 'woocommerce_before_shop_loop', 'otb_shop_toolbar_close', 35 );

	// Related products.
	if ( ! absint( otb_option( 'related_count' ) ) ) {
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
	}

	// Free shipping progress.
	add_action( 'woocommerce_before_mini_cart', 'otb_print_free_shipping_bar' );
	add_action( 'woocommerce_before_cart_table', 'otb_print_free_shipping_bar' );

	// Catalog mode.
	if ( otb_option( 'catalog_mode' ) ) {
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		add_filter( 'woocommerce_is_purchasable', '__return_false' );
	}
}
// "init" rather than "wp" so admin-ajax renders (e.g. Elementor product widgets) get the theme cards too.
add_action( 'init', 'otb_woocommerce_hooks' );

/**
 * Hide WooCommerce's own title/description when the theme banner shows them.
 *
 * @param bool $show Show title.
 * @return bool
 */
function otb_wc_show_page_title( $show ) {
	return otb_show_page_header() ? false : $show;
}
add_filter( 'woocommerce_show_page_title', 'otb_wc_show_page_title' );

/**
 * Drop the duplicate category description under the theme banner.
 */
function otb_wc_archive_description() {
	if ( otb_show_page_header() ) {
		remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
	}
}
add_action( 'wp', 'otb_wc_archive_description' );

/**
 * Products per row.
 *
 * @return int
 */
function otb_loop_columns() {
	return max( 1, min( 6, absint( otb_option( 'shop_columns' ) ) ) );
}
add_filter( 'loop_shop_columns', 'otb_loop_columns' );

/**
 * Products per page.
 *
 * @return int
 */
function otb_loop_per_page() {
	return max( 1, absint( otb_option( 'shop_per_page' ) ) );
}
add_filter( 'loop_shop_per_page', 'otb_loop_per_page', 20 );

/**
 * Related products count/columns.
 *
 * @param array $args Args.
 * @return array
 */
function otb_related_products_args( $args ) {
	$count                  = absint( otb_option( 'related_count' ) );
	$args['posts_per_page'] = $count;
	$args['columns']        = min( 4, max( 1, $count ) );
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'otb_related_products_args' );

/**
 * Up-sells in four columns.
 *
 * @param array $args Args.
 * @return array
 */
function otb_upsell_args( $args ) {
	$args['columns'] = 4;
	return $args;
}
add_filter( 'woocommerce_upsell_display_args', 'otb_upsell_args' );

/**
 * Cross-sells in two columns beside the cart totals.
 *
 * @return int
 */
function otb_cross_sells_columns() {
	return 2;
}
add_filter( 'woocommerce_cross_sells_columns', 'otb_cross_sells_columns' );

/**
 * Product gallery thumbnail columns.
 *
 * @return int
 */
function otb_thumbnail_columns() {
	return 5;
}
add_filter( 'woocommerce_product_thumbnails_columns', 'otb_thumbnail_columns' );

/**
 * Breadcrumb defaults.
 *
 * @param array $defaults Defaults.
 * @return array
 */
function otb_wc_breadcrumb_defaults( $defaults ) {
	$defaults['home'] = __( 'Home', 'overtheball' );
	return $defaults;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'otb_wc_breadcrumb_defaults' );

/*
-------------------------------------------------------------------------
 * Product card
 * ---------------------------------------------------------------------- */

/**
 * Open the card media wrapper.
 */
function otb_loop_media_open() {
	echo '<div class="otb-card__media">';
}

/**
 * Close the card media wrapper.
 */
function otb_loop_media_close() {
	echo '</div>';
}

/**
 * Second gallery image shown on hover.
 */
function otb_loop_hover_image() {
	global $product;
	if ( ! otb_option( 'card_hover_image' ) || ! $product ) {
		return;
	}
	$gallery = $product->get_gallery_image_ids();
	if ( ! $gallery ) {
		return;
	}
	echo wp_get_attachment_image(
		$gallery[0],
		'woocommerce_thumbnail',
		false,
		array(
			'class'   => 'otb-card__hover-img',
			'alt'     => '',
			'loading' => 'lazy',
		)
	);
}

/**
 * "New" and "Sold out" badges.
 */
function otb_loop_badges() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$badges = array();

	if ( ! $product->is_in_stock() ) {
		$badges[] = '<span class="otb-badge otb-badge--soldout">' . esc_html__( 'Sold out', 'overtheball' ) . '</span>';
	}

	$days    = absint( otb_option( 'new_badge_days' ) );
	$created = $product->get_date_created();
	if ( $days && $created && ( time() - $created->getTimestamp() ) < $days * DAY_IN_SECONDS ) {
		$badges[] = '<span class="otb-badge otb-badge--new">' . esc_html__( 'New', 'overtheball' ) . '</span>';
	}

	if ( $badges ) {
		echo '<div class="otb-badges">' . implode( '', $badges ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	}
}

/**
 * Primary category above the product title.
 */
function otb_loop_category() {
	global $product;
	if ( ! otb_option( 'card_show_category' ) || ! $product ) {
		return;
	}
	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		echo '<span class="otb-card__cat">' . esc_html( $terms[0]->name ) . '</span>';
	}
}

/**
 * Sale badge with percentage off.
 *
 * @param string     $html    Default HTML.
 * @param WP_Post    $post    Post.
 * @param WC_Product $product Product.
 * @return string
 */
function otb_sale_flash( $html, $post, $product ) {
	$label = __( 'Sale', 'overtheball' );

	if ( 'percent' === otb_option( 'sale_badge' ) && $product ) {
		$percent = 0;
		if ( $product->is_type( 'variable' ) ) {
			$prices = $product->get_variation_prices();
			foreach ( $prices['regular_price'] as $id => $regular ) {
				$sale = $prices['sale_price'][ $id ];
				if ( $regular > 0 && $sale < $regular ) {
					$percent = max( $percent, ( $regular - $sale ) / $regular * 100 );
				}
			}
		} else {
			$regular = (float) $product->get_regular_price();
			$sale    = (float) $product->get_sale_price();
			if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
				$percent = ( $regular - $sale ) / $regular * 100;
			}
		}
		if ( $percent >= 1 ) {
			/* translators: %d: discount percentage. */
			$label = sprintf( __( '-%d%%', 'overtheball' ), round( $percent ) );
		}
	}

	return '<span class="onsale">' . esc_html( $label ) . '</span>';
}
add_filter( 'woocommerce_sale_flash', 'otb_sale_flash', 10, 3 );

/*
-------------------------------------------------------------------------
 * Shop toolbar & filter drawer
 * ---------------------------------------------------------------------- */

/**
 * Open the toolbar around result count + ordering, with the mobile filter button.
 */
function otb_shop_toolbar_open() {
	echo '<div class="otb-shop-toolbar">';
	$layout = otb_get_layout();
	if ( 'sidebar-shop' === $layout['sidebar'] && otb_option( 'shop_filter_drawer' ) ) {
		printf(
			'<button type="button" class="btn btn--outline btn--sm otb-filter-toggle" aria-controls="secondary" aria-expanded="false">%s<span>%s</span></button>',
			otb_get_icon( 'filter', array( 'size' => 18 ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
			esc_html__( 'Filter', 'overtheball' )
		);
	}
}

/**
 * Close the toolbar.
 */
function otb_shop_toolbar_close() {
	echo '</div>';
}

/*
-------------------------------------------------------------------------
 * Header cart, mini-cart drawer, fragments
 * ---------------------------------------------------------------------- */

/**
 * Cart item count (safe outside a cart session).
 *
 * @return int
 */
function otb_cart_count() {
	return WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
}

/**
 * Header cart link.
 */
function otb_header_cart() {
	$drawer = 'drawer' === otb_option( 'cart_behavior' ) && ! is_cart() && ! is_checkout();
	$count  = otb_cart_count();
	?>
	<a class="icon-btn cart-link<?php echo $drawer ? ' js-otb-cart-toggle' : ''; ?>" href="<?php echo esc_url( wc_get_cart_url() ); ?>"<?php echo $drawer ? ' aria-controls="otb-cart-drawer" aria-expanded="false"' : ''; ?>>
		<?php otb_icon( 'bag', array( 'size' => 22 ) ); ?>
		<?php echo otb_cart_count_html( $count ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in function. ?>
		<span class="screen-reader-text"><?php esc_html_e( 'Cart', 'overtheball' ); ?></span>
		<?php if ( otb_option( 'header_cart_total' ) ) : ?>
			<?php echo otb_cart_total_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in function. ?>
		<?php endif; ?>
	</a>
	<?php
}

/**
 * Cart count bubble.
 *
 * @param int|null $count Count.
 * @return string
 */
function otb_cart_count_html( $count = null ) {
	$count = null === $count ? otb_cart_count() : $count;
	return sprintf(
		'<span class="otb-cart-count" data-count="%1$d"><span class="screen-reader-text">%2$s</span>%1$d</span>',
		$count,
		esc_html__( 'Items in cart:', 'overtheball' )
	);
}

/**
 * Cart subtotal beside the icon.
 *
 * @return string
 */
function otb_cart_total_html() {
	$total = WC()->cart ? WC()->cart->get_cart_subtotal() : '';
	return '<span class="otb-cart-total">' . wp_kses_post( $total ) . '</span>';
}

/**
 * Refresh header cart pieces via WooCommerce fragments.
 *
 * @param array $fragments Fragments.
 * @return array
 */
function otb_cart_fragments( $fragments ) {
	$fragments['span.otb-cart-count'] = otb_cart_count_html();
	$fragments['span.otb-cart-total'] = otb_cart_total_html();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'otb_cart_fragments' );

/**
 * Cart fragments are no longer loaded on every page by WooCommerce; the
 * header cart needs them.
 */
function otb_wc_scripts() {
	if ( otb_option( 'header_cart' ) && ! otb_option( 'catalog_mode' ) ) {
		wp_enqueue_script( 'wc-cart-fragments' );
	}
}
add_action( 'wp_enqueue_scripts', 'otb_wc_scripts', 20 );

/**
 * Slide-out mini cart.
 */
function otb_cart_drawer() {
	if ( ! otb_option( 'header_cart' ) || 'drawer' !== otb_option( 'cart_behavior' ) || otb_option( 'catalog_mode' ) || is_cart() || is_checkout() || ! otb_show_header() ) {
		return;
	}
	?>
	<div id="otb-cart-drawer" class="otb-drawer otb-drawer--right otb-cart-drawer" hidden>
		<div class="otb-drawer__overlay" data-otb-close></div>
		<div class="otb-drawer__panel" role="dialog" aria-modal="true" aria-labelledby="otb-cart-drawer-title" tabindex="-1">
			<div class="otb-drawer__head">
				<h2 id="otb-cart-drawer-title" class="otb-drawer__title"><?php esc_html_e( 'Your cart', 'overtheball' ); ?> <?php echo otb_cart_count_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				<button type="button" class="icon-btn otb-drawer__close" data-otb-close>
					<?php otb_icon( 'close', array( 'size' => 22 ) ); ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Close cart', 'overtheball' ); ?></span>
				</button>
			</div>
			<div class="otb-drawer__body">
				<div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'otb_after_page', 'otb_cart_drawer' );

/**
 * Free shipping progress bar markup.
 *
 * @return string
 */
function otb_free_shipping_bar() {
	$threshold = (float) otb_option( 'free_shipping_threshold' );
	if ( $threshold <= 0 || ! WC()->cart || WC()->cart->is_empty() ) {
		return '';
	}

	// Mirrors how WooCommerce's free shipping method measures the cart.
	$cart  = WC()->cart;
	$total = (float) $cart->get_displayed_subtotal() - (float) $cart->get_discount_total();
	if ( $cart->display_prices_including_tax() ) {
		$total -= (float) $cart->get_discount_tax();
	}

	$remaining = max( 0, $threshold - $total );
	$percent   = min( 100, max( 0, $total / $threshold * 100 ) );

	if ( $remaining > 0 ) {
		/* translators: %s: amount left to spend. */
		$message = sprintf( __( 'You are %s away from free shipping.', 'overtheball' ), '<strong>' . wc_price( $remaining ) . '</strong>' );
	} else {
		$message = __( 'You have unlocked free shipping!', 'overtheball' );
	}

	return sprintf(
		'<div class="otb-free-shipping%1$s"><p>%2$s %3$s</p><div class="otb-free-shipping__bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="%4$d"><span style="width:%4$d%%"></span></div></div>',
		$remaining > 0 ? '' : ' is-complete',
		otb_get_icon( 'truck', array( 'size' => 18 ) ),
		wp_kses_post( $message ),
		(int) round( $percent )
	);
}

/**
 * Echo the free shipping bar.
 */
function otb_print_free_shipping_bar() {
	echo otb_free_shipping_bar(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in function.
}

/**
 * Show the free shipping bar above the Cart block too (the classic hook does not run there).
 *
 * @param string $content Block HTML.
 * @return string
 */
function otb_cart_block_free_shipping_bar( $content ) {
	return otb_free_shipping_bar() . $content;
}
add_filter( 'render_block_woocommerce/cart', 'otb_cart_block_free_shipping_bar' );

/*
-------------------------------------------------------------------------
 * Sticky add to cart
 * ---------------------------------------------------------------------- */

/**
 * Sticky bar that appears once the add-to-cart form scrolls away.
 */
function otb_sticky_add_to_cart() {
	if ( ! is_product() || ! otb_option( 'sticky_add_to_cart' ) || otb_option( 'catalog_mode' ) ) {
		return;
	}
	$product = wc_get_product( get_queried_object_id() );
	if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return;
	}
	?>
	<div class="otb-sticky-atc" hidden>
		<div class="container otb-sticky-atc__inner">
			<div class="otb-sticky-atc__product">
				<?php echo wp_kses_post( $product->get_image( 'woocommerce_gallery_thumbnail' ) ); ?>
				<div>
					<p class="otb-sticky-atc__title"><?php echo esc_html( $product->get_name() ); ?></p>
					<p class="otb-sticky-atc__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
				</div>
			</div>
			<button type="button" class="btn btn--primary otb-sticky-atc__btn"><?php echo esc_html( $product->is_type( 'simple' ) ? $product->single_add_to_cart_text() : __( 'Choose options', 'overtheball' ) ); ?></button>
		</div>
	</div>
	<?php
}
add_action( 'otb_after_page', 'otb_sticky_add_to_cart' );

/*
-------------------------------------------------------------------------
 * Homepage product sections
 * ---------------------------------------------------------------------- */

/**
 * Product loop HTML for a homepage source, or '' when it has no products.
 *
 * @param string $source   recent|featured|best_selling|sale|top_rated|category.
 * @param int    $count    Products.
 * @param int    $category Product category ID (for "category").
 * @return string
 */
function otb_products_html( $source, $count, $category = 0 ) {
	$atts = array(
		'limit'   => $count,
		'columns' => min( 4, max( 1, $count ) ),
	);
	// WC_Shortcode_Products picks its query from the shortcode "type".
	$type = 'products';

	switch ( $source ) {
		case 'featured':
			$atts['visibility'] = 'featured';
			break;
		case 'best_selling':
			$type = 'best_selling_products';
			break;
		case 'sale':
			$type = 'sale_products';
			break;
		case 'top_rated':
			$type = 'top_rated_products';
			break;
		case 'category':
			$term = $category ? get_term( $category, 'product_cat' ) : null;
			if ( ! $term || is_wp_error( $term ) ) {
				return '';
			}
			$atts['category'] = $term->slug;
			break;
		default:
			$atts['orderby'] = 'date';
			$atts['order']   = 'DESC';
	}

	$shortcode = new WC_Shortcode_Products( $atts, $type );
	$html      = $shortcode->get_content();

	// With no matches WooCommerce returns only an empty wrapper.
	return false === strpos( $html, 'class="products' ) ? '' : $html;
}

/**
 * Body class for the mobile column count.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function otb_wc_body_classes( $classes ) {
	$classes[] = 'otb-shop-cols-m-' . absint( otb_option( 'shop_columns_mobile' ) );
	return $classes;
}
add_filter( 'body_class', 'otb_wc_body_classes' );
