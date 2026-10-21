<?php
/**
 * Yarn-specific product data: a "Yarn Details" tab in the product editor,
 * spec chips and a spec table on the product page, a skein calculator,
 * a dye-lot notice and shop filtering by yarn weight (?yarn_weight=dk).
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * Yarn detail fields.
 *
 * @return array key => field config.
 */
function otb_yarn_fields() {
	$weights = array( '' => __( '— Not set —', 'overtheball' ) );
	foreach ( otb_yarn_weights() as $slug => $weight ) {
		/* translators: 1: CYC weight number, 2: weight name. */
		$weights[ $slug ] = sprintf( __( '%1$d · %2$s', 'overtheball' ), $weight['cyc'], $weight['label'] );
	}

	return array(
		'yarn_weight'  => array(
			'label'   => __( 'Yarn weight', 'overtheball' ),
			'type'    => 'select',
			'options' => $weights,
			'desc'    => __( 'Craft Yarn Council standard weight. Powers "Shop by Weight".', 'overtheball' ),
		),
		'fiber'        => array(
			'label'       => __( 'Fiber content', 'overtheball' ),
			'placeholder' => __( 'e.g. 75% superwash merino, 25% nylon', 'overtheball' ),
		),
		'yardage'      => array(
			'label' => __( 'Yards per skein', 'overtheball' ),
			'type'  => 'number',
		),
		'meters'       => array(
			'label' => __( 'Meters per skein', 'overtheball' ),
			'type'  => 'number',
			'desc'  => __( 'Leave blank to calculate from yards.', 'overtheball' ),
		),
		'skein_weight' => array(
			'label'       => __( 'Skein weight', 'overtheball' ),
			'placeholder' => __( 'e.g. 100 g', 'overtheball' ),
		),
		'gauge'        => array(
			'label'       => __( 'Gauge', 'overtheball' ),
			'placeholder' => __( 'e.g. 28 sts = 4 in / 10 cm', 'overtheball' ),
		),
		'needle'       => array(
			'label'       => __( 'Needle size', 'overtheball' ),
			'placeholder' => __( 'e.g. US 1–3 (2.25–3.25 mm)', 'overtheball' ),
		),
		'hook'         => array(
			'label'       => __( 'Hook size', 'overtheball' ),
			'placeholder' => __( 'e.g. B-1 to E-4 (2.25–3.5 mm)', 'overtheball' ),
		),
		'care'         => array(
			'label'       => __( 'Care', 'overtheball' ),
			'placeholder' => __( 'e.g. Machine wash cool, lay flat to dry', 'overtheball' ),
		),
		'origin'       => array(
			'label'       => __( 'Origin', 'overtheball' ),
			'placeholder' => __( 'e.g. Hand-dyed in small batches', 'overtheball' ),
		),
		'dye_lot'      => array(
			'label' => __( 'Show dye lot notice', 'overtheball' ),
			'type'  => 'checkbox',
			'desc'  => __( 'Reminds shoppers to buy enough from one lot (text set in the Customizer).', 'overtheball' ),
		),
	);
}

/**
 * Add the "Yarn Details" tab to the product data box.
 *
 * @param array $tabs Tabs.
 * @return array
 */
function otb_yarn_product_data_tab( $tabs ) {
	$tabs['otb_yarn'] = array(
		'label'    => __( 'Yarn Details', 'overtheball' ),
		'target'   => 'otb_yarn_data',
		'class'    => array(),
		'priority' => 25,
	);
	return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'otb_yarn_product_data_tab' );

/**
 * Render the "Yarn Details" panel.
 */
