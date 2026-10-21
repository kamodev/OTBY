<?php
/**
 * Customizer registration, sanitization and live preview.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

require_once OTB_DIR . 'inc/customizer/options.php';

/**
 * Cached field map (built once per request).
 *
 * @return array
 */
function otb_customizer_fields() {
	static $fields = null;
	if ( null === $fields ) {
		$options = otb_customizer_options();
		$fields  = $options['fields'];
	}
	return $fields;
}

/**
 * Register panels, sections, settings, controls and partials.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function otb_customize_register( $wp_customize ) {
	require_once OTB_DIR . 'inc/customizer/controls.php';

	$options = otb_customizer_options();

	foreach ( $options['panels'] as $id => $panel ) {
		$wp_customize->add_panel(
			$id,
			array(
				'title'    => $panel[0],
				'priority' => $panel[1],
			)
		);
	}

	$priority = 10;
	foreach ( $options['sections'] as $id => $section ) {
		$wp_customize->add_section(
			$id,
			array(
				'title'       => $section[0],
				'panel'       => $section[1],
				'description' => isset( $section[2] ) ? $section[2] : '',
				'priority'    => $priority,
			)
		);
		$priority += 5;
	}

	$wp_customize->add_section(
		'otb_tools',
		array(
			'title'       => __( 'Theme: Pictures & Tools', 'overtheball' ),
			'priority'    => 36,
			'description' => sprintf(
				/* translators: %s: link to the theme admin page. */
				__( 'Import every picture from your previous theme, check plugin status, and export, import or reset these settings on the <a href="%s" target="_blank">Over The Ball theme page</a>.', 'overtheball' ),
				esc_url( admin_url( 'themes.php?page=overtheball' ) )
			),
		)
	);
	$wp_customize->add_control(
		new OTB_Heading_Control(
			$wp_customize,
			'otb_tools_link',
			array(
				'section'  => 'otb_tools',
				'settings' => array(),
				'label'    => __( 'Open the theme page from Appearance → Over The Ball.', 'overtheball' ),
			)
		)
	);

	foreach ( $options['fields'] as $key => $field ) {
		$control_args = array(
			'section'     => $field['section'],
			'label'       => isset( $field['label'] ) ? $field['label'] : '',
			'description' => isset( $field['description'] ) ? $field['description'] : '',
			'priority'    => isset( $field['priority'] ) ? $field['priority'] : 10,
		);

		if ( ! empty( $field['active'] ) ) {
			$control_args['active_callback'] = static function () use ( $field ) {
				return otb_customizer_condition_met( $field['active'] );
			};
		}

		if ( 'heading' === $field['type'] ) {
			$control_args['settings'] = array();
			$wp_customize->add_control( new OTB_Heading_Control( $wp_customize, $key, $control_args ) );
			continue;
		}

		$wp_customize->add_setting(
			$key,
			array(
				'default'           => otb_default( $key ),
				'transport'         => isset( $field['transport'] ) ? $field['transport'] : 'refresh',
				'sanitize_callback' => 'otb_sanitize_setting',
				'capability'        => 'edit_theme_options',
			)
		);

		switch ( $field['type'] ) {
			case 'toggle':
				$wp_customize->add_control( new OTB_Toggle_Control( $wp_customize, $key, $control_args ) );
				break;

			case 'range':
				$control_args['input_attrs'] = array(
					'min'  => isset( $field['min'] ) ? $field['min'] : 0,
					'max'  => isset( $field['max'] ) ? $field['max'] : 100,
					'step' => isset( $field['step'] ) ? $field['step'] : 1,
				);
				$control                     = new OTB_Range_Control( $wp_customize, $key, $control_args );
				$control->unit               = isset( $field['unit'] ) ? $field['unit'] : '';
				$wp_customize->add_control( $control );
				break;

			case 'color':
				$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $key, $control_args ) );
				break;

			case 'image':
				$control_args['mime_type'] = 'image';
				$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $key, $control_args ) );
				break;

			case 'sortable':
				$control_args['choices'] = $field['choices'];
				$wp_customize->add_control( new OTB_Sortable_Control( $wp_customize, $key, $control_args ) );
				break;

			case 'gallery':
				$wp_customize->add_control( new OTB_Gallery_Control( $wp_customize, $key, $control_args ) );
				break;

			case 'select':
			case 'radio':
				$control_args['type']    = $field['type'];
				$control_args['choices'] = $field['choices'];
				$wp_customize->add_control( $key, $control_args );
				break;

			case 'number':
				$control_args['type']        = 'number';
				$control_args['input_attrs'] = array(
					'min'  => isset( $field['min'] ) ? $field['min'] : 0,
					'max'  => isset( $field['max'] ) ? $field['max'] : 9999,
					'step' => isset( $field['step'] ) ? $field['step'] : 1,
				);
				$wp_customize->add_control( $key, $control_args );
				break;

			case 'textarea':
				$control_args['type'] = 'textarea';
				$wp_customize->add_control( $key, $control_args );
				break;

			case 'url':
				$control_args['type'] = 'url';
				$wp_customize->add_control( $key, $control_args );
				break;

			case 'email':
				$control_args['type'] = 'email';
				$wp_customize->add_control( $key, $control_args );
				break;

			default:
				$control_args['type'] = 'text';
				$wp_customize->add_control( $key, $control_args );
		}

		if ( ! empty( $field['partial'] ) && isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial(
				$key,
				array(
					'selector'            => $field['partial'],
					'settings'            => array( $key ),
					'container_inclusive' => false,
					'render_callback'     => static function () use ( $key ) {
						return otb_render_partial( $key );
					},
				)
			);
		}
	}
}
add_action( 'customize_register', 'otb_customize_register' );

