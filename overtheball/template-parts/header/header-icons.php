<?php
/**
 * Header action icons: search, account, cart and the header widget area.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

$otb_layout = otb_option( 'header_layout' );
?>
<div class="header-actions">
	<?php if ( is_active_sidebar( 'header' ) ) : ?>
		<div class="header-widgets otb-desktop-only"><?php dynamic_sidebar( 'header' ); ?></div>
	<?php endif; ?>

	<?php if ( otb_option( 'header_search' ) ) : ?>
		<button type="button" class="icon-btn search-toggle<?php echo 'shop' === $otb_layout ? ' otb-mobile-only' : ''; ?>" aria-controls="otb-search-overlay" aria-expanded="false">
			<?php otb_icon( 'search', array( 'size' => 22 ) ); ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Search', 'overtheball' ); ?></span>
		</button>
	<?php endif; ?>

	<?php if ( otb_option( 'header_account' ) && otb_is_woocommerce_active() ) : ?>
		<a class="icon-btn account-link otb-desktop-only" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
			<?php otb_icon( 'user', array( 'size' => 22 ) ); ?>
			<span class="screen-reader-text"><?php is_user_logged_in() ? esc_html_e( 'My account', 'overtheball' ) : esc_html_e( 'Sign in', 'overtheball' ); ?></span>
		</a>
	<?php endif; ?>

	<?php
	if ( otb_option( 'header_cart' ) && otb_is_woocommerce_active() && ! otb_option( 'catalog_mode' ) ) {
		otb_header_cart();
	}
	?>
</div>