function otb_yarn_product_data_panel() {
	global $product_object;
	echo '<div id="otb_yarn_data" class="panel woocommerce_options_panel hidden"><div class="options_group">';
	foreach ( otb_yarn_fields() as $key => $field ) {
		$id    = '_otb_' . $key;
		$type  = isset( $field['type'] ) ? $field['type'] : 'text';
		$value = $product_object ? $product_object->get_meta( $id ) : '';
		$args  = array(
			'id'          => $id,
			'label'       => $field['label'],
			'value'       => $value,
			'desc_tip'    => ! empty( $field['desc'] ),
			'description' => isset( $field['desc'] ) ? $field['desc'] : '',
			'placeholder' => isset( $field['placeholder'] ) ? $field['placeholder'] : '',
		);

		if ( 'select' === $type ) {
			$args['options'] = $field['options'];
			woocommerce_wp_select( $args );
		} elseif ( 'checkbox' === $type ) {
			$args['value']   = $value ? 'yes' : 'no';
			$args['cbvalue'] = 'yes';
			woocommerce_wp_checkbox( $args );
		} else {
			if ( 'number' === $type ) {
				$args['type']              = 'number';
				$args['custom_attributes'] = array(
					'min'  => '0',
					'step' => 'any',
				);
			}
			woocommerce_wp_text_input( $args );
		}
	}
	echo '</div></div>';
}
add_action( 'woocommerce_product_data_panels', 'otb_yarn_product_data_panel' );

/**
 * Save the yarn fields (WooCommerce has already verified its nonce).
 *
 * @param WC_Product $product Product.
 */
function otb_yarn_save_product( $product ) {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- verified by WooCommerce before this hook.
	foreach ( otb_yarn_fields() as $key => $field ) {
		$id   = '_otb_' . $key;
		$type = isset( $field['type'] ) ? $field['type'] : 'text';
		$raw  = isset( $_POST[ $id ] ) ? sanitize_text_field( wp_unslash( $_POST[ $id ] ) ) : '';

		if ( 'checkbox' === $type ) {
			$value = 'yes' === $raw ? 'yes' : '';
		} elseif ( 'select' === $type ) {
			$value = array_key_exists( $raw, $field['options'] ) ? $raw : '';
		} elseif ( 'number' === $type ) {
			$value = '' === $raw ? '' : (string) max( 0, (float) $raw );
		} else {
			$value = $raw;
		}

		if ( '' === $value ) {
			$product->delete_meta_data( $id );
		} else {
			$product->update_meta_data( $id, $value );
		}
	}
	// phpcs:enable
}
add_action( 'woocommerce_admin_process_product_object', 'otb_yarn_save_product' );

/**
 * Display-ready yarn details for a product.
 *
 * @param WC_Product $product Product.
 * @return array key => [label, value].
 */
function otb_get_yarn_details( $product ) {
	if ( ! $product ) {
		return array();
	}
	$fields  = otb_yarn_fields();
	$weights = otb_yarn_weights();
	$out     = array();

	foreach ( $fields as $key => $field ) {
		if ( 'dye_lot' === $key ) {
			continue;
		}
		$value = trim( (string) $product->get_meta( '_otb_' . $key ) );

		if ( 'meters' === $key && '' === $value ) {
			$yards = (float) $product->get_meta( '_otb_yardage' );
			$value = $yards > 0 ? (string) round( $yards * 0.9144 ) : '';
		}
		if ( '' === $value ) {
			continue;
		}

		switch ( $key ) {
			case 'yarn_weight':
				if ( ! isset( $weights[ $value ] ) ) {
					continue 2;
				}
				/* translators: 1: weight name, 2: CYC weight number. */
				$value = sprintf( __( '%1$s (CYC %2$d)', 'overtheball' ), $weights[ $value ]['label'], $weights[ $value ]['cyc'] );
				break;
			case 'yardage':
				/* translators: %s: yards. */
				$value = sprintf( __( '%s yd', 'overtheball' ), wc_format_localized_decimal( $value ) );
				break;
			case 'meters':
				/* translators: %s: meters. */
				$value = sprintf( __( '%s m', 'overtheball' ), wc_format_localized_decimal( $value ) );
				break;
		}

		$out[ $key ] = array(
			'label' => 'yardage' === $key ? __( 'Length', 'overtheball' ) : $field['label'],
			'value' => $value,
		);
	}

	// Combine yards and meters into one row.
	if ( isset( $out['yardage'], $out['meters'] ) ) {
		$out['yardage']['value'] .= ' / ' . $out['meters']['value'];
		unset( $out['meters'] );
	}

	return $out;
}

