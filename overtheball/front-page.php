<?php
/**
 * Front page: configurable homepage sections, or the normal page/blog
 * template when sections are switched off (or the page is built with Elementor).
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

if ( ! otb_home_sections_active() ) {
	include 'page' === get_option( 'show_on_front' ) ? get_page_template() : get_index_template();
	return;
}

get_header();
?>
<main id="primary" class="site-main site-main--home">
	<?php
	foreach ( otb_home_section_order() as $otb_section ) {
		if ( otb_section_enabled( $otb_section ) ) {
			get_template_part( 'template-parts/home/' . $otb_section );
		}
	}
	?>
</main>
<?php
get_footer();
