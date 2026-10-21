<?php
/**
 * Testimonials. Hidden until at least one quote is entered in the Customizer.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

$otb_quotes = array();
for ( $otb_i = 1; $otb_i <= 3; $otb_i++ ) {
	$otb_quote = otb_option( "testimonial_{$otb_i}_quote" );
	if ( $otb_quote ) {
		$otb_quotes[] = array(
			'quote'  => $otb_quote,
			'name'   => otb_option( "testimonial_{$otb_i}_name" ),
			'detail' => otb_option( "testimonial_{$otb_i}_detail" ),
		);
	}
}

if ( ! $otb_quotes ) {
	return;
}
?>
<section class="otb-section otb-testimonials" aria-labelledby="otb-testimonials-title">
	<div class="container">
		<?php
		otb_section_heading(
			array(
				'eyebrow' => otb_option( 'testimonials_eyebrow' ),
				'title'   => otb_option( 'testimonials_title' ),
				'id'      => 'otb-testimonials-title',
			)
		);
		?>
		<div class="otb-testimonials__grid otb-testimonials__grid--<?php echo count( $otb_quotes ); ?>">
			<?php foreach ( $otb_quotes as $otb_quote ) : ?>
				<figure class="otb-testimonial reveal">
					<?php
					otb_icon(
						'quote',
						array(
							'size'  => 32,
							'class' => 'otb-testimonial__icon',
						)
					);
					?>
					<blockquote class="otb-testimonial__quote"><p><?php echo esc_html( $otb_quote['quote'] ); ?></p></blockquote>
					<?php if ( $otb_quote['name'] ) : ?>
						<figcaption class="otb-testimonial__by">
							<span class="otb-testimonial__name"><?php echo esc_html( $otb_quote['name'] ); ?></span>
							<?php if ( $otb_quote['detail'] ) : ?>
								<span class="otb-testimonial__detail"><?php echo esc_html( $otb_quote['detail'] ); ?></span>
							<?php endif; ?>
						</figcaption>
					<?php endif; ?>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>