/**
 * Add the "Yarn Details" tab on the product page.
 *
 * @param array $tabs Tabs.
 * @return array
 */
function otb_yarn_product_tab( $tabs ) {
	global $product;
	if ( otb_option( 'yarn_details' ) && otb_get_yarn_details( $product ) ) {
		$tabs['otb_yarn'] = array(
			'title'    => __( 'Yarn Details', 'overtheball' ),
			'priority' => 5,
			'callback' => 'otb_yarn_product_tab_content',
		);
	}
	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'otb_yarn_product_tab' );

/**
 * Yarn details table.
 */
function otb_yarn_product_tab_content() {
	global $product;
	$details = otb_get_yarn_details( $product );
	echo '<h2>' . esc_html__( 'Yarn Details', 'overtheball' ) . '</h2>';
	echo '<table class="otb-yarn-table"><tbody>';
	foreach ( $details as $key => $detail ) {
		printf( '<tr class="otb-yarn-table__%1$s"><th scope="row">%2$s</th><td>%3$s</td></tr>', esc_attr( $key ), esc_html( $detail['label'] ), esc_html( $detail['value'] ) );
	}
	echo '</tbody></table>';
}

/**
 * Spec chips under the price.
 */
function otb_yarn_summary_chips() {
	global $product;
	if ( ! otb_option( 'yarn_details' ) ) {
		return;
	}
	$details = otb_get_yarn_details( $product );
	$chips   = array(
		'yarn_weight' => 'yarn',
		'fiber'       => 'leaf',
		'yardage'     => 'returns',
		'needle'      => 'needles',
	);
	$html    = '';
	foreach ( $chips as $key => $icon ) {
		if ( isset( $details[ $key ] ) ) {
			$html .= '<li>' . otb_get_icon( $icon, array( 'size' => 16 ) ) . '<span><span class="screen-reader-text">' . esc_html( $details[ $key ]['label'] ) . ': </span>' . esc_html( $details[ $key ]['value'] ) . '</span></li>';
		}
	}
	if ( $html ) {
		echo '<ul class="otb-yarn-chips">' . $html . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	}
}
add_action( 'woocommerce_single_product_summary', 'otb_yarn_summary_chips', 15 );

/**
 * Dye lot reminder above the add-to-cart button.
 */
function otb_yarn_dye_lot_notice() {
	global $product;
	if ( ! $product || ! otb_option( 'yarn_details' ) || 'yes' !== $product->get_meta( '_otb_dye_lot' ) ) {
		return;
	}
	$text = otb_option( 'dye_lot_text' );
	if ( $text ) {
		echo '<p class="otb-dye-lot">' . otb_get_icon( 'info', array( 'size' => 18 ) ) . '<span>' . esc_html( $text ) . '</span></p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
	}
}
add_action( 'woocommerce_single_product_summary', 'otb_yarn_dye_lot_notice', 25 );

/**
 * "How many skeins do I need?" calculator.
 */
