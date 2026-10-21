<?php
/**
 * Shop by category tiles (WooCommerce product categories).
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

if ( ! otb_is_woocommerce_active() ) {
	return;
}

$otb_ids  = otb_parse_id_list( otb_option( 'categories_ids' ) );
$otb_args = array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => false,
	'number'     => max( 1, absint( otb_option( 'categories_count' ) ) ),
);

if ( $otb_ids ) {
	$otb_args['include'] = $otb_ids;
	$otb_args['orderby'] = 'include';
} else {
	$otb_args['parent']     = 0;
	$otb_args['hide_empty'] = true;
	$otb_args['orderby']    = 'menu_order';
	$otb_args['exclude']    = array( (int) get_option( 'default_product_cat' ) );
}

$otb_terms = get_terms( $otb_args );
if ( is_wp_error( $otb_terms ) || ! $otb_terms ) {
	return;
}

$otb_style = otb_option( 'categories_style' );
?>
<section class="otb-section otb-categories otb-categories--<?php echo esc_attr( $otb_style ); ?>" aria-labelledby="otb-categories-title">
	<div class="container">
		<?php
		otb_section_heading(
			array(
				'eyebrow'   => otb_option( 'categories_eyebrow' ),
				'title'     => otb_option( 'categories_title' ),
				'id'        => 'otb-categories-title',
				'link_text' => __( 'View all', 'overtheball' ),
				'link_url'  => otb_shop_url(),
			)
		);
		?>
		<ul class="otb-categories__grid otb-categories__grid--<?php echo esc_attr( min( 6, count( $otb_terms ) ) ); ?>">
			<?php foreach ( $otb_terms as $otb_term ) : ?>
				<?php $otb_thumb = absint( get_term_meta( $otb_term->term_id, 'thumbnail_id', true ) ); ?>
				<li class="otb-cat reveal">
					<a class="otb-cat__link" href="<?php echo esc_url( get_term_link( $otb_term ) ); ?>">
						<span class="otb-cat__media">
							<?php
							if ( $otb_thumb ) {
								echo wp_get_attachment_image( $otb_thumb, 'circles' === $otb_style ? 'otb-square' : 'otb-portrait', false, array( 'alt' => '' ) );
							} else {
								otb_placeholder_art( 'otb-cat__placeholder' );
							}
							?>
						</span>
						<span class="otb-cat__label">
							<span class="otb-cat__name"><?php echo esc_html( $otb_term->name ); ?></span>
							<span class="otb-cat__count">
								<?php
								/* translators: %d: number of products. */
								echo esc_html( sprintf( _n( '%d product', '%d products', (int) $otb_term->count, 'overtheball' ), (int) $otb_term->count ) );
								?>
							</span>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
