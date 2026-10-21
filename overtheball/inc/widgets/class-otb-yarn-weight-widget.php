<?php
/**
 * "Shop by Yarn Weight" widget for the shop sidebar.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * Lists Craft Yarn Council weights linking to the filtered shop.
 */
class OTB_Yarn_Weight_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'otb_yarn_weights',
			__( 'Over The Ball: Yarn Weights', 'overtheball' ),
			array(
				'description'                 => __( 'Links that filter the shop by yarn weight (uses the Yarn Details "Yarn weight" field).', 'overtheball' ),
				'customize_selective_refresh' => true,
			)
		);
	}

	/**
	 * Front-end output.
	 *
	 * @param array $args     Sidebar args.
	 * @param array $instance Settings.
	 */
	public function widget( $args, $instance ) {
		$title   = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Yarn Weight', 'overtheball' );
		$current = otb_requested_yarn_weight();
		$base    = otb_is_woocommerce_page() && ( is_shop() || is_product_taxonomy() ) ? remove_query_arg( array( 'yarn_weight', 'paged' ) ) : otb_shop_url();

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sidebar markup.
		echo $args['before_title'] . esc_html( apply_filters( 'widget_title', $title, $instance, $this->id_base ) ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sidebar markup.
		echo '<ul class="otb-weight-list">';
		foreach ( otb_yarn_weights() as $slug => $weight ) {
			$active = $slug === $current;
			printf(
				'<li class="%1$s"><a href="%2$s"%3$s><span class="otb-weight-list__num">%4$d</span>%5$s</a></li>',
				$active ? 'is-active' : '',
				esc_url( $active ? $base : add_query_arg( 'yarn_weight', $slug, $base ) ),
				$active ? ' aria-current="true"' : '',
				(int) $weight['cyc'],
				esc_html( $weight['label'] )
			);
		}
		echo '</ul>';
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sidebar markup.
	}

	/**
	 * Admin form.
	 *
	 * @param array $instance Settings.
	 * @return void
	 */
	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : __( 'Yarn Weight', 'overtheball' );
		printf(
			'<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s"></p>',
			esc_attr( $this->get_field_id( 'title' ) ),
			esc_html__( 'Title:', 'overtheball' ),
			esc_attr( $this->get_field_name( 'title' ) ),
			esc_attr( $title )
		);
	}

	/**
	 * Save settings.
	 *
	 * @param array $new_instance New.
	 * @param array $old_instance Old.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		return array( 'title' => sanitize_text_field( $new_instance['title'] ) );
	}
}
