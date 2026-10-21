<?php
/**
 * Classic (full-width) blog entry.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--classic reveal' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="entry__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'otb-wide', array( 'alt' => '' ) ); ?>
		</a>
	<?php endif; ?>
	<header class="entry-header">
		<?php otb_entry_categories(); ?>
		<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
		<?php otb_entry_meta(); ?>
	</header>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div>
	<a class="btn btn--outline" href="<?php the_permalink(); ?>">
		<?php echo esc_html( otb_option( 'read_more_text' ) ); ?>
		<span class="screen-reader-text"><?php the_title(); ?></span>
	</a>
</article>
