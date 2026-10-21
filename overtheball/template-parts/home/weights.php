<?php
/**
 * Shop by yarn weight (Craft Yarn Council categories or a product attribute).
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

if ( ! otb_is_woocommerce_active() ) {
	return;
}

$otb_items  = array();
$otb_source = otb_option( 'weights_source' );
$otb_attr   = sanitize_key( otb_option( 'weights_attribute' ) );

if ( 'attribute' === $otb_source && $otb_attr && taxonomy_exists( $otb_attr ) ) {
	$otb_terms = get_terms(
		array(
			'taxonomy'   => $otb_attr,
			'hide_empty' => true,
			'orderby'    => 'menu_order',
		)
	);
	if ( ! is_wp_error( $otb_terms ) ) {
		$otb_known = otb_yarn_weights();
		$otb_n     = 0;
		foreach ( $otb_terms as $otb_term ) {
			$otb_match   = isset( $otb_known[ $otb_term->slug ] ) ? $otb_known[ $otb_term->slug ] : null;
			$otb_link    = is_taxonomy_viewable( $otb_attr ) ? get_term_link( $otb_term ) : add_query_arg( 'filter_' . substr( $otb_attr, 3 ), $otb_term->slug, otb_shop_url() );
			$otb_items[] = array(
				'label'  => $otb_term->name,
				'badge'  => $otb_match ? $otb_match['cyc'] : $otb_n,
				'detail' => $otb_term->description ? $otb_term->description : ( $otb_match ? $otb_match['detail'] : '' ),
				'needle' => $otb_match ? $otb_match['needle'] : '',
				'url'    => $otb_link,
			);
			++$otb_n;
		}
	}
} else {
	foreach ( otb_yarn_weights() as $otb_slug => $otb_weight ) {
		$otb_items[] = array(
			'label'  => $otb_weight['label'],
			'badge'  => $otb_weight['cyc'],
			'detail' => $otb_weight['detail'],
			'needle' => $otb_weight['needle'],
			'url'    => add_query_arg( 'yarn_weight', $otb_slug, otb_shop_url() ),
		);
	}
}

if ( ! $otb_items ) {
	return;
}
?>
<section class="otb-section otb-weights" aria-labelledby="otb-weights-title">
	<div class="container">
		<?php
		otb_section_heading(
			array(
				'eyebrow' => otb_option( 'weights_eyebrow' ),
				'title'   => otb_option( 'weights_title' ),
				'id'      => 'otb-weights-title',
			)
		);
		?>
		<ul class="otb-weights__grid">
			<?php foreach ( $otb_items as $otb_item ) : ?>
				<li class="reveal">
					<a class="otb-weight" href="<?php echo esc_url( $otb_item['url'] ); ?>">
						<span class="otb-weight__badge" aria-hidden="true">
							<?php otb_icon( 'yarn', array( 'size' => 44 ) ); ?>
							<span class="otb-weight__num"><?php echo esc_html( $otb_item['badge'] ); ?></span>
						</span>
						<span class="otb-weight__label"><?php echo esc_html( $otb_item['label'] ); ?></span>
						<?php if ( $otb_item['detail'] ) : ?>
							<span class="otb-weight__detail"><?php echo esc_html( $otb_item['detail'] ); ?></span>
						<?php endif; ?>
						<?php if ( $otb_item['needle'] ) : ?>
							<span class="otb-weight__needle"><?php otb_icon( 'needles', array( 'size' => 14 ) ); ?> <?php echo esc_html( $otb_item['needle'] ); ?></span>
						<?php endif; ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
