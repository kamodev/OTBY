<?php
/**
 * Product carousel / grid with optional tabs (new, best sellers, on sale).
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'otb_products_html' ) ) {
	return;
}

$otb_count  = max( 1, absint( otb_option( 'products_count' ) ) );
$otb_layout = otb_option( 'products_layout' );
$otb_tabs   = array();

if ( otb_option( 'products_tabs' ) ) {
	$otb_sources = array(
		'recent'       => __( 'New Arrivals', 'overtheball' ),
		'best_selling' => __( 'Best Sellers', 'overtheball' ),
		'sale'         => __( 'On Sale', 'overtheball' ),
	);
} else {
	$otb_sources = array( otb_option( 'products_source' ) => '' );
}

foreach ( $otb_sources as $otb_source => $otb_label ) {
	$otb_html = otb_products_html( $otb_source, $otb_count, absint( otb_option( 'products_category' ) ) );
	if ( $otb_html ) {
		$otb_tabs[ $otb_source ] = array(
			'label' => $otb_label,
			'html'  => $otb_html,
		);
	}
}

if ( ! $otb_tabs ) {
	return;
}

$otb_show_tabs = count( $otb_tabs ) > 1;
?>
<section class="otb-section otb-products otb-products--<?php echo esc_attr( $otb_layout ); ?>" aria-labelledby="otb-products-title">
	<div class="container">
		<?php
		otb_section_heading(
			array(
				'eyebrow'   => otb_option( 'products_eyebrow' ),
				'title'     => otb_option( 'products_title' ),
				'id'        => 'otb-products-title',
				'link_text' => __( 'Shop all', 'overtheball' ),
				'link_url'  => otb_shop_url(),
			)
		);
		?>

		<?php if ( $otb_show_tabs ) : ?>
			<div class="otb-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Product collections', 'overtheball' ); ?>">
				<?php $otb_first = true; ?>
				<?php foreach ( $otb_tabs as $otb_key => $otb_tab ) : ?>
					<button type="button" role="tab" class="otb-tab<?php echo $otb_first ? ' is-active' : ''; ?>" id="otb-tab-<?php echo esc_attr( $otb_key ); ?>" aria-controls="otb-panel-<?php echo esc_attr( $otb_key ); ?>" aria-selected="<?php echo $otb_first ? 'true' : 'false'; ?>"<?php echo $otb_first ? '' : ' tabindex="-1"'; ?>><?php echo esc_html( $otb_tab['label'] ); ?></button>
					<?php $otb_first = false; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php $otb_first = true; ?>
		<?php foreach ( $otb_tabs as $otb_key => $otb_tab ) : ?>
			<div class="otb-tab-panel<?php echo 'carousel' === $otb_layout ? ' otb-carousel' : ''; ?>" id="otb-panel-<?php echo esc_attr( $otb_key ); ?>"<?php echo $otb_show_tabs ? ' role="tabpanel" aria-labelledby="otb-tab-' . esc_attr( $otb_key ) . '"' : ''; ?><?php echo $otb_first ? '' : ' hidden'; ?>>
				<?php echo $otb_tab['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce loop markup. ?>
				<?php if ( 'carousel' === $otb_layout ) : ?>
					<div class="otb-carousel__nav">
						<button type="button" class="otb-carousel__btn otb-carousel__prev" aria-label="<?php esc_attr_e( 'Scroll back', 'overtheball' ); ?>"><?php otb_icon( 'chevron-left', array( 'size' => 20 ) ); ?></button>
						<button type="button" class="otb-carousel__btn otb-carousel__next" aria-label="<?php esc_attr_e( 'Scroll forward', 'overtheball' ); ?>"><?php otb_icon( 'chevron-right', array( 'size' => 20 ) ); ?></button>
					</div>
				<?php endif; ?>
			</div>
			<?php $otb_first = false; ?>
		<?php endforeach; ?>
	</div>
</section>
