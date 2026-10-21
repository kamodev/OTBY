<?php
/**
 * Sidebar for the current context (blog, page, shop, product).
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

$otb_layout = otb_get_layout();

if ( 'none' === $otb_layout['position'] ) {
	return;
}

$otb_is_shop = 'sidebar-shop' === $otb_layout['sidebar'];
?>
<aside id="secondary" class="widget-area sidebar sidebar--<?php echo esc_attr( $otb_layout['position'] ); ?><?php echo otb_option( 'sticky_sidebar' ) ? ' is-sticky' : ''; ?><?php echo $otb_is_shop && otb_option( 'shop_filter_drawer' ) ? ' sidebar--drawer' : ''; ?>" aria-label="<?php esc_attr_e( 'Sidebar', 'overtheball' ); ?>">
	<?php if ( $otb_is_shop && otb_option( 'shop_filter_drawer' ) ) : ?>
		<div class="sidebar__drawer-head otb-mobile-only">
			<span class="sidebar__drawer-title"><?php esc_html_e( 'Filter products', 'overtheball' ); ?></span>
			<button type="button" class="icon-btn sidebar__close" data-otb-close-filters>
				<?php otb_icon( 'close', array( 'size' => 22 ) ); ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Close filters', 'overtheball' ); ?></span>
			</button>
		</div>
	<?php endif; ?>
	<div class="sidebar__inner">
		<?php dynamic_sidebar( $otb_layout['sidebar'] ); ?>
	</div>
</aside>
