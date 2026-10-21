<?php
/**
 * Single post content.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--single' ); ?>>
	<?php if ( ! otb_show_page_header() ) : ?>
		<header class="entry-header">
			<?php otb_entry_categories(); ?>
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			<?php otb_entry_meta(); ?>
		</header>
	<?php endif; ?>

	<?php if ( has_post_thumbnail() && otb_option( 'single_featured' ) ) : ?>
		<figure class="entry-featured">
			<?php the_post_thumbnail( 'otb-wide', array( 'fetchpriority' => 'high' ) ); ?>
			<?php if ( get_the_post_thumbnail_caption() ) : ?>
				<figcaption><?php the_post_thumbnail_caption(); ?></figcaption>
			<?php endif; ?>
		</figure>
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

	<footer class="entry-footer">
		<?php otb_entry_footer(); ?>
		<?php otb_share_buttons(); ?>
	</footer>

	<?php otb_author_box(); ?>
</article>
