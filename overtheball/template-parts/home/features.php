<?php
/**
 * Feature highlights strip (shipping, quality, checkout, support…).
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

$otb_items = array();
for ( $otb_i = 1; $otb_i <= 4; $otb_i++ ) {
	$otb_title = otb_option( "feature_{$otb_i}_title" );
	if ( $otb_title ) {
		$otb_items[] = array(
			'icon'  => otb_option( "feature_{$otb_i}_icon" ),
			'title' => $otb_title,
			'text'  => otb_option( "feature_{$otb_i}_text" ),
		);
	}
}

if ( ! $otb_items ) {
	return;
}
?>
<section class="otb-features" aria-label="<?php esc_attr_e( 'Why shop with us', 'overtheball' ); ?>">
	<div class="container">
		<ul class="otb-features__list otb-features__list--<?php echo count( $otb_items ); ?>">
			<?php foreach ( $otb_items as $otb_item ) : ?>
				<li class="otb-feature reveal">
					<span class="otb-feature__icon"><?php otb_icon( $otb_item['icon'], array( 'size' => 26 ) ); ?></span>
					<span class="otb-feature__copy">
						<strong class="otb-feature__title"><?php echo esc_html( $otb_item['title'] ); ?></strong>
						<?php if ( $otb_item['text'] ) : ?>
							<span class="otb-feature__text"><?php echo esc_html( $otb_item['text'] ); ?></span>
						<?php endif; ?>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
