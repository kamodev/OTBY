<?php
/**
 * Search form used by get_search_form().
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

$otb_field_id = wp_unique_id( 'search-form-' );
?>
<form role="search" method="get" class="search-form otb-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $otb_field_id ); ?>"><?php esc_html_e( 'Search for:', 'overtheball' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $otb_field_id ); ?>" class="search-field otb-search-form__field" placeholder="<?php esc_attr_e( 'Search…', 'overtheball' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	<button type="submit" class="search-submit otb-search-form__submit">
		<?php otb_icon( 'search', array( 'size' => 20 ) ); ?>
		<span class="screen-reader-text"><?php esc_html_e( 'Search', 'overtheball' ); ?></span>
	</button>
</form>
