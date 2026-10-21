<?php
/**
 * Promo banner (image + copy, or copy over a background image).
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

$otb_title = otb_option( 'promo_title' );
if ( ! $otb_title ) {
	return;
}

$otb_image  = absint( otb_option( 'promo_image' ) );
$otb_layout = otb_option( 'promo_layout' );
$otb_url    = otb_option( 'promo_btn_url' ) ? otb_option( 'promo_btn_url' ) : otb_shop_url();
?>
<section class="otb-section otb-promo otb-promo--<?php echo esc_attr( $otb_layout ); ?><?php echo $otb_image ? ' has-image' : ' no-image'; ?>" aria-labelledby="otb-promo-title">
	<div class="container">
		<div class="otb-promo__card reveal">
			<div class="otb-promo__media">
				<?php
				if ( $otb_image ) {
					echo wp_get_attachment_image( $otb_image, 'background' === $otb_layout ? 'otb-hero' : 'otb-wide', false, array( 'alt' => '' ) );
				} else {
					otb_placeholder_art( 'otb-promo__placeholder' );
				}
				?>
			</div>
			<div class="otb-promo__copy">
				<?php if ( otb_option( 'promo_eyebrow' ) ) : ?>
					<p class="eyebrow"><?php echo esc_html( otb_option( 'promo_eyebrow' ) ); ?></p>
				<?php endif; ?>
				<h2 id="otb-promo-title" class="otb-promo__title"><?php echo esc_html( $otb_title ); ?></h2>
				<?php if ( otb_option( 'promo_text' ) ) : ?>
					<p class="otb-promo__text"><?php echo esc_html( otb_option( 'promo_text' ) ); ?></p>
				<?php endif; ?>
				<?php otb_button( otb_option( 'promo_btn_text' ), $otb_url, 'background' === $otb_layout && $otb_image ? 'light' : 'primary' ); ?>
			</div>
		</div>
	</div>
</section>