function otb_yarn_skein_calculator() {
	global $product;
	if ( ! $product || ! otb_option( 'yarn_details' ) || ! otb_option( 'yardage_calculator' ) ) {
		return;
	}
	$yards = (float) $product->get_meta( '_otb_yardage' );
	if ( $yards <= 0 ) {
		return;
	}
	$meters = (float) $product->get_meta( '_otb_meters' );
	$meters = $meters > 0 ? $meters : round( $yards * 0.9144 );
	$id     = 'otb-skein-calc-' . $product->get_id();
	?>
	<details class="otb-skein-calc" data-yards="<?php echo esc_attr( $yards ); ?>" data-meters="<?php echo esc_attr( $meters ); ?>">
		<summary><?php otb_icon( 'calculator', array( 'size' => 18 ) ); ?> <?php esc_html_e( 'How many skeins do I need?', 'overtheball' ); ?></summary>
		<div class="otb-skein-calc__body">
			<label for="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Your pattern calls for', 'overtheball' ); ?></label>
			<div class="otb-skein-calc__row">
				<input type="number" id="<?php echo esc_attr( $id ); ?>" min="0" step="1" inputmode="numeric" placeholder="1200">
				<select aria-label="<?php esc_attr_e( 'Unit', 'overtheball' ); ?>">
					<option value="yd"><?php esc_html_e( 'yards', 'overtheball' ); ?></option>
					<option value="m"><?php esc_html_e( 'meters', 'overtheball' ); ?></option>
				</select>
			</div>
			<p class="otb-skein-calc__result" aria-live="polite"></p>
			<p class="otb-skein-calc__note">
				<?php
				/* translators: 1: yards per skein, 2: meters per skein. */
				echo esc_html( sprintf( __( 'Each skein is about %1$s yd / %2$s m. Consider one extra for swatching.', 'overtheball' ), wc_format_localized_decimal( $yards ), wc_format_localized_decimal( $meters ) ) );
				?>
			</p>
		</div>
	</details>
	<?php
}
add_action( 'woocommerce_single_product_summary', 'otb_yarn_skein_calculator', 35 );

/**
 * Weight and length under the product title in loops (e.g. "Worsted · 220 yd").
 */
function otb_yarn_loop_meta() {
	global $product;
	if ( ! $product || ! otb_option( 'yarn_details' ) ) {
		return;
	}
	$parts   = array();
	$weights = otb_yarn_weights();
	$weight  = $product->get_meta( '_otb_yarn_weight' );
	if ( $weight && isset( $weights[ $weight ] ) ) {
		$parts[] = $weights[ $weight ]['label'];
	}
	$yards = (float) $product->get_meta( '_otb_yardage' );
	if ( $yards > 0 ) {
		/* translators: %s: yards. */
		$parts[] = sprintf( __( '%s yd', 'overtheball' ), wc_format_localized_decimal( $yards ) );
	}
	if ( $parts ) {
		echo '<span class="otb-card__yarn">' . esc_html( implode( ' · ', $parts ) ) . '</span>';
	}
}
add_action( 'woocommerce_shop_loop_item_title', 'otb_yarn_loop_meta', 15 );

/**
 * Currently requested yarn weight filter.
 *
 * @return string Weight slug or ''.
 */
function otb_requested_yarn_weight() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only filter.
	$weight = isset( $_GET['yarn_weight'] ) ? sanitize_key( wp_unslash( $_GET['yarn_weight'] ) ) : '';
	return isset( otb_yarn_weights()[ $weight ] ) ? $weight : '';
}

/**
 * Filter the shop by ?yarn_weight=slug.
 *
 * @param WP_Query $query Product query.
 */
function otb_filter_products_by_weight( $query ) {
	$weight = otb_requested_yarn_weight();
	if ( ! $weight ) {
		return;
	}
	$meta_query   = (array) $query->get( 'meta_query' );
	$meta_query[] = array(
		'key'   => '_otb_yarn_weight',
		'value' => $weight,
	);
	$query->set( 'meta_query', $meta_query );
}
add_action( 'woocommerce_product_query', 'otb_filter_products_by_weight' );

/**
 * "Showing: DK weight — clear" notice above filtered results.
 */
function otb_yarn_weight_filter_notice() {
	$weight = otb_requested_yarn_weight();
	if ( ! $weight ) {
		return;
	}
	$weights = otb_yarn_weights();
	printf(
		'<p class="otb-active-filter">%1$s <strong>%2$s</strong> <a class="chip" href="%3$s">%4$s %5$s</a></p>',
		esc_html__( 'Showing yarn weight:', 'overtheball' ),
		esc_html( $weights[ $weight ]['label'] ),
		esc_url( remove_query_arg( array( 'yarn_weight', 'paged' ) ) ),
		esc_html__( 'Clear', 'overtheball' ),
		otb_get_icon( 'close', array( 'size' => 14 ) ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
	);
}
add_action( 'woocommerce_before_shop_loop', 'otb_yarn_weight_filter_notice', 12 );
