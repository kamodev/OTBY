<?php
/**
 * Site footer.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;
?>
	</div><!-- #site-content -->

	<?php
	if ( otb_show_footer() ) {
		if ( function_exists( 'hfe_footer_enabled' ) && hfe_footer_enabled() ) {
			hfe_render_footer();
		} elseif ( ! otb_elementor_location( 'footer' ) ) {
			get_template_part( 'template-parts/footer/site-footer' );
		}
	}
	?>
</div><!-- #page -->

<?php
if ( otb_show_header() ) {
	get_template_part( 'template-parts/header/mobile-drawer' );
	get_template_part( 'template-parts/header/search-overlay' );
}

/**
 * Fires after the page wrapper; used for drawers and sticky bars.
 */
do_action( 'otb_after_page' );

if ( otb_option( 'back_to_top' ) ) :
	?>
	<a href="#page" class="back-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'overtheball' ); ?>"><?php otb_icon( 'arrow-up', array( 'size' => 20 ) ); ?></a>
	<?php
endif;

wp_footer();
?>
</body>
</html>
