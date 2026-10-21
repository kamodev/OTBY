<?php
/**
 * Header search form (searches products when WooCommerce is active).
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

$otb_products    = otb_is_woocommerce_active();
$otb_placeholder = $otb_products ? __( 'Search yarn, fiber, kits…', 'overtheball' ) : __( 'Search…', 'overtheball' );
$otb_field_id    = wp_unique_id( 'otb-search-' );
?>
<form role="search" method="get" class="otb-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $otb_field_id ); ?>"><?php esc_html_e( 'Search for:', 'overtheball' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $otb_field_id ); ?>" class="otb-search-form__field" placeholder="<?php echo esc_attr( $otb_placeholder ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	<?php if ( $otb_products ) : ?>
		<input type="hidden" name="post_type" value="product">
	<?php endif; ?>
	<button type="submit" class="otb-search-form__submit">
		<?php otb_icon( 'search', array( 'size' => 20 ) ); ?>
		<span class="screen-reader-text"><?php esc_html_e( 'Search', 'overtheball' ); ?></span>
	</button>
</form>
