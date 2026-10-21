<?php
/**
 * Picture gallery. With no pictures picked it shows every picture
 * imported from the previous theme (Appearance → Over The Ball → Pictures).
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

$otb_ids = otb_parse_id_list( otb_option( 'gallery_ids' ) );
if ( ! $otb_ids && function_exists( 'otb_theme_picture_ids' ) ) {
	$otb_ids = otb_theme_picture_ids();
}
$otb_ids = array_values( array_filter( $otb_ids, 'wp_attachment_is_image' ) );

$otb_limit = absint( otb_option( 'gallery_limit' ) );
if ( $otb_limit ) {
	$otb_ids = array_slice( $otb_ids, 0, $otb_limit );
}

if ( ! $otb_ids ) {
	return;
}

$otb_link = otb_option( 'gallery_link' ) ? otb_option( 'gallery_link' ) : otb_option( 'social_instagram' );
?>
<section class="otb-section otb-gallery" aria-labelledby="otb-gallery-title">
	<div class="container">
		<?php
		otb_section_heading(
			array(
				'eyebrow'   => otb_option( 'gallery_eyebrow' ),
				'title'     => otb_option( 'gallery_title' ),
				'id'        => 'otb-gallery-title',
				'link_text' => $otb_link ? __( 'Follow us', 'overtheball' ) : '',
				'link_url'  => $otb_link,
			)
		);
		?>
		<ul class="otb-gallery__grid" style="--otb-gallery-cols: <?php echo esc_attr( max( 2, min( 8, absint( otb_option( 'gallery_columns' ) ) ) ) ); ?>;">
			<?php foreach ( $otb_ids as $otb_id ) : ?>
				<?php
				$otb_full = wp_get_attachment_image_url( $otb_id, 'large' );
				$otb_alt  = trim( (string) get_post_meta( $otb_id, '_wp_attachment_image_alt', true ) );
				$otb_alt  = $otb_alt ? $otb_alt : get_the_title( $otb_id );
				?>
				<li class="otb-gallery__item reveal">
					<a href="<?php echo esc_url( $otb_full ); ?>" data-otb-lightbox aria-label="<?php echo esc_attr( sprintf( /* translators: %s: image title. */ __( 'View larger: %s', 'overtheball' ), $otb_alt ) ); ?>">
						<?php echo wp_get_attachment_image( $otb_id, 'otb-square', false, array( 'alt' => $otb_alt ) ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
