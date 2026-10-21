<?php
/**
 * Latest blog posts.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

$otb_query_args = array(
	'posts_per_page'      => max( 1, absint( otb_option( 'blog_count' ) ) ),
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
);
if ( absint( otb_option( 'blog_category' ) ) ) {
	$otb_query_args['cat'] = absint( otb_option( 'blog_category' ) );
}

$otb_posts = new WP_Query( $otb_query_args );
if ( ! $otb_posts->have_posts() ) {
	return;
}

$otb_blog_page = (int) get_option( 'page_for_posts' );
?>
<section class="otb-section otb-home-blog" aria-labelledby="otb-blog-title">
	<div class="container">
		<?php
		otb_section_heading(
			array(
				'eyebrow'   => otb_option( 'blog_eyebrow' ),
				'title'     => otb_option( 'blog_title' ),
				'id'        => 'otb-blog-title',
				'link_text' => $otb_blog_page ? __( 'All posts', 'overtheball' ) : '',
				'link_url'  => $otb_blog_page ? get_permalink( $otb_blog_page ) : '',
			)
		);
		?>
		<div class="post-grid post-grid--3">
			<?php
			while ( $otb_posts->have_posts() ) {
				$otb_posts->the_post();
				get_template_part( 'template-parts/content/card', null, array( 'heading' => 'h3' ) );
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
