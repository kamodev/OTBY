<?php
/**
 * Header wrapper: announcement bar, top bar and the main header row(s).
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

if ( otb_is_woocommerce_active() && is_checkout() && ! is_order_received_page() && otb_option( 'checkout_minimal' ) ) {
	get_template_part( 'template-parts/header/checkout-header' );
	return;
}

$otb_layout      = otb_option( 'header_layout' );
$otb_transparent = otb_is_transparent_header();
$otb_classes     = array( 'site-header', 'site-header--' . $otb_layout );

if ( otb_option( 'header_border' ) ) {
	$otb_classes[] = 'has-border';
}
if ( $otb_transparent ) {
	$otb_classes[] = 'is-transparent';
}
?>
<div class="site-header-wrap">
	<?php
	get_template_part( 'template-parts/header/announcement' );
	get_template_part( 'template-parts/header/topbar' );
	?>

	<header id="masthead" class="<?php echo esc_attr( implode( ' ', $otb_classes ) ); ?>">
		<div class="container site-header__main">
			<div class="site-header__left">
				<button type="button" class="icon-btn menu-toggle otb-mobile-only" aria-controls="otb-mobile-drawer" aria-expanded="false">
					<?php otb_icon( 'menu', array( 'size' => 24 ) ); ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Open menu', 'overtheball' ); ?></span>
				</button>
				<?php if ( 'centered' === $otb_layout ) : ?>
					<div class="otb-desktop-only site-header__social"><?php otb_social_links( 'otb-social--header' ); ?></div>
				<?php endif; ?>
			</div>

			<?php otb_site_branding( true ); ?>

			<?php if ( 'classic' === $otb_layout ) : ?>
				<nav id="site-navigation" class="main-navigation otb-desktop-only" aria-label="<?php esc_attr_e( 'Primary', 'overtheball' ); ?>">
					<?php
					otb_nav_menu(
						'primary',
						array(
							'fallback_cb' => 'otb_primary_menu_fallback',
							'menu_id'     => 'primary-menu',
						)
					);
					?>
				</nav>
			<?php elseif ( 'shop' === $otb_layout && otb_option( 'header_search' ) ) : ?>
				<div class="site-header__search otb-desktop-only">
					<?php get_template_part( 'template-parts/header/search-form' ); ?>
				</div>
			<?php endif; ?>

			<div class="site-header__right">
				<?php get_template_part( 'template-parts/header/header-icons' ); ?>
			</div>
		</div>

		<?php if ( 'classic' !== $otb_layout ) : ?>
			<div class="site-header__nav otb-desktop-only">
				<div class="container">
					<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Primary', 'overtheball' ); ?>">
						<?php
						otb_nav_menu(
							'primary',
							array(
								'fallback_cb' => 'otb_primary_menu_fallback',
								'menu_id'     => 'primary-menu',
							)
						);
						?>
					</nav>
				</div>
			</div>
		<?php endif; ?>
	</header>
</div>
