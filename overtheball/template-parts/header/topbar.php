<?php
/**
 * Utility bar above the header.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

if ( ! otb_option( 'topbar_enable' ) ) {
	return;
}

/**
 * Render one side of the top bar.
 *
 * @param string $type Content type.
 */
$otb_topbar_side = static function ( $type ) {
	switch ( $type ) {
		case 'text':
			$text = otb_option( 'topbar_text' );
			if ( $text ) {
				echo '<p class="topbar__text">' . wp_kses(
					$text,
					array(
						'a'      => array( 'href' => array() ),
						'strong' => array(),
						'em'     => array(),
					)
				) . '</p>';
			}
			break;
		case 'contact':
			otb_contact_list(
				array(
					'class'  => 'otb-contact--inline',
					'fields' => array( 'phone', 'email' ),
				)
			);
			break;
		case 'menu':
			otb_nav_menu(
				'topbar',
				array(
					'depth'       => 1,
					'otb_toggles' => false,
				)
			);
			break;
		case 'social':
			otb_social_links( 'otb-social--topbar' );
			break;
		case 'widget':
			if ( is_active_sidebar( 'topbar' ) ) {
				dynamic_sidebar( 'topbar' );
			}
			break;
	}
};

$otb_left  = otb_option( 'topbar_left' );
$otb_right = otb_option( 'topbar_right' );

if ( 'none' === $otb_left && 'none' === $otb_right ) {
	return;
}
?>
<div class="topbar<?php echo otb_option( 'topbar_mobile' ) ? '' : ' otb-desktop-only'; ?>">
	<div class="container topbar__inner">
		<div class="topbar__left"><?php $otb_topbar_side( $otb_left ); ?></div>
		<div class="topbar__right"><?php $otb_topbar_side( $otb_right ); ?></div>
	</div>
</div>
