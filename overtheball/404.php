<?php
/**
 * 404 page.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part( 'template-parts/page-header' );
?>
<div class="container content-wrap content-wrap--sidebar-none">
	<main id="primary" class="site-main site-main--404">
		<section class="error-404">
			<?php otb_placeholder_art( 'error-404__art' ); ?>
			<p class="error-404__lead"><?php esc_html_e( 'This page seems to have rolled under the sofa. Try a search, or head somewhere cozy:', 'overtheball' ); ?></p>
			<?php get_template_part( 'template-parts/header/search-form' ); ?>
			<div class="error-404__actions">
				<?php
				otb_button( __( 'Back to home', 'overtheball' ), home_url( '/' ), 'primary' );
				if ( otb_is_woocommerce_active() ) {
					otb_button( __( 'Browse the shop', 'overtheball' ), otb_shop_url(), 'outline' );
				}
				?>
			</div>
		</section>
	</main>
</div>
<?php
get_footer();
