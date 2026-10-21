<?php
/**
 * Message when no posts match.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="no-results">
	<?php otb_placeholder_art( 'no-results__art' ); ?>
	<h2 class="no-results__title"><?php esc_html_e( 'Nothing found', 'overtheball' ); ?></h2>
	<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
		<p>
			<?php
			printf(
				/* translators: %s: new post link. */
				wp_kses_post( __( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'overtheball' ) ),
				esc_url( admin_url( 'post-new.php' ) )
			);
			?>
		</p>
	<?php elseif ( is_search() ) : ?>
		<p><?php esc_html_e( 'Sorry, nothing matched your search. Try different keywords.', 'overtheball' ); ?></p>
		<?php get_search_form(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'We could not find what you were looking for. Perhaps searching can help.', 'overtheball' ); ?></p>
		<?php get_search_form(); ?>
	<?php endif; ?>
</section>
