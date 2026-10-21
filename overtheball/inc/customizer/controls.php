<?php
/**
 * Custom Customizer controls: toggle, range, heading, sortable list, gallery.
 *
 * Loaded only inside customize_register, after WP_Customize_Control exists.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * On/off switch.
 */
class OTB_Toggle_Control extends WP_Customize_Control {
	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'otb-toggle';

	/**
	 * Render the control.
	 */
	public function render_content() {
		?>
		<label class="otb-toggle">
			<span class="otb-toggle__text">
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php if ( $this->description ) : ?>
					<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
				<?php endif; ?>
			</span>
			<input type="checkbox" value="1" <?php $this->link(); ?> <?php checked( (bool) $this->value() ); ?>>
			<span class="otb-toggle__switch" aria-hidden="true"></span>
		</label>
		<?php
	}
}

/**
 * Slider with a numeric field beside it.
 */
class OTB_Range_Control extends WP_Customize_Control {
	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'otb-range';

	/**
	 * Unit label.
	 *
	 * @var string
	 */
	public $unit = '';

	/**
	 * Render the control.
	 */
	public function render_content() {
		$attrs = wp_parse_args(
			$this->input_attrs,
			array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			)
		);
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
			<?php endif; ?>
		</label>
		<div class="otb-range" data-setting="<?php echo esc_attr( $this->setting->id ); ?>">
			<input type="range" class="otb-range__slider" min="<?php echo esc_attr( $attrs['min'] ); ?>" max="<?php echo esc_attr( $attrs['max'] ); ?>" step="<?php echo esc_attr( $attrs['step'] ); ?>" value="<?php echo esc_attr( $this->value() ); ?>" aria-label="<?php echo esc_attr( $this->label ); ?>">
			<input type="number" class="otb-range__number" min="<?php echo esc_attr( $attrs['min'] ); ?>" max="<?php echo esc_attr( $attrs['max'] ); ?>" step="<?php echo esc_attr( $attrs['step'] ); ?>" value="<?php echo esc_attr( $this->value() ); ?>" aria-label="<?php echo esc_attr( $this->label ); ?>">
			<?php if ( $this->unit ) : ?>
				<span class="otb-range__unit"><?php echo esc_html( $this->unit ); ?></span>
			<?php endif; ?>
			<button type="button" class="button-link otb-range__reset" data-default="<?php echo esc_attr( $this->setting->default ); ?>" title="<?php esc_attr_e( 'Reset', 'overtheball' ); ?>"><span class="dashicons dashicons-image-rotate"></span><span class="screen-reader-text"><?php esc_html_e( 'Reset', 'overtheball' ); ?></span></button>
		</div>
		<?php
	}
}

/**
 * Sub-heading to group controls inside a section.
 */
class OTB_Heading_Control extends WP_Customize_Control {
	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'otb-heading';

	/**
	 * Render the control.
	 */
	public function render_content() {
		?>
		<h3 class="otb-customizer-heading"><?php echo esc_html( $this->label ); ?></h3>
		<?php if ( $this->description ) : ?>
			<p class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></p>
		<?php endif; ?>
		<?php
	}
}

/**
 * Drag-and-drop ordering of a fixed list of keys (stored comma separated).
 */
class OTB_Sortable_Control extends WP_Customize_Control {
	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'otb-sortable';

	/**
	 * Render the control.
	 */
	public function render_content() {
		$saved = array_filter( array_map( 'trim', explode( ',', (string) $this->value() ) ) );
		$keys  = array_values( array_unique( array_merge( array_intersect( $saved, array_keys( $this->choices ) ), array_keys( $this->choices ) ) ) );
		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php if ( $this->description ) : ?>
			<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
		<?php endif; ?>
		<ul class="otb-sortable" data-setting="<?php echo esc_attr( $this->setting->id ); ?>">
			<?php foreach ( $keys as $key ) : ?>
				<li class="otb-sortable__item" data-key="<?php echo esc_attr( $key ); ?>" tabindex="0">
					<span class="dashicons dashicons-menu" aria-hidden="true"></span>
					<span class="otb-sortable__label"><?php echo esc_html( $this->choices[ $key ] ); ?></span>
					<span class="otb-sortable__move">
						<button type="button" class="button-link otb-sortable__up" aria-label="<?php esc_attr_e( 'Move up', 'overtheball' ); ?>"><span class="dashicons dashicons-arrow-up-alt2"></span></button>
						<button type="button" class="button-link otb-sortable__down" aria-label="<?php esc_attr_e( 'Move down', 'overtheball' ); ?>"><span class="dashicons dashicons-arrow-down-alt2"></span></button>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}
}

/**
 * Multiple image picker (stores a comma separated list of attachment IDs).
 */
class OTB_Gallery_Control extends WP_Customize_Control {
	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'otb-gallery';

	/**
	 * Render the control.
	 */
	public function render_content() {
		$ids = otb_parse_id_list( $this->value() );
		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php if ( $this->description ) : ?>
			<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
		<?php endif; ?>
		<div class="otb-gallery-control" data-setting="<?php echo esc_attr( $this->setting->id ); ?>">
			<ul class="otb-gallery-control__thumbs">
				<?php foreach ( $ids as $id ) : ?>
					<?php $src = wp_get_attachment_image_url( $id, 'thumbnail' ); ?>
					<?php if ( $src ) : ?>
						<li data-id="<?php echo esc_attr( $id ); ?>"><img src="<?php echo esc_url( $src ); ?>" alt=""></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
			<p class="otb-gallery-control__empty"<?php echo $ids ? ' hidden' : ''; ?>><?php esc_html_e( 'Showing every picture imported from your previous theme.', 'overtheball' ); ?></p>
			<button type="button" class="button otb-gallery-control__select"><?php esc_html_e( 'Choose pictures', 'overtheball' ); ?></button>
			<button type="button" class="button-link otb-gallery-control__clear"<?php echo $ids ? '' : ' hidden'; ?>><?php esc_html_e( 'Clear', 'overtheball' ); ?></button>
		</div>
		<?php
	}
}
