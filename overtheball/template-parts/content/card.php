<?php
/**
 * Post card used in grids, lists, related posts and the homepage.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

$otb_heading = isset( $args['heading'] ) ? $args['heading'] : 'h2';
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card reveal' ); ?>>
	<a class="post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'otb-card', array( 'alt' => '' ) );
		} else {
			otb_placeholder_art( 'post-card__placeholder' );
		}
		?>
	</a>
	<div class="post-card__body">
		<?php if ( 'post' === get_post_type() && otb_option( 'meta_categories' ) ) : ?>
			<?php $otb_cat = get_the_category(); ?>
			<?php if ( $otb_cat ) : ?>
				<a class="post-card__cat" href="<?php echo esc_url( get_category_link( $otb_cat[0] ) ); ?>"><?php echo esc_html( $otb_cat[0]->name ); ?></a>
			<?php endif; ?>
		<?php endif; ?>

		<<?php echo esc_attr( $otb_heading ); ?> class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></<?php echo esc_attr( $otb_heading ); ?>>

		<?php if ( has_excerpt() || get_the_content() ) : ?>
			<div class="post-card__excerpt"><?php the_excerpt(); ?></div>
		<?php endif; ?>

		<div class="post-card__foot">
			<?php
			if ( 'post' === get_post_type() && otb_option( 'meta_date' ) ) {
				otb_posted_on();
			}
			?>
			<a class="post-card__more" href="<?php the_permalink(); ?>">
				<?php echo esc_html( otb_option( 'read_more_text' ) ); ?>
				<span class="screen-reader-text"><?php the_title(); ?></span>
				<?php otb_icon( 'arrow-right', array( 'size' => 16 ) ); ?>
			</a>
		</div>
	</div>
</article>
