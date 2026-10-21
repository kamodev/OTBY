<?php
/**
 * Footer brand block: light logo, about text and social links.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

$otb_footer_logo = absint( otb_option( 'footer_logo' ) );
?>
<div class="footer-brand">
	<a class="footer-brand__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<?php
		if ( $otb_footer_logo && wp_attachment_is_image( $otb_footer_logo ) ) {
			echo wp_get_attachment_image( $otb_footer_logo, 'medium_large', false, array( 'alt' => get_bloginfo( 'name' ) ) );
		} else {
			echo otb_get_logo_image( 'light' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in otb_get_logo_image().
		}
		?>
	</a>
	<?php if ( otb_option( 'footer_about' ) ) : ?>
		<p class="footer-brand__about"><?php echo wp_kses_post( otb_option( 'footer_about' ) ); ?></p>
	<?php endif; ?>
	<?php otb_social_links( 'otb-social--footer' ); ?>
</div>
