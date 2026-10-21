<?php
/**
 * Comments.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$otb_count = get_comments_number();
			printf(
				/* translators: 1: comment count, 2: post title. */
				esc_html( _n( '%1$s thought on “%2$s”', '%1$s thoughts on “%2$s”', $otb_count, 'overtheball' ) ),
				esc_html( number_format_i18n( $otb_count ) ),
				'<span>' . wp_kses_post( get_the_title() ) . '</span>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 56,
				)
			);
			?>
		</ol>

		<?php the_comments_navigation(); ?>

		<?php if ( ! comments_open() ) : ?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'overtheball' ); ?></p>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h2>',
			'class_submit'       => 'submit btn btn--primary',
		)
	);
	?>
</section>
