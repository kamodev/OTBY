<?php
/**
 * Announcement bar with rotating, optionally dismissible messages.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

if ( ! otb_option( 'announce_enable' ) ) {
	return;
}

$otb_messages = array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) otb_option( 'announce_messages' ) ) ) ) );
if ( ! $otb_messages ) {
	return;
}

$otb_link        = otb_option( 'announce_link' );
$otb_dismissible = otb_option( 'announce_dismissible' );
?>
<div class="otb-announcement" data-key="<?php echo esc_attr( substr( md5( implode( '|', $otb_messages ) ), 0, 10 ) ); ?>" role="region" aria-label="<?php esc_attr_e( 'Announcements', 'overtheball' ); ?>">
	<div class="container otb-announcement__inner">
		<div class="otb-announcement__track" aria-live="polite">
			<?php foreach ( $otb_messages as $otb_i => $otb_message ) : ?>
				<p class="otb-announcement__msg<?php echo 0 === $otb_i ? ' is-active' : ''; ?>"<?php echo 0 === $otb_i ? '' : ' hidden'; ?>>
					<?php if ( $otb_link ) : ?>
						<a href="<?php echo esc_url( $otb_link ); ?>">
							<?php
							echo wp_kses(
								$otb_message,
								array(
									'strong' => array(),
									'em'     => array(),
								)
							);
							?>
						</a>
					<?php else : ?>
						<?php
						echo wp_kses(
							$otb_message,
							array(
								'strong' => array(),
								'em'     => array(),
								'a'      => array( 'href' => array() ),
							)
						);
						?>
					<?php endif; ?>
				</p>
			<?php endforeach; ?>
		</div>
		<?php if ( $otb_dismissible ) : ?>
			<button type="button" class="otb-announcement__close" aria-label="<?php esc_attr_e( 'Dismiss announcement', 'overtheball' ); ?>">
				<?php otb_icon( 'close', array( 'size' => 16 ) ); ?>
			</button>
		<?php endif; ?>
	</div>
</div>
