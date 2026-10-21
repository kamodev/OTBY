<?php
/**
 * Full-width search overlay opened from the header search icon.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

if ( ! otb_option( 'header_search' ) ) {
	return;
}
?>
<div id="otb-search-overlay" class="otb-search-overlay" hidden>
	<div class="otb-search-overlay__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Search', 'overtheball' ); ?>">
		<div class="container">
			<p class="eyebrow"><?php esc_html_e( 'What are you making next?', 'overtheball' ); ?></p>
			<?php get_template_part( 'template-parts/header/search-form' ); ?>
			<?php
			if ( otb_is_woocommerce_active() ) {
				$otb_popular = get_terms(
					array(
						'taxonomy'   => 'product_cat',
						'orderby'    => 'count',
						'order'      => 'DESC',
						'number'     => 6,
						'hide_empty' => true,
						'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
					)
				);
				if ( ! is_wp_error( $otb_popular ) && $otb_popular ) {
					echo '<div class="otb-search-overlay__popular"><span>' . esc_html__( 'Popular:', 'overtheball' ) . '</span>';
					foreach ( $otb_popular as $otb_term ) {
						printf( '<a class="chip" href="%s">%s</a>', esc_url( get_term_link( $otb_term ) ), esc_html( $otb_term->name ) );
					}
					echo '</div>';
				}
			}
			?>
		</div>
		<button type="button" class="icon-btn otb-search-overlay__close" data-otb-close>
			<?php otb_icon( 'close', array( 'size' => 24 ) ); ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Close search', 'overtheball' ); ?></span>
		</button>
	</div>
	<div class="otb-search-overlay__backdrop" data-otb-close></div>
</div>
