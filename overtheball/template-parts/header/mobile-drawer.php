<?php
/**
 * Off-canvas mobile navigation drawer.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

$otb_location = has_nav_menu( 'mobile' ) ? 'mobile' : 'primary';
?>
<div id="otb-mobile-drawer" class="otb-drawer otb-drawer--<?php echo esc_attr( otb_option( 'mobile_menu_side' ) ); ?> otb-mobile-drawer" hidden>
	<div class="otb-drawer__overlay" data-otb-close></div>
	<div class="otb-drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'overtheball' ); ?>" tabindex="-1">
		<div class="otb-drawer__head">
			<?php otb_site_branding( false, false ); ?>
			<button type="button" class="icon-btn otb-drawer__close" data-otb-close>
				<?php otb_icon( 'close', array( 'size' => 22 ) ); ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Close menu', 'overtheball' ); ?></span>
			</button>
		</div>
		<div class="otb-drawer__body">
			<?php if ( otb_option( 'header_search' ) ) : ?>
				<?php get_template_part( 'template-parts/header/search-form' ); ?>
			<?php endif; ?>
			<nav class="mobile-navigation" aria-label="<?php esc_attr_e( 'Mobile', 'overtheball' ); ?>">
				<?php
				otb_nav_menu(
					$otb_location,
					array(
						'menu_class'  => 'menu menu--mobile',
						'menu_id'     => 'mobile-menu',
						'fallback_cb' => 'otb_primary_menu_fallback',
					)
				);
				?>
			</nav>
			<?php if ( otb_is_woocommerce_active() && otb_option( 'header_account' ) ) : ?>
				<a class="otb-drawer__account" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
					<?php otb_icon( 'user', array( 'size' => 20 ) ); ?>
					<?php is_user_logged_in() ? esc_html_e( 'My account', 'overtheball' ) : esc_html_e( 'Sign in / Register', 'overtheball' ); ?>
				</a>
			<?php endif; ?>
			<?php
			if ( is_active_sidebar( 'mobile-menu' ) ) {
				echo '<div class="otb-drawer__widgets">';
				dynamic_sidebar( 'mobile-menu' );
				echo '</div>';
			}
			otb_contact_list(
				array(
					'class'  => 'otb-contact--drawer',
					'fields' => array( 'phone', 'email' ),
				)
			);
			otb_social_links( 'otb-social--drawer' );
			?>
		</div>
	</div>
</div>
