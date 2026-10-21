<?php
/**
 * Homepage hero slider (up to three slides).
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

$otb_slides = array();
for ( $otb_i = 1; $otb_i <= 3; $otb_i++ ) {
	$otb_slide = array(
		'image'     => absint( otb_option( "hero_{$otb_i}_image" ) ),
		'eyebrow'   => otb_option( "hero_{$otb_i}_eyebrow" ),
		'title'     => otb_option( "hero_{$otb_i}_title" ),
		'text'      => otb_option( "hero_{$otb_i}_text" ),
		'btn_text'  => otb_option( "hero_{$otb_i}_btn_text" ),
		'btn_url'   => otb_option( "hero_{$otb_i}_btn_url" ),
		'btn2_text' => otb_option( "hero_{$otb_i}_btn2_text" ),
		'btn2_url'  => otb_option( "hero_{$otb_i}_btn2_url" ),
	);
	if ( ! $otb_slide['title'] && ! $otb_slide['image'] ) {
		continue;
	}
	if ( $otb_slide['btn_text'] && ! $otb_slide['btn_url'] ) {
		$otb_slide['btn_url'] = otb_shop_url();
	}
	if ( $otb_slide['btn2_text'] && ! $otb_slide['btn2_url'] ) {
		$otb_slide['btn2_url'] = otb_is_woocommerce_active() ? add_query_arg( 'orderby', 'date', otb_shop_url() ) : otb_shop_url();
	}
	$otb_slides[] = $otb_slide;
}

if ( ! $otb_slides ) {
	return;
}

$otb_count = count( $otb_slides );
?>
<section class="otb-hero otb-hero--<?php echo esc_attr( otb_option( 'hero_height' ) ); ?> otb-hero--<?php echo esc_attr( otb_option( 'hero_align' ) ); ?>" style="--otb-overlay: <?php echo esc_attr( absint( otb_option( 'hero_overlay' ) ) / 100 ); ?>;" <?php echo $otb_count > 1 ? 'aria-roledescription="carousel"' : ''; ?> aria-label="<?php esc_attr_e( 'Featured', 'overtheball' ); ?>" data-autoplay="<?php echo esc_attr( absint( otb_option( 'hero_autoplay' ) ) ); ?>">
	<div class="otb-hero__slides">
		<?php foreach ( $otb_slides as $otb_index => $otb_slide ) : ?>
			<div class="otb-hero__slide<?php echo 0 === $otb_index ? ' is-active' : ''; ?><?php echo $otb_slide['image'] ? ' has-image' : ' no-image'; ?>" <?php echo $otb_count > 1 ? 'role="group" aria-roledescription="slide"' : ''; ?> aria-label="<?php echo esc_attr( sprintf( /* translators: 1: slide number, 2: slide count. */ __( '%1$d of %2$d', 'overtheball' ), $otb_index + 1, $otb_count ) ); ?>">
				<?php if ( $otb_slide['image'] ) : ?>
					<div class="otb-hero__media">
						<?php
						echo wp_get_attachment_image(
							$otb_slide['image'],
							'otb-hero',
							false,
							array(
								'class'         => 'otb-hero__img',
								'alt'           => '',
								'loading'       => 0 === $otb_index ? 'eager' : 'lazy',
								'fetchpriority' => 0 === $otb_index ? 'high' : 'low',
								'sizes'         => '100vw',
							)
						);
						?>
					</div>
				<?php else : ?>
					<div class="otb-hero__art" aria-hidden="true">
						<img src="<?php echo esc_url( OTB_URI . 'assets/images/site-icon.png' ); ?>" alt="" width="512" height="512" decoding="async">
					</div>
				<?php endif; ?>

				<div class="container otb-hero__content">
					<div class="otb-hero__copy">
						<?php if ( $otb_slide['eyebrow'] ) : ?>
							<p class="eyebrow otb-hero__eyebrow"><?php echo esc_html( $otb_slide['eyebrow'] ); ?></p>
						<?php endif; ?>
						<?php if ( $otb_slide['title'] ) : ?>
							<?php $otb_tag = 0 === $otb_index ? 'h1' : 'h2'; ?>
							<<?php echo esc_attr( $otb_tag ); ?> class="otb-hero__title"><?php echo esc_html( $otb_slide['title'] ); ?></<?php echo esc_attr( $otb_tag ); ?>>
						<?php endif; ?>
						<?php if ( $otb_slide['text'] ) : ?>
							<p class="otb-hero__text"><?php echo esc_html( $otb_slide['text'] ); ?></p>
						<?php endif; ?>
						<?php if ( $otb_slide['btn_text'] || $otb_slide['btn2_text'] ) : ?>
							<div class="otb-hero__actions">
								<?php
								otb_button( $otb_slide['btn_text'], $otb_slide['btn_url'], 'primary' );
								otb_button( $otb_slide['btn2_text'], $otb_slide['btn2_url'], $otb_slide['image'] ? 'light' : 'outline' );
								?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( $otb_count > 1 ) : ?>
		<div class="otb-hero__controls">
			<button type="button" class="otb-hero__arrow otb-hero__prev" aria-label="<?php esc_attr_e( 'Previous slide', 'overtheball' ); ?>"><?php otb_icon( 'chevron-left', array( 'size' => 22 ) ); ?></button>
			<div class="otb-hero__dots">
				<?php for ( $otb_d = 0; $otb_d < $otb_count; $otb_d++ ) : ?>
					<button type="button" class="otb-hero__dot<?php echo 0 === $otb_d ? ' is-active' : ''; ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: slide number. */ __( 'Go to slide %d', 'overtheball' ), $otb_d + 1 ) ); ?>"<?php echo 0 === $otb_d ? ' aria-current="true"' : ''; ?>></button>
				<?php endfor; ?>
			</div>
			<button type="button" class="otb-hero__arrow otb-hero__next" aria-label="<?php esc_attr_e( 'Next slide', 'overtheball' ); ?>"><?php otb_icon( 'chevron-right', array( 'size' => 22 ) ); ?></button>
		</div>
	<?php endif; ?>
</section>
