<?php
/**
 * Distraction-free header for the checkout.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;
?>
<header id="masthead" class="site-header site-header--checkout has-border">
	<div class="container site-header__main">
		<a class="checkout-back" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
			<?php otb_icon( 'chevron-left', array( 'size' => 18 ) ); ?>
			<span><?php esc_html_e( 'Back to cart', 'overtheball' ); ?></span>
		</a>
		<?php otb_site_branding(); ?>
		<p class="checkout-secure"><?php otb_icon( 'lock', array( 'size' => 18 ) ); ?> <span><?php esc_html_e( 'Secure checkout', 'overtheball' ); ?></span></p>
	</div>
</header>
