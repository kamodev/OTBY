<?php
/**
 * Widget areas (sidebars).
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register every widget area the theme supports.
 */
function otb_widgets_init() {
	$standard = array(
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	);

	$areas = array(
		'sidebar-blog'    => array(
			'name'        => __( 'Blog Sidebar', 'overtheball' ),
			'description' => __( 'Shown on the blog, archives and single posts.', 'overtheball' ),
		),
		'sidebar-page'    => array(
			'name'        => __( 'Page Sidebar', 'overtheball' ),
			'description' => __( 'Shown on pages whose layout includes a sidebar.', 'overtheball' ),
		),
		'sidebar-shop'    => array(
			'name'        => __( 'Shop Sidebar', 'overtheball' ),
			'description' => __( 'Shop and product category pages. Ideal for product filters. Becomes a slide-out drawer on mobile.', 'overtheball' ),
		),
		'sidebar-product' => array(
			'name'        => __( 'Product Sidebar', 'overtheball' ),
			'description' => __( 'Single product pages (when a product sidebar is enabled).', 'overtheball' ),
		),
		'topbar'          => array(
			'name'        => __( 'Top Bar', 'overtheball' ),
			'description' => __( 'Used when a Top Bar side is set to "Widget area" (e.g. a currency switcher).', 'overtheball' ),
			'compact'     => true,
		),
		'header'          => array(
			'name'        => __( 'Header', 'overtheball' ),
			'description' => __( 'Small widgets shown in the header beside the icons.', 'overtheball' ),
			'compact'     => true,
		),
		'mobile-menu'     => array(
			'name'        => __( 'Mobile Menu Drawer', 'overtheball' ),
			'description' => __( 'Shown below the menu inside the mobile drawer.', 'overtheball' ),
		),
		'before-footer'   => array(
			'name'        => __( 'Before Footer', 'overtheball' ),
			'description' => __( 'Full-width area above the newsletter strip and footer.', 'overtheball' ),
		),
	);

	$columns = 4;
	for ( $i = 1; $i <= $columns; $i++ ) {
		$areas[ 'footer-' . $i ] = array(
			/* translators: %d: footer column number. */
			'name'        => sprintf( __( 'Footer Column %d', 'overtheball' ), $i ),
			'description' => __( 'Footer widget column. The number of columns is set in Customizer → Footer.', 'overtheball' ),
			'footer'      => true,
		);
	}

	foreach ( $areas as $id => $area ) {
		$args = array_merge(
			$standard,
			array(
				'id'          => $id,
				'name'        => $area['name'],
				'description' => $area['description'],
			)
		);

		if ( ! empty( $area['compact'] ) ) {
			$args['before_widget'] = '<div id="%1$s" class="widget widget--compact %2$s">';
			$args['after_widget']  = '</div>';
			$args['before_title']  = '<span class="screen-reader-text">';
			$args['after_title']   = '</span>';
		}

		if ( ! empty( $area['footer'] ) ) {
			$args['before_title'] = '<h2 class="widget-title footer-widget-title">';
		}

		register_sidebar( $args );
	}

	if ( otb_is_woocommerce_active() ) {
		require_once OTB_DIR . 'inc/widgets/class-otb-yarn-weight-widget.php';
		register_widget( 'OTB_Yarn_Weight_Widget' );
	}
}
add_action( 'widgets_init', 'otb_widgets_init' );
