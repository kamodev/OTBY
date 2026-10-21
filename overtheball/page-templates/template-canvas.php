<?php
/**
 * Template Name: Blank Canvas (no header or footer)
 * Template Post Type: page, post
 *
 * Only the page content, edge to edge. Ideal for landing pages built with
 * Elementor or the block editor.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="primary" class="site-main site-main--canvas">
	<?php
	while ( have_posts() ) {
		the_post();
		the_content();
	}
	?>
</main>
<?php
get_footer();
