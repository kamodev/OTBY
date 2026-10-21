<?php
/**
 * Homepage "page content" section: the static front page's own content,
 * or the latest posts when the front page shows posts.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

if ( 'page' === get_option( 'show_on_front' ) ) {
	while ( have_posts() ) {
		the_post();
		if ( '' === trim( (string) get_the_content() ) ) {
			continue;
		}
		?>
		<section class="otb-section otb-home-content">
			<div class="container">
				<div class="entry-content"><?php the_content(); ?></div>
			</div>
		</section>
		<?php
	}
	return;
}

if ( ! have_posts() || otb_section_enabled( 'blog' ) ) {
	return;
}
?>
<section class="otb-section otb-home-content">
	<div class="container">
		<div class="post-grid post-grid--3">
			<?php
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/content/card', null, array( 'heading' => 'h3' ) );
			}
			?>
		</div>
		<?php otb_pagination(); ?>
	</div>
</section>