/**
 * Evaluate an "active" condition map against current (previewed) values.
 *
 * @param array $conditions setting => required value.
 * @return bool
 */
function otb_customizer_condition_met( $conditions ) {
	foreach ( $conditions as $setting => $required ) {
		$current = otb_option( $setting );
		if ( is_bool( $required ) ) {
			if ( (bool) $current !== $required ) {
				return false;
			}
		} elseif ( (string) $current !== (string) $required ) {
			return false;
		}
	}
	return true;
}

/**
 * Sanitize any theme setting based on its field definition.
 *
 * @param mixed                $value   Raw value.
 * @param WP_Customize_Setting $setting Setting.
 * @return mixed
 */
function otb_sanitize_setting( $value, $setting ) {
	$key = is_object( $setting ) ? $setting->id : (string) $setting;
	return otb_sanitize_value( $key, $value );
}

/**
 * Sanitize a value for a setting key (shared with the settings importer).
 *
 * @param string $key   Setting key.
 * @param mixed  $value Raw value.
 * @return mixed
 */
function otb_sanitize_value( $key, $value ) {
	$fields = otb_customizer_fields();
	if ( ! isset( $fields[ $key ] ) ) {
		return otb_default( $key );
	}
	$field   = $fields[ $key ];
	$default = otb_default( $key );

	switch ( $field['type'] ) {
		case 'toggle':
			return ! empty( $value ) && 'false' !== $value;

		case 'select':
		case 'radio':
			return array_key_exists( (string) $value, (array) $field['choices'] ) ? $value : $default;

		case 'color':
			$color = sanitize_hex_color( $value );
			return $color ? strtoupper( $color ) : $default;

		case 'range':
		case 'number':
			$min  = isset( $field['min'] ) ? $field['min'] : 0;
			$max  = isset( $field['max'] ) ? $field['max'] : PHP_INT_MAX;
			$step = isset( $field['step'] ) ? $field['step'] : 1;
			$num  = is_numeric( $value ) ? $value + 0 : $default;
			$num  = max( $min, min( $max, $num ) );
			return ( is_float( $step ) || floor( $step ) !== (float) $step ) ? (float) $num : (int) round( $num );

		case 'image':
			return absint( $value );

		case 'gallery':
			return implode( ',', otb_parse_id_list( $value ) );

		case 'sortable':
			$keys  = array_filter( array_map( 'trim', explode( ',', (string) $value ) ) );
			$valid = array_values( array_intersect( $keys, array_keys( $field['choices'] ) ) );
			return implode( ',', $valid );

		case 'url':
			return esc_url_raw( $value );

		case 'email':
			return sanitize_email( $value );

		case 'textarea':
			return wp_kses_post( $value );

		case 'html':
			return wp_kses(
				$value,
				array(
					'a'      => array(
						'href'   => array(),
						'target' => array(),
						'rel'    => array(),
					),
					'strong' => array(),
					'em'     => array(),
					'br'     => array(),
					'span'   => array( 'class' => array() ),
				)
			);

		default:
			return sanitize_text_field( $value );
	}
}

/**
 * Selective refresh renderers.
 *
 * @param string $key Setting key.
 * @return string
 */
function otb_render_partial( $key ) {
	switch ( $key ) {
		case 'copyright':
			return wp_kses_post( otb_replace_tokens( otb_option( 'copyright' ) ) );
		case 'topbar_text':
		case 'footer_about':
			return wp_kses_post( otb_option( $key ) );
		default:
			return esc_html( otb_option( $key ) );
	}
}

/**
 * Controls pane assets.
 */
function otb_customize_controls_assets() {
	wp_enqueue_style( 'otb-customizer-controls', OTB_URI . 'assets/css/customizer-controls.css', array(), OTB_VERSION );
	wp_enqueue_media();
	wp_enqueue_script( 'otb-customizer-controls', OTB_URI . 'assets/js/customizer-controls.js', array( 'customize-controls', 'jquery', 'jquery-ui-sortable' ), OTB_VERSION, true );
	wp_localize_script(
		'otb-customizer-controls',
		'otbControls',
		array(
			'galleryTitle'  => __( 'Choose gallery pictures', 'overtheball' ),
			'galleryButton' => __( 'Use these pictures', 'overtheball' ),
		)
	);
}
add_action( 'customize_controls_enqueue_scripts', 'otb_customize_controls_assets' );

/**
 * Preview frame script: live CSS variable updates.
 */
function otb_customize_preview_assets() {
	$map = array();
	foreach ( otb_customizer_fields() as $key => $field ) {
		if ( empty( $field['css_var'] ) ) {
			continue;
		}
		$map[ $key ] = array(
			'var'   => $field['css_var'],
			'unit'  => isset( $field['css_unit'] ) ? $field['css_unit'] : ( isset( $field['unit'] ) && 'px' === $field['unit'] ? 'px' : '' ),
			'scale' => isset( $field['css_scale'] ) ? $field['css_scale'] : 1,
		);
	}

	wp_enqueue_script( 'otb-customizer-preview', OTB_URI . 'assets/js/customizer-preview.js', array( 'customize-preview' ), OTB_VERSION, true );
	wp_localize_script( 'otb-customizer-preview', 'otbPreviewMap', $map );
}
add_action( 'customize_preview_init', 'otb_customize_preview_assets' );
