<?php
/**
 * Template Name: Contact
 *
 * Contact details, a Contact Form 7 form and an optional map. The page's
 * own content appears above the form.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part( 'template-parts/page-header' );

$otb_form_id = absint( otb_option( 'contact_form' ) );
$otb_address = trim( (string) otb_option( 'contact_address' ) );
?>
<div class="container content-wrap content-wrap--sidebar-none">
	<main id="primary" class="site-main site-main--contact">
		<div class="otb-contact-page">
			<aside class="otb-contact-page__info">
				<p class="eyebrow"><?php esc_html_e( 'Say hello', 'overtheball' ); ?></p>
				<h2 class="otb-contact-page__title"><?php esc_html_e( 'We would love to hear from you', 'overtheball' ); ?></h2>
				<?php
				otb_contact_list( array( 'class' => 'otb-contact--cards' ) );
				otb_social_links( 'otb-social--contact' );
				?>
			</aside>
			<div class="otb-contact-page__form">
				<?php
				while ( have_posts() ) {
					the_post();
					if ( '' !== trim( (string) get_the_content() ) ) {
						echo '<div class="entry-content">';
						the_content();
						echo '</div>';
					}
				}

				if ( $otb_form_id && defined( 'WPCF7_VERSION' ) ) {
					echo do_shortcode( '[contact-form-7 id="' . $otb_form_id . '"]' );
				} elseif ( current_user_can( 'edit_theme_options' ) ) {
					printf(
						'<p class="otb-admin-hint">%s <a href="%s">%s</a></p>',
						esc_html__( 'Install Contact Form 7 and choose a form to show it here (or add the form shortcode to this page).', 'overtheball' ),
						esc_url( admin_url( 'customize.php?autofocus[section]=otb_contact_forms' ) ),
						esc_html__( 'Choose a form', 'overtheball' )
					);
				}
				?>
			</div>
		</div>

		<?php if ( otb_option( 'contact_map' ) && $otb_address ) : ?>
			<div class="otb-contact-page__map">
				<iframe title="<?php esc_attr_e( 'Map', 'overtheball' ); ?>" src="<?php echo esc_url( 'https://maps.google.com/maps?q=' . rawurlencode( $otb_address ) . '&output=embed' ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
			</div>
		<?php endif; ?>
	</main>
</div>
<?php
get_footer();
