<?php
/**
 * Newsletter strip (MailPoet or any newsletter shortcode).
 *
 * Also used as the homepage "newsletter" section.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

$otb_image = absint( otb_option( 'newsletter_image' ) );
?>
<section class="otb-newsletter<?php echo $otb_image ? ' has-image' : ''; ?>" aria-labelledby="otb-newsletter-title">
	<?php if ( $otb_image ) : ?>
		<div class="otb-newsletter__bg" aria-hidden="true">
			<?php
			echo wp_get_attachment_image(
				$otb_image,
				'otb-hero',
				false,
				array(
					'alt'     => '',
					'loading' => 'lazy',
				)
			);
			?>
		</div>
	<?php endif; ?>
	<div class="container otb-newsletter__inner">
		<div class="otb-newsletter__copy">
			<?php if ( otb_option( 'newsletter_eyebrow' ) ) : ?>
				<p class="eyebrow"><?php echo esc_html( otb_option( 'newsletter_eyebrow' ) ); ?></p>
			<?php endif; ?>
			<h2 id="otb-newsletter-title" class="otb-newsletter__title"><?php echo esc_html( otb_option( 'newsletter_title' ) ); ?></h2>
			<?php if ( otb_option( 'newsletter_text' ) ) : ?>
				<p class="otb-newsletter__text"><?php echo esc_html( otb_option( 'newsletter_text' ) ); ?></p>
			<?php endif; ?>
		</div>
		<div class="otb-newsletter__form">
			<?php otb_newsletter_form(); ?>
		</div>
	</div>
</section>
