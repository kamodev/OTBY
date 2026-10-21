<?php
/**
 * Footer: before-footer area, newsletter strip, widget columns, bottom bar.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

if ( otb_is_woocommerce_active() && is_checkout() && ! is_order_received_page() && otb_option( 'checkout_minimal' ) ) {
	?>
	<footer id="colophon" class="site-footer site-footer--minimal">
		<div class="container site-footer__bottom-inner">
			<p class="site-footer__copyright"><?php echo wp_kses_post( otb_replace_tokens( otb_option( 'copyright' ) ) ); ?></p>
			<?php otb_payment_icons(); ?>
		</div>
	</footer>
	<?php
	return;
}

if ( is_active_sidebar( 'before-footer' ) ) :
	?>
	<div class="before-footer">
		<div class="container"><?php dynamic_sidebar( 'before-footer' ); ?></div>
	</div>
	<?php
endif;

if ( otb_show_newsletter_strip() ) {
	get_template_part( 'template-parts/footer/newsletter' );
}

$otb_columns = min( 4, absint( otb_option( 'footer_columns' ) ) );
$otb_widgets = false;
for ( $otb_i = 1; $otb_i <= $otb_columns; $otb_i++ ) {
	if ( is_active_sidebar( 'footer-' . $otb_i ) ) {
		$otb_widgets = true;
	}
}
?>
<footer id="colophon" class="site-footer">
	<?php if ( $otb_columns ) : ?>
		<div class="site-footer__widgets">
			<div class="container footer-columns footer-columns--<?php echo esc_attr( $otb_columns ); ?> footer-columns--<?php echo esc_attr( otb_option( 'footer_column_layout' ) ); ?>">
				<?php for ( $otb_i = 1; $otb_i <= $otb_columns; $otb_i++ ) : ?>
					<div class="footer-col footer-col--<?php echo esc_attr( $otb_i ); ?>">
						<?php
						if ( 1 === $otb_i && otb_option( 'footer_brand' ) ) {
							get_template_part( 'template-parts/footer/brand' );
						}

						if ( is_active_sidebar( 'footer-' . $otb_i ) ) {
							dynamic_sidebar( 'footer-' . $otb_i );
						} elseif ( ! $otb_widgets ) {
							get_template_part( 'template-parts/footer/default-column', null, array( 'column' => $otb_i ) );
						}
						?>
					</div>
				<?php endfor; ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="site-footer__bottom">
		<div class="container site-footer__bottom-inner">
			<p class="site-footer__copyright"><?php echo wp_kses_post( otb_replace_tokens( otb_option( 'copyright' ) ) ); ?></p>
			<?php
			if ( has_nav_menu( 'footer-bottom' ) ) {
				echo '<nav class="footer-bottom-nav" aria-label="' . esc_attr__( 'Legal', 'overtheball' ) . '">';
				otb_nav_menu(
					'footer-bottom',
					array(
						'depth'       => 1,
						'otb_toggles' => false,
					)
				);
				echo '</nav>';
			}
			otb_payment_icons();
			?>
		</div>
	</div>
</footer>
