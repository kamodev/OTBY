<?php
/**
 * Default values for every theme setting.
 *
 * Every Customizer setting reads its default from here so templates, the
 * Customizer and the import/export tool always agree.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * All theme setting defaults keyed by theme_mod name.
 *
 * @return array
 */
function otb_defaults() {
	static $defaults = null;

	if ( null !== $defaults ) {
		return $defaults;
	}

	$defaults = array(
		// Site identity.
		'logo_width'              => 200,
		'logo_width_mobile'       => 140,
		'logo_light'              => 0,

		// Layout.
		'site_layout'             => 'full',
		'boxed_bg'                => '#EFE9F7',
		'container_width'         => 1280,
		'radius'                  => 12,
		'button_shape'            => 'pill',
		'button_uppercase'        => true,
		'animations'              => true,
		'back_to_top'             => true,
		'sticky_sidebar'          => true,

		// Sidebars.
		'sidebar_blog'            => 'right',
		'sidebar_single'          => 'right',
		'sidebar_page'            => 'none',
		'sidebar_search'          => 'right',
		'sidebar_shop'            => 'left',
		'sidebar_product'         => 'none',

		// Page header & breadcrumbs.
		'page_header_enable'      => true,
		'page_header_style'       => 'tinted',
		'page_header_image'       => 0,
		'page_header_align'       => 'center',
		'breadcrumbs'             => true,

		// Brand colors.
		'color_primary'           => '#6623EB',
		'color_primary_dark'      => '#4B14B8',
		'color_accent'            => '#D9A441',
		'color_heading'           => '#1B1525',
		'color_text'              => '#463F52',
		'color_muted'             => '#756D82',
		'color_bg'                => '#FFFFFF',
		'color_surface'           => '#F6F2FE',
		'color_cream'             => '#FBF8F3',
		'color_border'            => '#E6DFF1',
		'color_sale'              => '#C8336E',

		// Header & footer colors.
		'color_announce_bg'       => '#6623EB',
		'color_announce_text'     => '#FFFFFF',
		'color_topbar_bg'         => '#1B1525',
		'color_topbar_text'       => '#E9E2F7',
		'color_header_bg'         => '#FFFFFF',
		'color_header_text'       => '#1B1525',
		'color_footer_bg'         => '#1B1525',
		'color_footer_text'       => '#CBC3D8',
		'color_footer_heading'    => '#FFFFFF',
		'color_footer_link_hover' => '#B794FF',

		// Typography.
		'font_body'               => 'Jost',
		'font_heading'            => 'Cormorant Garamond',
		'font_script'             => 'Allura',
		'font_size_base'          => 17,
		'heading_weight'          => '600',
		'h1_size'                 => 54,
		'h2_size'                 => 40,
		'nav_uppercase'           => true,
		'nav_letter_spacing'      => 6,
		'script_accents'          => true,
		'google_fonts'            => true,

		// Announcement bar.
		'announce_enable'         => true,
		'announce_messages'       => "New yarn and fiber arrivals are in the shop — come take a look!\nSign up for our newsletter to hear about restocks first.",
		'announce_link'           => '',
		'announce_dismissible'    => true,
		'announce_speed'          => 5,

		// Top bar.
		'topbar_enable'           => true,
		'topbar_left'             => 'text',
		'topbar_right'            => 'social',
		'topbar_text'             => 'Yarn & fiber for knitters, crocheters, weavers and spinners',
		'topbar_mobile'           => false,

		// Header.
		'header_layout'           => 'classic',
		'header_sticky'           => true,
		'header_shrink'           => true,
		'header_transparent_home' => false,
		'header_border'           => true,
		'header_padding'          => 18,
		'header_search'           => true,
		'header_search_style'     => 'overlay',
		'header_account'          => true,
		'header_cart'             => true,
		'header_cart_total'       => false,
		'cart_behavior'           => 'drawer',
		'mobile_menu_side'        => 'left',
		'mobile_breakpoint'       => 1024,

		// Footer.
		'newsletter_enable'       => true,
		'newsletter_eyebrow'      => 'Stay in the loop',
		'newsletter_title'        => 'Get first dibs on new yarn',
		'newsletter_text'         => 'New arrivals, restocks, pattern ideas and shop news, delivered to your inbox.',
		'newsletter_image'        => 0,
		'footer_columns'          => 4,
		'footer_column_layout'    => 'wide-first',
		'footer_brand'            => true,
		'footer_about'            => 'Over The Ball Yarns brings beautiful yarn and fiber to makers everywhere, from first stitches to heirloom pieces.',
		'footer_logo'             => 0,
		'copyright'               => '&copy; {year} {site}. All rights reserved.',
		'payment_icons'           => true,
		'payment_methods'         => 'Visa, Mastercard, Amex, Discover, PayPal, Apple Pay',
		'payment_image'           => 0,

		// Homepage.
		'home_sections'           => true,
		'home_order'              => 'hero,features,categories,products,weights,promo,about,testimonials,blog,gallery,newsletter,content',

		'hero_enable'             => true,
		'hero_height'             => 'large',
		'hero_align'              => 'left',
		'hero_overlay'            => 35,
		'hero_autoplay'           => 6,
		'hero_1_image'            => 0,
		'hero_1_eyebrow'          => 'Welcome to Over The Ball',
		'hero_1_title'            => 'Yarn & fiber you will love to work with',
		'hero_1_text'             => 'Hand-picked yarns, spinning fiber, kits and notions for knitters, crocheters and makers of every kind.',
		'hero_1_btn_text'         => 'Shop Yarn',
		'hero_1_btn_url'          => '',
		'hero_1_btn2_text'        => 'New Arrivals',
		'hero_1_btn2_url'         => '',
		'hero_2_image'            => 0,
		'hero_2_eyebrow'          => '',
		'hero_2_title'            => '',
		'hero_2_text'             => '',
		'hero_2_btn_text'         => '',
		'hero_2_btn_url'          => '',
		'hero_2_btn2_text'        => '',
		'hero_2_btn2_url'         => '',
		'hero_3_image'            => 0,
		'hero_3_eyebrow'          => '',
		'hero_3_title'            => '',
		'hero_3_text'             => '',
		'hero_3_btn_text'         => '',
		'hero_3_btn_url'          => '',
		'hero_3_btn2_text'        => '',
		'hero_3_btn2_url'         => '',

		'features_enable'         => true,
		'feature_1_icon'          => 'truck',
		'feature_1_title'         => 'Packed with care',
		'feature_1_text'          => 'Every order wrapped by hand',
		'feature_2_icon'          => 'yarn',
		'feature_2_title'         => 'Curated yarn & fiber',
		'feature_2_text'          => 'Chosen for softness, color and quality',
		'feature_3_icon'          => 'lock',
		'feature_3_title'         => 'Secure checkout',
		'feature_3_text'          => 'Shop safely with trusted payments',
		'feature_4_icon'          => 'heart',
		'feature_4_title'         => 'Maker support',
		'feature_4_text'          => 'Questions? We are happy to help',

		'categories_enable'       => true,
		'categories_eyebrow'      => 'Find your fiber',
		'categories_title'        => 'Shop by Category',
		'categories_ids'          => '',
		'categories_count'        => 6,
		'categories_style'        => 'tiles',

		'products_enable'         => true,
		'products_eyebrow'        => 'Just landed',
		'products_title'          => 'Fresh in the Shop',
		'products_tabs'           => true,
		'products_source'         => 'recent',
		'products_category'       => 0,
		'products_count'          => 8,
		'products_layout'         => 'carousel',

		'weights_enable'          => true,
		'weights_eyebrow'         => 'Every gauge, every project',
		'weights_title'           => 'Shop by Weight',
		'weights_source'          => 'theme',
		'weights_attribute'       => '',

		'promo_enable'            => true,
		'promo_image'             => 0,
		'promo_layout'            => 'image-left',
		'promo_eyebrow'           => 'Kits & bundles',
		'promo_title'             => 'Everything you need for your next make',
		'promo_text'              => 'Yarn, pattern and notions gathered together so you can cast on the moment your parcel arrives.',
		'promo_btn_text'          => 'Shop Kits',
		'promo_btn_url'           => '',

		'about_enable'            => true,
		'about_image'             => 0,
		'about_eyebrow'           => 'Our story',
		'about_title'             => 'Made for makers',
		'about_text'              => "Over The Ball Yarns is an online yarn and fiber shop for knitters, crocheters, weavers and spinners.\n\nWe love helping makers find the right yarn for the project in front of them, and the next one waiting on the needles.",
		'about_btn_text'          => 'Learn More',
		'about_btn_url'           => '',
		'about_signature'         => 'Happy making!',

		'testimonials_enable'     => true,
		'testimonials_eyebrow'    => 'Kind words',
		'testimonials_title'      => 'From Our Makers',
		'testimonial_1_quote'     => '',
		'testimonial_1_name'      => '',
		'testimonial_1_detail'    => '',
		'testimonial_2_quote'     => '',
		'testimonial_2_name'      => '',
		'testimonial_2_detail'    => '',
		'testimonial_3_quote'     => '',
		'testimonial_3_name'      => '',
		'testimonial_3_detail'    => '',

		'blog_enable'             => true,
		'blog_eyebrow'            => 'Patterns, tips & inspiration',
		'blog_title'              => 'From the Blog',
		'blog_count'              => 3,
		'blog_category'           => 0,

		'gallery_enable'          => true,
		'gallery_eyebrow'         => 'Follow along',
		'gallery_title'           => '#OverTheBallYarns',
		'gallery_ids'             => '',
		'gallery_limit'           => 0,
		'gallery_columns'         => 6,
		'gallery_link'            => '',

		'home_newsletter_enable'  => true,

		// Blog.
		'blog_layout'             => 'grid',
		'blog_columns'            => 3,
		'excerpt_length'          => 24,
		'read_more_text'          => 'Read More',
		'meta_date'               => true,
		'meta_author'             => true,
		'meta_categories'         => true,
		'meta_comments'           => true,
		'single_featured'         => true,
		'single_author_box'       => true,
		'single_related'          => true,
		'single_nav'              => true,
		'single_tags'             => true,
		'share_buttons'           => true,

		// WooCommerce.
		'shop_columns'            => 4,
		'shop_columns_mobile'     => 2,
		'shop_per_page'           => 12,
		'shop_filter_drawer'      => true,
		'card_hover_image'        => true,
		'card_show_category'      => true,
		'card_show_rating'        => true,
		'card_button'             => 'always',
		'sale_badge'              => 'percent',
		'new_badge_days'          => 30,
		'free_shipping_threshold' => 0,
		'yarn_details'            => true,
		'yardage_calculator'      => true,
		'dye_lot_text'            => 'Hand-dyed yarn varies from lot to lot. Order enough for your whole project in one go.',
		'sticky_add_to_cart'      => true,
		'related_count'           => 4,
		'gallery_zoom'            => true,
		'gallery_lightbox'        => true,
		'gallery_slider'          => true,
		'checkout_minimal'        => true,
		'catalog_mode'            => false,

		// Contact & social.
		'contact_phone'           => '',
		'contact_email'           => '',
		'contact_address'         => '',
		'contact_hours'           => '',
		'contact_map'             => false,
		'contact_form'            => 0,
		'mailpoet_form'           => 0,
		'newsletter_shortcode'    => '',
		'mailpoet_theme_styles'   => true,
		'social_facebook'         => '',
		'social_instagram'        => '',
		'social_pinterest'        => '',
		'social_ravelry'          => '',
		'social_youtube'          => '',
		'social_tiktok'           => '',
		'social_x'                => '',
		'social_etsy'             => '',
		'social_new_tab'          => true,

		// Performance.
		'disable_emojis'          => true,
		'preload_fonts'           => true,
		'cf7_conditional_assets'  => false,
	);

	return $defaults;
}

/**
 * Default value for a single setting.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function otb_default( $key ) {
	$defaults = otb_defaults();
	return array_key_exists( $key, $defaults ) ? $defaults[ $key ] : '';
}
