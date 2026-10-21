<?php
/**
 * "Page Layout" meta box: per-page overrides for sidebar, header, footer and title.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * Meta fields managed by the box.
 *
 * @return array key => [label, type, choices].
 */
function otb_layout_meta_fields() {
	return array(
		'sidebar'            => array(
			'label'   => __( 'Sidebar', 'overtheball' ),
			'type'    => 'select',
			'choices' => array(
				''      => __( 'Default (Customizer)', 'overtheball' ),
				'right' => __( 'Right sidebar', 'overtheball' ),
				'left'  => __( 'Left sidebar', 'overtheball' ),
				'none'  => __( 'No sidebar', 'overtheball' ),
			),
		),
		'content_width'      => array(
			'label'   => __( 'Content width', 'overtheball' ),
			'type'    => 'select',
			'choices' => array(
				''     => __( 'Default (contained)', 'overtheball' ),
				'full' => __( 'Full width / stretched (for page builders)', 'overtheball' ),
			),
		),
		'transparent_header' => array(
			'label'   => __( 'Transparent header', 'overtheball' ),
			'type'    => 'select',
			'choices' => array(
				''    => __( 'Default', 'overtheball' ),
				'on'  => __( 'On — float over the first section', 'overtheball' ),
				'off' => __( 'Off', 'overtheball' ),
			),
		),
		'hide_title'         => array(
			'label' => __( 'Hide title banner', 'overtheball' ),
			'type'  => 'checkbox',
		),
		'hide_breadcrumbs'   => array(
			'label' => __( 'Hide breadcrumbs', 'overtheball' ),
			'type'  => 'checkbox',
		),
		'hide_header'        => array(
			'label' => __( 'Hide site header', 'overtheball' ),
			'type'  => 'checkbox',
		),
		'hide_footer'        => array(
			'label' => __( 'Hide site footer', 'overtheball' ),
			'type'  => 'checkbox',
		),
	);
}

/**
 * Post types that get the box.
 *
 * @return string[]
 */
function otb_layout_meta_post_types() {
	return apply_filters( 'otb_layout_meta_post_types', array( 'page', 'post', 'product' ) );
}

/**
 * Register the meta so it is also available over REST.
 */
function otb_register_layout_meta() {
	foreach ( otb_layout_meta_post_types() as $post_type ) {
		foreach ( otb_layout_meta_fields() as $key => $field ) {
			register_post_meta(
				$post_type,
				'_otb_' . $key,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => 'sanitize_key',
					'auth_callback'     => static function ( $allowed, $meta_key, $post_id ) {
						return current_user_can( 'edit_post', $post_id );
					},
				)
			);
		}
	}
}
add_action( 'init', 'otb_register_layout_meta' );

/**
 * Add the meta box.
 */
function otb_add_layout_meta_box() {
	add_meta_box(
		'otb-page-layout',
		__( 'Page Layout (Over The Ball)', 'overtheball' ),
		'otb_render_layout_meta_box',
		otb_layout_meta_post_types(),
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'otb_add_layout_meta_box' );

/**
 * Render the meta box.
 *
 * @param WP_Post $post Post.
 */
function otb_render_layout_meta_box( $post ) {
	wp_nonce_field( 'otb_layout_meta', 'otb_layout_meta_nonce' );
	echo '<div class="otb-meta-box">';
	foreach ( otb_layout_meta_fields() as $key => $field ) {
		$value = get_post_meta( $post->ID, '_otb_' . $key, true );
		$id    = 'otb-meta-' . $key;
		echo '<p>';
		if ( 'checkbox' === $field['type'] ) {
			printf(
				'<label for="%1$s"><input type="checkbox" id="%1$s" name="otb_meta[%2$s]" value="1" %3$s> %4$s</label>',
				esc_attr( $id ),
				esc_attr( $key ),
				checked( '1', $value, false ),
				esc_html( $field['label'] )
			);
		} else {
			printf( '<label for="%1$s"><strong>%2$s</strong></label><br><select id="%1$s" name="otb_meta[%3$s]" style="width:100%%">', esc_attr( $id ), esc_html( $field['label'] ), esc_attr( $key ) );
			foreach ( $field['choices'] as $choice => $label ) {
				printf( '<option value="%s" %s>%s</option>', esc_attr( $choice ), selected( $value, $choice, false ), esc_html( $label ) );
			}
			echo '</select>';
		}
		echo '</p>';
	}
	echo '</div>';
}

/**
 * Save the meta box.
 *
 * @param int $post_id Post ID.
 */
function otb_save_layout_meta( $post_id ) {
	if ( ! isset( $_POST['otb_layout_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['otb_layout_meta_nonce'] ), 'otb_layout_meta' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$input = isset( $_POST['otb_meta'] ) && is_array( $_POST['otb_meta'] ) ? wp_unslash( $_POST['otb_meta'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field below.

	foreach ( otb_layout_meta_fields() as $key => $field ) {
		$value = isset( $input[ $key ] ) ? sanitize_key( $input[ $key ] ) : '';

		if ( 'checkbox' === $field['type'] ) {
			$value = '1' === $value ? '1' : '';
		} elseif ( ! array_key_exists( $value, $field['choices'] ) ) {
			$value = '';
		}

		if ( '' === $value ) {
			delete_post_meta( $post_id, '_otb_' . $key );
		} else {
			update_post_meta( $post_id, '_otb_' . $key, $value );
		}
	}
}
add_action( 'save_post', 'otb_save_layout_meta' );
