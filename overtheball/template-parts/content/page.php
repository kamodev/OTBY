<?php
/**
 * Page content.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--page' ); ?>>
	<?php if ( ! otb_show_page_header() && '1' !== otb_page_meta( 'hide_title' ) && ! otb_is_elementor_page() ) : ?>
		<header class="entry-header">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</header>
	<?php endif; ?>

	<div class="entry-content">
		<?php
		the_content();
		wp_link_pages(
			array(
				'before' => '<nav class="page-links">' . esc_html__( 'Pages:', 'overtheball' ),
				'after'  => '</nav>',
			)
		);
		?>
	</div>

	<?php if ( get_edit_post_link() ) : ?>
		<footer class="entry-footer">
			<?php otb_entry_footer(); ?>
		</footer>
	<?php endif; ?>
</article>
