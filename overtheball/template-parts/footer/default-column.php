<?php
/**
 * Default footer column content, shown until footer widgets are added.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

$otb_column = isset( $args['column'] ) ? (int) $args['column'] : 1;

switch ( $otb_column ) {
	case 2:
		echo '<section class="widget"><h2 class="widget-title footer-widget-title">' . esc_html__( 'Shop', 'overtheball' ) . '</h2><ul>';
		if ( otb_is_woocommerce_active() ) {
			printf( '<li><a href="%s">%s</a></li>', esc_url( otb_shop_url() ), esc_html__( 'All products', 'overtheball' ) );
			$otb_cats = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'parent'     => 0,
					'hide_empty' => true,
					'number'     => 6,
					'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
				)
			);
			if ( ! is_wp_error( $otb_cats ) ) {
				foreach ( $otb_cats as $otb_cat ) {
					printf( '<li><a href="%s">%s</a></li>', esc_url( get_term_link( $otb_cat ) ), esc_html( $otb_cat->name ) );
				}
			}
		} else {
			wp_list_pages(
				array(
					'title_li' => '',
					'depth'    => 1,
					'number'   => 6,
				)
			);
		}
		echo '</ul></section>';
		break;

	case 3:
		echo '<section class="widget"><h2 class="widget-title footer-widget-title">' . esc_html__( 'Customer Care', 'overtheball' ) . '</h2>';
		if ( has_nav_menu( 'footer' ) ) {
			otb_nav_menu(
				'footer',
				array(
					'depth'       => 1,
					'otb_toggles' => false,
				)
			);
		} else {
			echo '<ul>';
			if ( otb_is_woocommerce_active() ) {
				printf( '<li><a href="%s">%s</a></li>', esc_url( wc_get_page_permalink( 'myaccount' ) ), esc_html__( 'My account', 'overtheball' ) );
				printf( '<li><a href="%s">%s</a></li>', esc_url( wc_get_cart_url() ), esc_html__( 'Cart', 'overtheball' ) );
			}
			$otb_privacy = (int) get_option( 'wp_page_for_privacy_policy' );
			if ( $otb_privacy && 'publish' === get_post_status( $otb_privacy ) ) {
				printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink( $otb_privacy ) ), esc_html( get_the_title( $otb_privacy ) ) );
			}
			$otb_contact = get_pages(
				array(
					'meta_key'   => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'meta_value' => 'page-templates/template-contact.php', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
					'number'     => 1,
				)
			);
			if ( $otb_contact ) {
				printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink( $otb_contact[0] ) ), esc_html( get_the_title( $otb_contact[0] ) ) );
			}
			echo '</ul>';
		}
		echo '</section>';
		break;

	case 4:
		echo '<section class="widget"><h2 class="widget-title footer-widget-title">' . esc_html__( 'Get in Touch', 'overtheball' ) . '</h2>';
		otb_contact_list( array( 'class' => 'otb-contact--footer' ) );
		if ( ! otb_option( 'footer_brand' ) ) {
			otb_social_links( 'otb-social--footer' );
		}
		if ( ! otb_option( 'contact_email' ) && ! otb_option( 'contact_phone' ) && current_user_can( 'edit_theme_options' ) ) {
			printf(
				'<p class="otb-admin-hint"><a href="%s">%s</a></p>',
				esc_url( admin_url( 'customize.php?autofocus[section]=otb_contact_info' ) ),
				esc_html__( 'Add your phone, email and address', 'overtheball' )
			);
		}
		echo '</section>';
		break;

	default:
		if ( ! otb_option( 'footer_brand' ) && otb_option( 'footer_about' ) ) {
			echo '<p class="footer-brand__about">' . wp_kses_post( otb_option( 'footer_about' ) ) . '</p>';
		}
}
