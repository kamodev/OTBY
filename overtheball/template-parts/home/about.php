<?php
/**
 * About / story section.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

$otb_title = otb_option( 'about_title' );
$otb_text  = otb_option( 'about_text' );
if ( ! $otb_title && ! $otb_text ) {
	return;
}

$otb_image   = absint( otb_option( 'about_image' ) );
$otb_btn_url = otb_option( 'about_btn_url' );
if ( ! $otb_btn_url ) {
	// Fall back to an "About" page when one exists.
	$otb_about_page = get_page_by_path( 'about' );
	$otb_btn_url    = $otb_about_page ? get_permalink( $otb_about_page ) : '';
}
?>
<section class="otb-section otb-about<?php echo $otb_image ? ' has-image' : ' no-image'; ?>" aria-labelledby="otb-about-title">
	<div class="container otb-about__inner">
		<div class="otb-about__media reveal">
			<?php
			if ( $otb_image ) {
				echo wp_get_attachment_image( $otb_image, 'otb-portrait', false, array( 'alt' => '' ) );
			} else {
				otb_placeholder_art( 'otb-about__placeholder' );
			}
			?>
		</div>
		<div class="otb-about__copy reveal">
			<?php if ( otb_option( 'about_eyebrow' ) ) : ?>
				<p class="eyebrow"><?php echo esc_html( otb_option( 'about_eyebrow' ) ); ?></p>
			<?php endif; ?>
			<h2 id="otb-about-title" class="otb-about__title"><?php echo esc_html( $otb_title ); ?></h2>
			<div class="otb-about__text"><?php echo wp_kses_post( wpautop( $otb_text ) ); ?></div>
			<?php if ( otb_option( 'about_signature' ) ) : ?>
				<p class="otb-about__signature"><?php echo esc_html( otb_option( 'about_signature' ) ); ?></p>
			<?php endif; ?>
			<?php otb_button( otb_option( 'about_btn_text' ), $otb_btn_url, 'outline' ); ?>
		</div>
	</div>
</section>
