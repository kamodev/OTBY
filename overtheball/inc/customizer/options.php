<?php
/**
 * Declarative map of every Customizer panel, section and setting.
 *
 * Field keys:
 *   section, type, label, description, choices, min, max, step, unit,
 *   transport ('postMessage' for live preview), css_var (live CSS variable),
 *   active (array of setting => required value for the control to show),
 *   partial (selector for selective refresh; renders via otb_partial_*).
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * Build the option map.
 *
 * @return array{panels: array, sections: array, fields: array}
 */
function otb_customizer_options() {
	$sidebar_choices = array(
		'right' => __( 'Right', 'overtheball' ),
		'left'  => __( 'Left', 'overtheball' ),
		'none'  => __( 'None (full width)', 'overtheball' ),
	);

	$topbar_choices = array(
		'text'    => __( 'Text', 'overtheball' ),
		'contact' => __( 'Phone & email', 'overtheball' ),
		'menu'    => __( 'Top Bar menu', 'overtheball' ),
		'social'  => __( 'Social icons', 'overtheball' ),
		'widget'  => __( 'Top Bar widget area', 'overtheball' ),
		'none'    => __( 'Nothing', 'overtheball' ),
	);

	$post_categories = array( 0 => __( 'All categories', 'overtheball' ) );
	foreach ( get_categories( array( 'hide_empty' => false ) ) as $category ) {
		$post_categories[ $category->term_id ] = $category->name;
	}

	$product_categories = array( 0 => __( '— None —', 'overtheball' ) );
	$attributes         = array( '' => __( '— Choose an attribute —', 'overtheball' ) );
	if ( otb_is_woocommerce_active() ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			)
		);
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$product_categories[ $term->term_id ] = $term->name;
			}
		}
		foreach ( wc_get_attribute_taxonomies() as $attribute ) {
			$attributes[ 'pa_' . $attribute->attribute_name ] = $attribute->attribute_label;
		}
	}

	$cf7_forms = array( 0 => __( '— None —', 'overtheball' ) );
	if ( post_type_exists( 'wpcf7_contact_form' ) ) {
		$forms = get_posts(
			array(
				'post_type'      => 'wpcf7_contact_form',
				'posts_per_page' => 100,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
		foreach ( $forms as $form ) {
			$cf7_forms[ $form->ID ] = $form->post_title;
		}
	}

	$mailpoet_forms = array( 0 => __( 'Automatic (first active form)', 'overtheball' ) );
	if ( function_exists( 'otb_mailpoet_forms' ) ) {
		$mailpoet_forms += otb_mailpoet_forms();
	}

	$icons   = otb_icon_choices();
	$weights = array(
		'400' => __( 'Regular (400)', 'overtheball' ),
		'500' => __( 'Medium (500)', 'overtheball' ),
		'600' => __( 'Semibold (600)', 'overtheball' ),
		'700' => __( 'Bold (700)', 'overtheball' ),
	);

	$panels = array(
		'otb_general'       => array( __( 'Theme: General & Layout', 'overtheball' ), 22 ),
		'otb_style'         => array( __( 'Theme: Colors & Fonts', 'overtheball' ), 24 ),
		'otb_header_panel'  => array( __( 'Theme: Header', 'overtheball' ), 26 ),
		'otb_footer_panel'  => array( __( 'Theme: Footer', 'overtheball' ), 28 ),
		'otb_home'          => array( __( 'Theme: Homepage', 'overtheball' ), 30 ),
		'otb_blog_panel'    => array( __( 'Theme: Blog', 'overtheball' ), 32 ),
		'otb_contact_panel' => array( __( 'Theme: Contact, Social & Forms', 'overtheball' ), 34 ),
	);

	$sections = array(
		'otb_layout'            => array( __( 'Site Layout', 'overtheball' ), 'otb_general' ),
		'otb_sidebars'          => array( __( 'Sidebars', 'overtheball' ), 'otb_general', __( 'A sidebar only appears when its widget area has widgets (Appearance → Widgets). Individual pages can override this in the "Page Layout" box.', 'overtheball' ) ),
		'otb_page_header'       => array( __( 'Page Title & Breadcrumbs', 'overtheball' ), 'otb_general' ),
		'otb_performance'       => array( __( 'Performance', 'overtheball' ), 'otb_general' ),

		'otb_colors_brand'      => array( __( 'Brand Colors', 'overtheball' ), 'otb_style', __( 'Defaults are taken from the Over The Ball Yarns logo: violet yarn and ink-black script.', 'overtheball' ) ),
		'otb_colors_regions'    => array( __( 'Header & Footer Colors', 'overtheball' ), 'otb_style' ),
		'otb_typography'        => array( __( 'Typography', 'overtheball' ), 'otb_style', __( 'Jost, Cormorant Garamond and Allura are bundled with the theme. Other fonts load from Google Fonts unless that is turned off.', 'overtheball' ) ),

		'otb_announcement'      => array( __( 'Announcement Bar', 'overtheball' ), 'otb_header_panel' ),
		'otb_topbar'            => array( __( 'Top Bar', 'overtheball' ), 'otb_header_panel' ),
		'otb_header_layout'     => array( __( 'Header Layout & Behavior', 'overtheball' ), 'otb_header_panel' ),
		'otb_header_icons'      => array( __( 'Header Icons, Search & Cart', 'overtheball' ), 'otb_header_panel' ),
		'otb_mobile_menu'       => array( __( 'Mobile Menu', 'overtheball' ), 'otb_header_panel' ),

		'otb_newsletter'        => array( __( 'Newsletter Strip', 'overtheball' ), 'otb_footer_panel', __( 'Uses the MailPoet form chosen under Theme: Contact, Social & Forms.', 'overtheball' ) ),
		'otb_footer_widgets'    => array( __( 'Footer Columns', 'overtheball' ), 'otb_footer_panel', __( 'Add widgets to Footer Column 1–4. Until you do, the footer shows shop categories, customer care links and contact details.', 'overtheball' ) ),
		'otb_footer_bottom'     => array( __( 'Copyright & Payments', 'overtheball' ), 'otb_footer_panel' ),

		'otb_home_general'      => array( __( 'Sections & Order', 'overtheball' ), 'otb_home', __( 'Shown when the homepage is set to your latest posts or a static page (Settings → Reading). Pages built with Elementor show their own content instead.', 'overtheball' ) ),
		'otb_home_hero'         => array( __( 'Hero Slider', 'overtheball' ), 'otb_home' ),
		'otb_home_features'     => array( __( 'Feature Highlights', 'overtheball' ), 'otb_home' ),
		'otb_home_categories'   => array( __( 'Shop by Category', 'overtheball' ), 'otb_home' ),
		'otb_home_products'     => array( __( 'Products', 'overtheball' ), 'otb_home' ),
		'otb_home_weights'      => array( __( 'Shop by Weight', 'overtheball' ), 'otb_home' ),
		'otb_home_promo'        => array( __( 'Promo Banner', 'overtheball' ), 'otb_home' ),
		'otb_home_about'        => array( __( 'About / Story', 'overtheball' ), 'otb_home' ),
		'otb_home_testimonials' => array( __( 'Testimonials', 'overtheball' ), 'otb_home', __( 'The section stays hidden until at least one quote is entered.', 'overtheball' ) ),
		'otb_home_blog'         => array( __( 'Latest Posts', 'overtheball' ), 'otb_home' ),
		'otb_home_gallery'      => array( __( 'Picture Gallery', 'overtheball' ), 'otb_home', __( 'Leave the picture list empty to show every picture imported from your previous theme.', 'overtheball' ) ),

		'otb_blog_archive'      => array( __( 'Blog & Archives', 'overtheball' ), 'otb_blog_panel' ),
		'otb_blog_single'       => array( __( 'Single Post', 'overtheball' ), 'otb_blog_panel' ),

		'otb_contact_info'      => array( __( 'Contact Details', 'overtheball' ), 'otb_contact_panel', __( 'Used in the top bar, footer, mobile menu and Contact page template.', 'overtheball' ) ),
		'otb_social'            => array( __( 'Social Profiles', 'overtheball' ), 'otb_contact_panel' ),
		'otb_contact_forms'     => array( __( 'Forms (Contact Form 7 & MailPoet)', 'overtheball' ), 'otb_contact_panel' ),
	);

	if ( otb_is_woocommerce_active() ) {
		$sections += array(
			'otb_shop_layout'    => array( __( 'Theme: Shop Layout', 'overtheball' ), 'woocommerce' ),
			'otb_product_cards'  => array( __( 'Theme: Product Cards', 'overtheball' ), 'woocommerce' ),
			'otb_single_product' => array( __( 'Theme: Single Product & Yarn Details', 'overtheball' ), 'woocommerce' ),
			'otb_cart_checkout'  => array( __( 'Theme: Cart & Checkout', 'overtheball' ), 'woocommerce' ),
		);
	}

	$fields = array(
		// Site identity additions.
		'logo_width'              => array(
			'section'   => 'title_tagline',
			'type'      => 'range',
			'label'     => __( 'Logo width (desktop)', 'overtheball' ),
			'min'       => 80,
			'max'       => 420,
			'unit'      => 'px',
			'transport' => 'postMessage',
			'css_var'   => '--otb-logo-w',
		),
		'logo_width_mobile'       => array(
			'section'   => 'title_tagline',
			'type'      => 'range',
			'label'     => __( 'Logo width (mobile)', 'overtheball' ),
			'min'       => 60,
			'max'       => 260,
			'unit'      => 'px',
			'transport' => 'postMessage',
			'css_var'   => '--otb-logo-w-m',
		),
		'logo_light'              => array(
			'section'     => 'title_tagline',
			'type'        => 'image',
			'label'       => __( 'Light logo', 'overtheball' ),
			'description' => __( 'Used on the transparent header and dark footer. Defaults to a white version of the Over The Ball Yarns logo.', 'overtheball' ),
		),

		// Layout.
		'site_layout'             => array(
			'section' => 'otb_layout',
			'type'    => 'radio',
			'label'   => __( 'Site layout', 'overtheball' ),
			'choices' => array(
				'full'  => __( 'Full width', 'overtheball' ),
				'boxed' => __( 'Boxed', 'overtheball' ),
			),
		),
		'boxed_bg'                => array(
			'section'   => 'otb_layout',
			'type'      => 'color',
			'label'     => __( 'Boxed layout backdrop', 'overtheball' ),
			'active'    => array( 'site_layout' => 'boxed' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-boxed-bg',
		),
		'container_width'         => array(
			'section'   => 'otb_layout',
			'type'      => 'range',
			'label'     => __( 'Container width', 'overtheball' ),
			'min'       => 960,
			'max'       => 1680,
			'step'      => 10,
			'unit'      => 'px',
			'transport' => 'postMessage',
			'css_var'   => '--otb-container',
		),
		'radius'                  => array(
			'section'   => 'otb_layout',
			'type'      => 'range',
			'label'     => __( 'Corner radius (cards, images, inputs)', 'overtheball' ),
			'min'       => 0,
			'max'       => 32,
			'unit'      => 'px',
			'transport' => 'postMessage',
			'css_var'   => '--otb-radius',
		),
		'button_shape'            => array(
			'section' => 'otb_layout',
			'type'    => 'select',
			'label'   => __( 'Button shape', 'overtheball' ),
			'choices' => array(
				'pill'    => __( 'Pill', 'overtheball' ),
				'rounded' => __( 'Rounded', 'overtheball' ),
				'square'  => __( 'Square', 'overtheball' ),
			),
		),
		'button_uppercase'        => array(
			'section' => 'otb_layout',
			'type'    => 'toggle',
			'label'   => __( 'Uppercase button labels', 'overtheball' ),
		),
		'animations'              => array(
			'section'     => 'otb_layout',
			'type'        => 'toggle',
			'label'       => __( 'Fade-in animations on scroll', 'overtheball' ),
			'description' => __( 'Always disabled for visitors who prefer reduced motion.', 'overtheball' ),
		),
		'back_to_top'             => array(
			'section' => 'otb_layout',
			'type'    => 'toggle',
			'label'   => __( 'Back-to-top button', 'overtheball' ),
		),

		// Sidebars.
		'sidebar_blog'            => array(
			'section' => 'otb_sidebars',
			'type'    => 'select',
			'label'   => __( 'Blog & archives', 'overtheball' ),
			'choices' => $sidebar_choices,
		),
		'sidebar_single'          => array(
			'section' => 'otb_sidebars',
			'type'    => 'select',
			'label'   => __( 'Single posts', 'overtheball' ),
			'choices' => $sidebar_choices,
		),
		'sidebar_page'            => array(
			'section' => 'otb_sidebars',
			'type'    => 'select',
			'label'   => __( 'Pages', 'overtheball' ),
			'choices' => $sidebar_choices,
		),
		'sidebar_search'          => array(
			'section' => 'otb_sidebars',
			'type'    => 'select',
			'label'   => __( 'Search results', 'overtheball' ),
			'choices' => $sidebar_choices,
		),
		'sidebar_shop'            => array(
			'section' => 'otb_sidebars',
			'type'    => 'select',
			'label'   => __( 'Shop & product categories', 'overtheball' ),
			'choices' => $sidebar_choices,
		),
		'sidebar_product'         => array(
			'section' => 'otb_sidebars',
			'type'    => 'select',
			'label'   => __( 'Single products', 'overtheball' ),
			'choices' => $sidebar_choices,
		),
		'sticky_sidebar'          => array(
			'section' => 'otb_sidebars',
			'type'    => 'toggle',
			'label'   => __( 'Sticky sidebar while scrolling', 'overtheball' ),
		),

		// Page header.
		'page_header_enable'      => array(
			'section' => 'otb_page_header',
			'type'    => 'toggle',
			'label'   => __( 'Show title banner', 'overtheball' ),
		),
		'page_header_style'       => array(
			'section' => 'otb_page_header',
			'type'    => 'select',
			'label'   => __( 'Banner style', 'overtheball' ),
			'choices' => array(
				'tinted' => __( 'Lavender tint', 'overtheball' ),
				'plain'  => __( 'Plain', 'overtheball' ),
				'image'  => __( 'Background image', 'overtheball' ),
			),
			'active'  => array( 'page_header_enable' => true ),
		),
		'page_header_image'       => array(
			'section'     => 'otb_page_header',
			'type'        => 'image',
			'label'       => __( 'Banner image', 'overtheball' ),
			'description' => __( 'Pages with a featured image use that instead.', 'overtheball' ),
			'active'      => array( 'page_header_style' => 'image' ),
		),
		'page_header_align'       => array(
			'section' => 'otb_page_header',
			'type'    => 'radio',
			'label'   => __( 'Alignment', 'overtheball' ),
			'choices' => array(
				'center' => __( 'Center', 'overtheball' ),
				'left'   => __( 'Left', 'overtheball' ),
			),
			'active'  => array( 'page_header_enable' => true ),
		),
		'breadcrumbs'             => array(
			'section'     => 'otb_page_header',
			'type'        => 'toggle',
			'label'       => __( 'Show breadcrumbs', 'overtheball' ),
			'description' => __( 'Uses Yoast SEO, Rank Math or WooCommerce breadcrumbs when available.', 'overtheball' ),
		),

		// Performance.
		'disable_emojis'          => array(
			'section' => 'otb_performance',
			'type'    => 'toggle',
			'label'   => __( 'Remove WordPress emoji script', 'overtheball' ),
		),
		'preload_fonts'           => array(
			'section' => 'otb_performance',
			'type'    => 'toggle',
			'label'   => __( 'Preload bundled fonts', 'overtheball' ),
		),
		'cf7_conditional_assets'  => array(
			'section'     => 'otb_performance',
			'type'        => 'toggle',
			'label'       => __( 'Load Contact Form 7 files only where a form is used', 'overtheball' ),
			'description' => __( 'Detects forms in page content and the Contact template. Leave off if you place forms in widgets or Elementor templates.', 'overtheball' ),
		),

		// Brand colors.
		'color_primary'           => array(
			'section'   => 'otb_colors_brand',
			'type'      => 'color',
			'label'     => __( 'Primary (logo violet)', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-primary',
		),
		'color_primary_dark'      => array(
			'section'   => 'otb_colors_brand',
			'type'      => 'color',
			'label'     => __( 'Primary hover', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-primary-dark',
		),
		'color_accent'            => array(
			'section'   => 'otb_colors_brand',
			'type'      => 'color',
			'label'     => __( 'Accent (stars, highlights)', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-accent',
		),
		'color_heading'           => array(
			'section'   => 'otb_colors_brand',
			'type'      => 'color',
			'label'     => __( 'Headings (logo ink)', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-heading',
		),
		'color_text'              => array(
			'section'   => 'otb_colors_brand',
			'type'      => 'color',
			'label'     => __( 'Body text', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-text',
		),
		'color_muted'             => array(
			'section'   => 'otb_colors_brand',
			'type'      => 'color',
			'label'     => __( 'Muted text', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-muted',
		),
		'color_bg'                => array(
			'section'   => 'otb_colors_brand',
			'type'      => 'color',
			'label'     => __( 'Page background', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-bg',
		),
		'color_surface'           => array(
			'section'   => 'otb_colors_brand',
			'type'      => 'color',
			'label'     => __( 'Lavender surface', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-surface',
		),
		'color_cream'             => array(
			'section'   => 'otb_colors_brand',
			'type'      => 'color',
			'label'     => __( 'Cream surface', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-cream',
		),
		'color_border'            => array(
			'section'   => 'otb_colors_brand',
			'type'      => 'color',
			'label'     => __( 'Borders', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-border',
		),
		'color_sale'              => array(
			'section'   => 'otb_colors_brand',
			'type'      => 'color',
			'label'     => __( 'Sale badge', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-sale',
		),

		// Region colors.
		'color_announce_bg'       => array(
			'section'   => 'otb_colors_regions',
			'type'      => 'color',
			'label'     => __( 'Announcement bar background', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-announce-bg',
		),
		'color_announce_text'     => array(
			'section'   => 'otb_colors_regions',
			'type'      => 'color',
			'label'     => __( 'Announcement bar text', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-announce-text',
		),
		'color_topbar_bg'         => array(
			'section'   => 'otb_colors_regions',
			'type'      => 'color',
			'label'     => __( 'Top bar background', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-topbar-bg',
		),
		'color_topbar_text'       => array(
			'section'   => 'otb_colors_regions',
			'type'      => 'color',
			'label'     => __( 'Top bar text', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-topbar-text',
		),
		'color_header_bg'         => array(
			'section'   => 'otb_colors_regions',
			'type'      => 'color',
			'label'     => __( 'Header background', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-header-bg',
		),
		'color_header_text'       => array(
			'section'   => 'otb_colors_regions',
			'type'      => 'color',
			'label'     => __( 'Header text & icons', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-header-text',
		),
		'color_footer_bg'         => array(
			'section'   => 'otb_colors_regions',
			'type'      => 'color',
			'label'     => __( 'Footer background', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-footer-bg',
		),
		'color_footer_text'       => array(
			'section'   => 'otb_colors_regions',
			'type'      => 'color',
			'label'     => __( 'Footer text', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-footer-text',
		),
		'color_footer_heading'    => array(
			'section'   => 'otb_colors_regions',
			'type'      => 'color',
			'label'     => __( 'Footer headings', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-footer-heading',
		),
		'color_footer_link_hover' => array(
			'section'   => 'otb_colors_regions',
			'type'      => 'color',
			'label'     => __( 'Footer link hover', 'overtheball' ),
			'transport' => 'postMessage',
			'css_var'   => '--otb-footer-link-hover',
		),

		// Typography.
		'font_body'               => array(
			'section' => 'otb_typography',
			'type'    => 'select',
			'label'   => __( 'Body font', 'overtheball' ),
			'choices' => otb_font_choices( 'body' ),
		),
		'font_heading'            => array(
			'section' => 'otb_typography',
			'type'    => 'select',
			'label'   => __( 'Heading font', 'overtheball' ),
			'choices' => otb_font_choices( 'heading' ),
		),
		'font_script'             => array(
			'section'     => 'otb_typography',
			'type'        => 'select',
			'label'       => __( 'Script accent font', 'overtheball' ),
			'description' => __( 'Echoes the hand-lettered logo in small eyebrow titles.', 'overtheball' ),
			'choices'     => otb_font_choices( 'script' ),
		),
		'script_accents'          => array(
			'section' => 'otb_typography',
			'type'    => 'toggle',
			'label'   => __( 'Use the script font for eyebrow titles', 'overtheball' ),
		),
		'font_size_base'          => array(
			'section'   => 'otb_typography',
			'type'      => 'range',
			'label'     => __( 'Base font size', 'overtheball' ),
			'min'       => 14,
			'max'       => 21,
			'unit'      => 'px',
			'transport' => 'postMessage',
			'css_var'   => '--otb-fs-base',
		),
		'heading_weight'          => array(
			'section'   => 'otb_typography',
			'type'      => 'select',
			'label'     => __( 'Heading weight', 'overtheball' ),
			'choices'   => $weights,
			'transport' => 'postMessage',
			'css_var'   => '--otb-heading-weight',
		),
		'h1_size'                 => array(
			'section'   => 'otb_typography',
			'type'      => 'range',
			'label'     => __( 'Large title size (H1)', 'overtheball' ),
			'min'       => 30,
			'max'       => 88,
			'unit'      => 'px',
			'transport' => 'postMessage',
			'css_var'   => '--otb-h1',
		),
		'h2_size'                 => array(
			'section'   => 'otb_typography',
			'type'      => 'range',
			'label'     => __( 'Section title size (H2)', 'overtheball' ),
			'min'       => 24,
			'max'       => 64,
			'unit'      => 'px',
			'transport' => 'postMessage',
			'css_var'   => '--otb-h2',
		),
		'nav_uppercase'           => array(
			'section' => 'otb_typography',
			'type'    => 'toggle',
			'label'   => __( 'Uppercase menu links', 'overtheball' ),
		),
		'nav_letter_spacing'      => array(
			'section'   => 'otb_typography',
			'type'      => 'range',
			'label'     => __( 'Menu letter spacing', 'overtheball' ),
			'min'       => 0,
			'max'       => 20,
			'unit'      => '',
			'transport' => 'postMessage',
			'css_var'   => '--otb-nav-spacing',
			'css_unit'  => 'em',
			'css_scale' => 0.01,
		),
		'google_fonts'            => array(
			'section'     => 'otb_typography',
			'type'        => 'toggle',
			'label'       => __( 'Allow Google Fonts', 'overtheball' ),
			'description' => __( 'Turn off to keep every font request on your own server; non-bundled choices then fall back to system fonts.', 'overtheball' ),
		),

		// Announcement.
		'announce_enable'         => array(
			'section' => 'otb_announcement',
			'type'    => 'toggle',
			'label'   => __( 'Show announcement bar', 'overtheball' ),
		),
		'announce_messages'       => array(
			'section' => 'otb_announcement',
			'type'    => 'textarea',
			'label'   => __( 'Messages (one per line — they rotate)', 'overtheball' ),
			'active'  => array( 'announce_enable' => true ),
		),
		'announce_link'           => array(
			'section' => 'otb_announcement',
			'type'    => 'url',
			'label'   => __( 'Link (optional)', 'overtheball' ),
			'active'  => array( 'announce_enable' => true ),
		),
		'announce_speed'          => array(
			'section' => 'otb_announcement',
			'type'    => 'range',
			'label'   => __( 'Seconds per message', 'overtheball' ),
			'min'     => 2,
			'max'     => 15,
			'unit'    => 's',
			'active'  => array( 'announce_enable' => true ),
		),
		'announce_dismissible'    => array(
			'section' => 'otb_announcement',
			'type'    => 'toggle',
			'label'   => __( 'Visitors can dismiss it', 'overtheball' ),
			'active'  => array( 'announce_enable' => true ),
		),

		// Top bar.
		'topbar_enable'           => array(
			'section' => 'otb_topbar',
			'type'    => 'toggle',
			'label'   => __( 'Show top bar', 'overtheball' ),
		),
		'topbar_left'             => array(
			'section' => 'otb_topbar',
			'type'    => 'select',
			'label'   => __( 'Left side', 'overtheball' ),
			'choices' => $topbar_choices,
			'active'  => array( 'topbar_enable' => true ),
		),
		'topbar_right'            => array(
			'section' => 'otb_topbar',
			'type'    => 'select',
			'label'   => __( 'Right side', 'overtheball' ),
			'choices' => $topbar_choices,
			'active'  => array( 'topbar_enable' => true ),
		),
		'topbar_text'             => array(
			'section'   => 'otb_topbar',
			'type'      => 'html',
			'label'     => __( 'Top bar text', 'overtheball' ),
			'active'    => array( 'topbar_enable' => true ),
			'transport' => 'postMessage',
			'partial'   => '.topbar__text',
		),
		'topbar_mobile'           => array(
			'section' => 'otb_topbar',
			'type'    => 'toggle',
			'label'   => __( 'Show on mobile', 'overtheball' ),
			'active'  => array( 'topbar_enable' => true ),
		),

		// Header layout.
		'header_layout'           => array(
			'section' => 'otb_header_layout',
			'type'    => 'radio',
			'label'   => __( 'Header layout', 'overtheball' ),
			'choices' => array(
				'classic'  => __( 'Classic — logo left, menu beside it', 'overtheball' ),
				'centered' => __( 'Boutique — logo centered, menu below', 'overtheball' ),
				'shop'     => __( 'Shop — logo, wide search bar, menu below', 'overtheball' ),
			),
		),
		'header_padding'          => array(
			'section'   => 'otb_header_layout',
			'type'      => 'range',
			'label'     => __( 'Header vertical padding', 'overtheball' ),
			'min'       => 4,
			'max'       => 48,
			'unit'      => 'px',
			'transport' => 'postMessage',
			'css_var'   => '--otb-header-pad',
		),
		'header_sticky'           => array(
			'section' => 'otb_header_layout',
			'type'    => 'toggle',
			'label'   => __( 'Sticky header', 'overtheball' ),
		),
		'header_shrink'           => array(
			'section' => 'otb_header_layout',
			'type'    => 'toggle',
			'label'   => __( 'Shrink when scrolling', 'overtheball' ),
			'active'  => array( 'header_sticky' => true ),
		),
		'header_transparent_home' => array(
			'section'     => 'otb_header_layout',
			'type'        => 'toggle',
			'label'       => __( 'Transparent header over the homepage hero', 'overtheball' ),
			'description' => __( 'Works best when the first hero slide has a photo.', 'overtheball' ),
		),
		'header_border'           => array(
			'section' => 'otb_header_layout',
			'type'    => 'toggle',
			'label'   => __( 'Bottom border', 'overtheball' ),
		),

		// Header icons.
		'header_search'           => array(
			'section'     => 'otb_header_icons',
			'type'        => 'toggle',
			'label'       => __( 'Search', 'overtheball' ),
			'description' => __( 'Searches products when WooCommerce is active.', 'overtheball' ),
		),
		'header_account'          => array(
			'section' => 'otb_header_icons',
			'type'    => 'toggle',
			'label'   => __( 'Account icon (WooCommerce)', 'overtheball' ),
		),
		'header_cart'             => array(
			'section' => 'otb_header_icons',
			'type'    => 'toggle',
			'label'   => __( 'Cart icon (WooCommerce)', 'overtheball' ),
		),
		'header_cart_total'       => array(
			'section' => 'otb_header_icons',
			'type'    => 'toggle',
			'label'   => __( 'Show cart total beside the icon', 'overtheball' ),
			'active'  => array( 'header_cart' => true ),
		),
		'cart_behavior'           => array(
			'section' => 'otb_header_icons',
			'type'    => 'radio',
			'label'   => __( 'Cart icon opens', 'overtheball' ),
			'choices' => array(
				'drawer' => __( 'Slide-out mini cart', 'overtheball' ),
				'page'   => __( 'Cart page', 'overtheball' ),
			),
			'active'  => array( 'header_cart' => true ),
		),

		// Mobile menu.
		'mobile_menu_side'        => array(
			'section' => 'otb_mobile_menu',
			'type'    => 'radio',
			'label'   => __( 'Drawer slides in from', 'overtheball' ),
			'choices' => array(
				'left'  => __( 'Left', 'overtheball' ),
				'right' => __( 'Right', 'overtheball' ),
			),
		),
		'mobile_breakpoint'       => array(
			'section' => 'otb_mobile_menu',
			'type'    => 'range',
			'label'   => __( 'Switch to the mobile menu below', 'overtheball' ),
			'min'     => 640,
			'max'     => 1280,
			'step'    => 8,
			'unit'    => 'px',
		),

		// Newsletter strip.
		'newsletter_enable'       => array(
			'section' => 'otb_newsletter',
			'type'    => 'toggle',
			'label'   => __( 'Show newsletter strip above the footer', 'overtheball' ),
		),
		'newsletter_eyebrow'      => array(
			'section'   => 'otb_newsletter',
			'type'      => 'text',
			'label'     => __( 'Script eyebrow', 'overtheball' ),
			'transport' => 'postMessage',
			'partial'   => '.otb-newsletter .eyebrow',
		),
		'newsletter_title'        => array(
			'section'   => 'otb_newsletter',
			'type'      => 'text',
			'label'     => __( 'Title', 'overtheball' ),
			'transport' => 'postMessage',
			'partial'   => '.otb-newsletter__title',
		),
		'newsletter_text'         => array(
			'section'   => 'otb_newsletter',
			'type'      => 'textarea',
			'label'     => __( 'Text', 'overtheball' ),
			'transport' => 'postMessage',
			'partial'   => '.otb-newsletter__text',
		),
		'newsletter_image'        => array(
			'section' => 'otb_newsletter',
			'type'    => 'image',
			'label'   => __( 'Background picture (optional)', 'overtheball' ),
		),

		// Footer columns.
		'footer_columns'          => array(
			'section' => 'otb_footer_widgets',
			'type'    => 'select',
			'label'   => __( 'Columns', 'overtheball' ),
			'choices' => array(
				'0' => __( 'No widget columns', 'overtheball' ),
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
			),
		),
		'footer_column_layout'    => array(
			'section' => 'otb_footer_widgets',
			'type'    => 'select',
			'label'   => __( 'Column widths', 'overtheball' ),
			'choices' => array(
				'equal'      => __( 'Equal', 'overtheball' ),
				'wide-first' => __( 'Wide first column', 'overtheball' ),
				'wide-last'  => __( 'Wide last column', 'overtheball' ),
			),
		),
		'footer_brand'            => array(
			'section' => 'otb_footer_widgets',
			'type'    => 'toggle',
			'label'   => __( 'Logo, about text & social icons in column 1', 'overtheball' ),
		),
		'footer_logo'             => array(
			'section'     => 'otb_footer_widgets',
			'type'        => 'image',
			'label'       => __( 'Footer logo', 'overtheball' ),
			'description' => __( 'Defaults to the light logo.', 'overtheball' ),
			'active'      => array( 'footer_brand' => true ),
		),
		'footer_about'            => array(
			'section'   => 'otb_footer_widgets',
			'type'      => 'textarea',
			'label'     => __( 'About text', 'overtheball' ),
			'transport' => 'postMessage',
			'partial'   => '.footer-brand__about',
		),

		// Footer bottom.
		'copyright'               => array(
			'section'     => 'otb_footer_bottom',
			'type'        => 'html',
			'label'       => __( 'Copyright text', 'overtheball' ),
			'description' => __( 'Tokens: {year}, {site}, {copy}.', 'overtheball' ),
			'transport'   => 'postMessage',
			'partial'     => '.site-footer__copyright',
		),
		'payment_icons'           => array(
			'section' => 'otb_footer_bottom',
			'type'    => 'toggle',
			'label'   => __( 'Show accepted payment methods', 'overtheball' ),
		),
		'payment_methods'         => array(
			'section' => 'otb_footer_bottom',
			'type'    => 'text',
			'label'   => __( 'Payment methods (comma separated)', 'overtheball' ),
			'active'  => array( 'payment_icons' => true ),
		),
		'payment_image'           => array(
			'section' => 'otb_footer_bottom',
			'type'    => 'image',
			'label'   => __( 'Or upload a payment badges image', 'overtheball' ),
			'active'  => array( 'payment_icons' => true ),
		),

		// Homepage general.
		'home_sections'           => array(
			'section' => 'otb_home_general',
			'type'    => 'toggle',
			'label'   => __( 'Use the theme homepage sections', 'overtheball' ),
		),
		'home_order'              => array(
			'section'     => 'otb_home_general',
			'type'        => 'sortable',
			'label'       => __( 'Section order', 'overtheball' ),
			'description' => __( 'Drag to reorder. Turn sections on or off in their own panels.', 'overtheball' ),
			'choices'     => otb_home_section_labels(),
			'active'      => array( 'home_sections' => true ),
		),

		// Hero.
		'hero_enable'             => array(
			'section' => 'otb_home_hero',
			'type'    => 'toggle',
			'label'   => __( 'Show hero', 'overtheball' ),
		),
		'hero_height'             => array(
			'section' => 'otb_home_hero',
			'type'    => 'select',
			'label'   => __( 'Height', 'overtheball' ),
			'choices' => array(
				'large'  => __( 'Large', 'overtheball' ),
				'medium' => __( 'Medium', 'overtheball' ),
				'small'  => __( 'Compact', 'overtheball' ),
				'full'   => __( 'Full screen', 'overtheball' ),
			),
			'active'  => array( 'hero_enable' => true ),
		),
		'hero_align'              => array(
			'section' => 'otb_home_hero',
			'type'    => 'radio',
			'label'   => __( 'Text alignment', 'overtheball' ),
			'choices' => array(
				'left'   => __( 'Left', 'overtheball' ),
				'center' => __( 'Center', 'overtheball' ),
			),
			'active'  => array( 'hero_enable' => true ),
		),
		'hero_overlay'            => array(
			'section' => 'otb_home_hero',
			'type'    => 'range',
			'label'   => __( 'Photo darkening', 'overtheball' ),
			'min'     => 0,
			'max'     => 80,
			'unit'    => '%',
			'active'  => array( 'hero_enable' => true ),
		),
		'hero_autoplay'           => array(
			'section' => 'otb_home_hero',
			'type'    => 'range',
			'label'   => __( 'Autoplay seconds (0 = off)', 'overtheball' ),
			'min'     => 0,
			'max'     => 15,
			'unit'    => 's',
			'active'  => array( 'hero_enable' => true ),
		),
	);

	for ( $i = 1; $i <= 3; $i++ ) {
		$active                          = array( 'hero_enable' => true );
		$fields[ "heading_hero_{$i}" ]   = array(
			'section' => 'otb_home_hero',
			'type'    => 'heading',
			/* translators: %d: slide number. */
			'label'   => sprintf( __( 'Slide %d', 'overtheball' ), $i ),
			'active'  => $active,
		);
		$fields[ "hero_{$i}_image" ]     = array(
			'section' => 'otb_home_hero',
			'type'    => 'image',
			'label'   => __( 'Picture', 'overtheball' ),
			'active'  => $active,
		);
		$fields[ "hero_{$i}_eyebrow" ]   = array(
			'section' => 'otb_home_hero',
			'type'    => 'text',
			'label'   => __( 'Script eyebrow', 'overtheball' ),
			'active'  => $active,
		);
		$fields[ "hero_{$i}_title" ]     = array(
			'section'     => 'otb_home_hero',
			'type'        => 'text',
			'label'       => __( 'Title', 'overtheball' ),
			'description' => 1 === $i ? '' : __( 'A slide shows once it has a title or a picture.', 'overtheball' ),
			'active'      => $active,
		);
		$fields[ "hero_{$i}_text" ]      = array(
			'section' => 'otb_home_hero',
			'type'    => 'textarea',
			'label'   => __( 'Text', 'overtheball' ),
			'active'  => $active,
		);
		$fields[ "hero_{$i}_btn_text" ]  = array(
			'section' => 'otb_home_hero',
			'type'    => 'text',
			'label'   => __( 'Button label', 'overtheball' ),
			'active'  => $active,
		);
		$fields[ "hero_{$i}_btn_url" ]   = array(
			'section' => 'otb_home_hero',
			'type'    => 'url',
			'label'   => __( 'Button link (blank = shop)', 'overtheball' ),
			'active'  => $active,
		);
		$fields[ "hero_{$i}_btn2_text" ] = array(
			'section' => 'otb_home_hero',
			'type'    => 'text',
			'label'   => __( 'Second button label', 'overtheball' ),
			'active'  => $active,
		);
		$fields[ "hero_{$i}_btn2_url" ]  = array(
			'section' => 'otb_home_hero',
			'type'    => 'url',
			'label'   => __( 'Second button link (blank = newest products)', 'overtheball' ),
			'active'  => $active,
		);
	}

	$fields['features_enable'] = array(
		'section' => 'otb_home_features',
		'type'    => 'toggle',
		'label'   => __( 'Show feature highlights', 'overtheball' ),
	);
	for ( $i = 1; $i <= 4; $i++ ) {
		$active                           = array( 'features_enable' => true );
		$fields[ "heading_feature_{$i}" ] = array(
			'section' => 'otb_home_features',
			'type'    => 'heading',
			/* translators: %d: item number. */
			'label'   => sprintf( __( 'Highlight %d', 'overtheball' ), $i ),
			'active'  => $active,
		);
		$fields[ "feature_{$i}_icon" ]    = array(
			'section' => 'otb_home_features',
			'type'    => 'select',
			'label'   => __( 'Icon', 'overtheball' ),
			'choices' => $icons,
			'active'  => $active,
		);
		$fields[ "feature_{$i}_title" ]   = array(
			'section' => 'otb_home_features',
			'type'    => 'text',
			'label'   => __( 'Title (blank hides it)', 'overtheball' ),
			'active'  => $active,
		);
		$fields[ "feature_{$i}_text" ]    = array(
			'section' => 'otb_home_features',
			'type'    => 'text',
			'label'   => __( 'Text', 'overtheball' ),
			'active'  => $active,
		);
	}

	$fields += array(
		'categories_enable'    => array(
			'section'     => 'otb_home_categories',
			'type'        => 'toggle',
			'label'       => __( 'Show product categories', 'overtheball' ),
			'description' => __( 'Category pictures come from Products → Categories → Thumbnail.', 'overtheball' ),
		),
		'categories_eyebrow'   => array(
			'section' => 'otb_home_categories',
			'type'    => 'text',
			'label'   => __( 'Script eyebrow', 'overtheball' ),
			'active'  => array( 'categories_enable' => true ),
		),
		'categories_title'     => array(
			'section' => 'otb_home_categories',
			'type'    => 'text',
			'label'   => __( 'Title', 'overtheball' ),
			'active'  => array( 'categories_enable' => true ),
		),
		'categories_ids'       => array(
			'section'     => 'otb_home_categories',
			'type'        => 'text',
			'label'       => __( 'Category IDs to show (comma separated)', 'overtheball' ),
			'description' => __( 'Blank shows your top-level categories in their custom order.', 'overtheball' ),
			'active'      => array( 'categories_enable' => true ),
		),
		'categories_count'     => array(
			'section' => 'otb_home_categories',
			'type'    => 'range',
			'label'   => __( 'How many', 'overtheball' ),
			'min'     => 2,
			'max'     => 12,
			'active'  => array( 'categories_enable' => true ),
		),
		'categories_style'     => array(
			'section' => 'otb_home_categories',
			'type'    => 'radio',
			'label'   => __( 'Style', 'overtheball' ),
			'choices' => array(
				'tiles'   => __( 'Portrait tiles', 'overtheball' ),
				'circles' => __( 'Circles', 'overtheball' ),
			),
			'active'  => array( 'categories_enable' => true ),
		),

		'products_enable'      => array(
			'section' => 'otb_home_products',
			'type'    => 'toggle',
			'label'   => __( 'Show products', 'overtheball' ),
		),
		'products_eyebrow'     => array(
			'section' => 'otb_home_products',
			'type'    => 'text',
			'label'   => __( 'Script eyebrow', 'overtheball' ),
			'active'  => array( 'products_enable' => true ),
		),
		'products_title'       => array(
			'section' => 'otb_home_products',
			'type'    => 'text',
			'label'   => __( 'Title', 'overtheball' ),
			'active'  => array( 'products_enable' => true ),
		),
		'products_tabs'        => array(
			'section' => 'otb_home_products',
			'type'    => 'toggle',
			'label'   => __( 'Tabs: New Arrivals / Best Sellers / On Sale', 'overtheball' ),
			'active'  => array( 'products_enable' => true ),
		),
		'products_source'      => array(
			'section' => 'otb_home_products',
			'type'    => 'select',
			'label'   => __( 'Products to show', 'overtheball' ),
			'choices' => array(
				'recent'       => __( 'Newest', 'overtheball' ),
				'featured'     => __( 'Featured', 'overtheball' ),
				'best_selling' => __( 'Best selling', 'overtheball' ),
				'sale'         => __( 'On sale', 'overtheball' ),
				'top_rated'    => __( 'Top rated', 'overtheball' ),
				'category'     => __( 'From a category', 'overtheball' ),
			),
			'active'  => array( 'products_tabs' => false ),
		),
		'products_category'    => array(
			'section' => 'otb_home_products',
			'type'    => 'select',
			'label'   => __( 'Category', 'overtheball' ),
			'choices' => $product_categories,
			'active'  => array( 'products_source' => 'category' ),
		),
		'products_count'       => array(
			'section' => 'otb_home_products',
			'type'    => 'range',
			'label'   => __( 'How many', 'overtheball' ),
			'min'     => 2,
			'max'     => 16,
			'active'  => array( 'products_enable' => true ),
		),
		'products_layout'      => array(
			'section' => 'otb_home_products',
			'type'    => 'radio',
			'label'   => __( 'Layout', 'overtheball' ),
			'choices' => array(
				'carousel' => __( 'Carousel', 'overtheball' ),
				'grid'     => __( 'Grid', 'overtheball' ),
			),
			'active'  => array( 'products_enable' => true ),
		),

		'weights_enable'       => array(
			'section' => 'otb_home_weights',
			'type'    => 'toggle',
			'label'   => __( 'Show shop-by-weight', 'overtheball' ),
		),
		'weights_eyebrow'      => array(
			'section' => 'otb_home_weights',
			'type'    => 'text',
			'label'   => __( 'Script eyebrow', 'overtheball' ),
			'active'  => array( 'weights_enable' => true ),
		),
		'weights_title'        => array(
			'section' => 'otb_home_weights',
			'type'    => 'text',
			'label'   => __( 'Title', 'overtheball' ),
			'active'  => array( 'weights_enable' => true ),
		),
		'weights_source'       => array(
			'section' => 'otb_home_weights',
			'type'    => 'radio',
			'label'   => __( 'Weights come from', 'overtheball' ),
			'choices' => array(
				'theme'     => __( 'The "Yarn weight" field on each product (Yarn Details tab)', 'overtheball' ),
				'attribute' => __( 'A product attribute', 'overtheball' ),
			),
			'active'  => array( 'weights_enable' => true ),
		),
		'weights_attribute'    => array(
			'section' => 'otb_home_weights',
			'type'    => 'select',
			'label'   => __( 'Attribute', 'overtheball' ),
			'choices' => $attributes,
			'active'  => array( 'weights_source' => 'attribute' ),
		),

		'promo_enable'         => array(
			'section' => 'otb_home_promo',
			'type'    => 'toggle',
			'label'   => __( 'Show promo banner', 'overtheball' ),
		),
		'promo_image'          => array(
			'section' => 'otb_home_promo',
			'type'    => 'image',
			'label'   => __( 'Picture', 'overtheball' ),
			'active'  => array( 'promo_enable' => true ),
		),
		'promo_layout'         => array(
			'section' => 'otb_home_promo',
			'type'    => 'select',
			'label'   => __( 'Layout', 'overtheball' ),
			'choices' => array(
				'image-left'  => __( 'Picture left', 'overtheball' ),
				'image-right' => __( 'Picture right', 'overtheball' ),
				'background'  => __( 'Text over picture', 'overtheball' ),
			),
			'active'  => array( 'promo_enable' => true ),
		),
		'promo_eyebrow'        => array(
			'section' => 'otb_home_promo',
			'type'    => 'text',
			'label'   => __( 'Script eyebrow', 'overtheball' ),
			'active'  => array( 'promo_enable' => true ),
		),
		'promo_title'          => array(
			'section' => 'otb_home_promo',
			'type'    => 'text',
			'label'   => __( 'Title', 'overtheball' ),
			'active'  => array( 'promo_enable' => true ),
		),
		'promo_text'           => array(
			'section' => 'otb_home_promo',
			'type'    => 'textarea',
			'label'   => __( 'Text', 'overtheball' ),
			'active'  => array( 'promo_enable' => true ),
		),
		'promo_btn_text'       => array(
			'section' => 'otb_home_promo',
			'type'    => 'text',
			'label'   => __( 'Button label', 'overtheball' ),
			'active'  => array( 'promo_enable' => true ),
		),
		'promo_btn_url'        => array(
			'section' => 'otb_home_promo',
			'type'    => 'url',
			'label'   => __( 'Button link (blank = shop)', 'overtheball' ),
			'active'  => array( 'promo_enable' => true ),
		),

		'about_enable'         => array(
			'section' => 'otb_home_about',
			'type'    => 'toggle',
			'label'   => __( 'Show about section', 'overtheball' ),
		),
		'about_image'          => array(
			'section' => 'otb_home_about',
			'type'    => 'image',
			'label'   => __( 'Picture', 'overtheball' ),
			'active'  => array( 'about_enable' => true ),
		),
		'about_eyebrow'        => array(
			'section' => 'otb_home_about',
			'type'    => 'text',
			'label'   => __( 'Script eyebrow', 'overtheball' ),
			'active'  => array( 'about_enable' => true ),
		),
		'about_title'          => array(
			'section' => 'otb_home_about',
			'type'    => 'text',
			'label'   => __( 'Title', 'overtheball' ),
			'active'  => array( 'about_enable' => true ),
		),
		'about_text'           => array(
			'section' => 'otb_home_about',
			'type'    => 'textarea',
			'label'   => __( 'Text', 'overtheball' ),
			'active'  => array( 'about_enable' => true ),
		),
		'about_signature'      => array(
			'section' => 'otb_home_about',
			'type'    => 'text',
			'label'   => __( 'Script sign-off', 'overtheball' ),
			'active'  => array( 'about_enable' => true ),
		),
		'about_btn_text'       => array(
			'section' => 'otb_home_about',
			'type'    => 'text',
			'label'   => __( 'Button label', 'overtheball' ),
			'active'  => array( 'about_enable' => true ),
		),
		'about_btn_url'        => array(
			'section' => 'otb_home_about',
			'type'    => 'url',
			'label'   => __( 'Button link (blank = your "about" page)', 'overtheball' ),
			'active'  => array( 'about_enable' => true ),
		),

		'testimonials_enable'  => array(
			'section' => 'otb_home_testimonials',
			'type'    => 'toggle',
			'label'   => __( 'Show testimonials', 'overtheball' ),
		),
		'testimonials_eyebrow' => array(
			'section' => 'otb_home_testimonials',
			'type'    => 'text',
			'label'   => __( 'Script eyebrow', 'overtheball' ),
			'active'  => array( 'testimonials_enable' => true ),
		),
		'testimonials_title'   => array(
			'section' => 'otb_home_testimonials',
			'type'    => 'text',
			'label'   => __( 'Title', 'overtheball' ),
			'active'  => array( 'testimonials_enable' => true ),
		),
	);

	for ( $i = 1; $i <= 3; $i++ ) {
		$active                               = array( 'testimonials_enable' => true );
		$fields[ "heading_testimonial_{$i}" ] = array(
			'section' => 'otb_home_testimonials',
			'type'    => 'heading',
			/* translators: %d: testimonial number. */
			'label'   => sprintf( __( 'Testimonial %d', 'overtheball' ), $i ),
			'active'  => $active,
		);
		$fields[ "testimonial_{$i}_quote" ]   = array(
			'section' => 'otb_home_testimonials',
			'type'    => 'textarea',
			'label'   => __( 'Quote', 'overtheball' ),
			'active'  => $active,
		);
		$fields[ "testimonial_{$i}_name" ]    = array(
			'section' => 'otb_home_testimonials',
			'type'    => 'text',
			'label'   => __( 'Name', 'overtheball' ),
			'active'  => $active,
		);
		$fields[ "testimonial_{$i}_detail" ]  = array(
			'section' => 'otb_home_testimonials',
			'type'    => 'text',
			'label'   => __( 'Detail (e.g. location or project)', 'overtheball' ),
			'active'  => $active,
		);
	}

	$fields += array(
		'blog_enable'            => array(
			'section' => 'otb_home_blog',
			'type'    => 'toggle',
			'label'   => __( 'Show latest posts', 'overtheball' ),
		),
		'blog_eyebrow'           => array(
			'section' => 'otb_home_blog',
			'type'    => 'text',
			'label'   => __( 'Script eyebrow', 'overtheball' ),
			'active'  => array( 'blog_enable' => true ),
		),
		'blog_title'             => array(
			'section' => 'otb_home_blog',
			'type'    => 'text',
			'label'   => __( 'Title', 'overtheball' ),
			'active'  => array( 'blog_enable' => true ),
		),
		'blog_count'             => array(
			'section' => 'otb_home_blog',
			'type'    => 'range',
			'label'   => __( 'How many', 'overtheball' ),
			'min'     => 1,
			'max'     => 9,
			'active'  => array( 'blog_enable' => true ),
		),
		'blog_category'          => array(
			'section' => 'otb_home_blog',
			'type'    => 'select',
			'label'   => __( 'Category', 'overtheball' ),
			'choices' => $post_categories,
			'active'  => array( 'blog_enable' => true ),
		),

		'gallery_enable'         => array(
			'section' => 'otb_home_gallery',
			'type'    => 'toggle',
			'label'   => __( 'Show picture gallery', 'overtheball' ),
		),
		'gallery_eyebrow'        => array(
			'section' => 'otb_home_gallery',
			'type'    => 'text',
			'label'   => __( 'Script eyebrow', 'overtheball' ),
			'active'  => array( 'gallery_enable' => true ),
		),
		'gallery_title'          => array(
			'section' => 'otb_home_gallery',
			'type'    => 'text',
			'label'   => __( 'Title', 'overtheball' ),
			'active'  => array( 'gallery_enable' => true ),
		),
		'gallery_ids'            => array(
			'section' => 'otb_home_gallery',
			'type'    => 'gallery',
			'label'   => __( 'Pictures', 'overtheball' ),
			'active'  => array( 'gallery_enable' => true ),
		),
		'gallery_limit'          => array(
			'section' => 'otb_home_gallery',
			'type'    => 'number',
			'label'   => __( 'Maximum pictures (0 = all)', 'overtheball' ),
			'min'     => 0,
			'max'     => 200,
			'active'  => array( 'gallery_enable' => true ),
		),
		'gallery_columns'        => array(
			'section' => 'otb_home_gallery',
			'type'    => 'range',
			'label'   => __( 'Columns (desktop)', 'overtheball' ),
			'min'     => 2,
			'max'     => 8,
			'active'  => array( 'gallery_enable' => true ),
		),
		'gallery_link'           => array(
			'section' => 'otb_home_gallery',
			'type'    => 'url',
			'label'   => __( '"Follow us" link (blank = Instagram profile)', 'overtheball' ),
			'active'  => array( 'gallery_enable' => true ),
		),

		'home_newsletter_enable' => array(
			'section'     => 'otb_home_general',
			'type'        => 'toggle',
			'label'       => __( 'Newsletter section on the homepage', 'overtheball' ),
			'description' => __( 'Replaces the footer newsletter strip on the homepage.', 'overtheball' ),
			'active'      => array( 'home_sections' => true ),
		),

		// Blog.
		'blog_layout'            => array(
			'section' => 'otb_blog_archive',
			'type'    => 'radio',
			'label'   => __( 'Layout', 'overtheball' ),
			'choices' => array(
				'grid'    => __( 'Grid of cards', 'overtheball' ),
				'list'    => __( 'List', 'overtheball' ),
				'classic' => __( 'Classic (large image)', 'overtheball' ),
			),
		),
		'blog_columns'           => array(
			'section' => 'otb_blog_archive',
			'type'    => 'select',
			'label'   => __( 'Grid columns', 'overtheball' ),
			'choices' => array(
				'2' => '2',
				'3' => '3',
				'4' => '4',
			),
			'active'  => array( 'blog_layout' => 'grid' ),
		),
		'excerpt_length'         => array(
			'section' => 'otb_blog_archive',
			'type'    => 'range',
			'label'   => __( 'Excerpt length (words)', 'overtheball' ),
			'min'     => 5,
			'max'     => 80,
		),
		'read_more_text'         => array(
			'section' => 'otb_blog_archive',
			'type'    => 'text',
			'label'   => __( '"Read more" label', 'overtheball' ),
		),
		'meta_date'              => array(
			'section' => 'otb_blog_archive',
			'type'    => 'toggle',
			'label'   => __( 'Show date', 'overtheball' ),
		),
		'meta_author'            => array(
			'section' => 'otb_blog_archive',
			'type'    => 'toggle',
			'label'   => __( 'Show author', 'overtheball' ),
		),
		'meta_categories'        => array(
			'section' => 'otb_blog_archive',
			'type'    => 'toggle',
			'label'   => __( 'Show categories', 'overtheball' ),
		),
		'meta_comments'          => array(
			'section' => 'otb_blog_archive',
			'type'    => 'toggle',
			'label'   => __( 'Show comment count', 'overtheball' ),
		),

		'single_featured'        => array(
			'section' => 'otb_blog_single',
			'type'    => 'toggle',
			'label'   => __( 'Featured image', 'overtheball' ),
		),
		'single_tags'            => array(
			'section' => 'otb_blog_single',
			'type'    => 'toggle',
			'label'   => __( 'Tags', 'overtheball' ),
		),
		'share_buttons'          => array(
			'section' => 'otb_blog_single',
			'type'    => 'toggle',
			'label'   => __( 'Share links', 'overtheball' ),
		),
		'single_author_box'      => array(
			'section' => 'otb_blog_single',
			'type'    => 'toggle',
			'label'   => __( 'Author box', 'overtheball' ),
		),
		'single_nav'             => array(
			'section' => 'otb_blog_single',
			'type'    => 'toggle',
			'label'   => __( 'Previous / next links', 'overtheball' ),
		),
		'single_related'         => array(
			'section' => 'otb_blog_single',
			'type'    => 'toggle',
			'label'   => __( 'Related posts', 'overtheball' ),
		),

		// Contact.
		'contact_phone'          => array(
			'section' => 'otb_contact_info',
			'type'    => 'text',
			'label'   => __( 'Phone', 'overtheball' ),
		),
		'contact_email'          => array(
			'section' => 'otb_contact_info',
			'type'    => 'email',
			'label'   => __( 'Email', 'overtheball' ),
		),
		'contact_address'        => array(
			'section' => 'otb_contact_info',
			'type'    => 'textarea',
			'label'   => __( 'Address', 'overtheball' ),
		),
		'contact_hours'          => array(
			'section' => 'otb_contact_info',
			'type'    => 'textarea',
			'label'   => __( 'Hours', 'overtheball' ),
		),
		'contact_map'            => array(
			'section' => 'otb_contact_info',
			'type'    => 'toggle',
			'label'   => __( 'Show a map of the address on the Contact page', 'overtheball' ),
		),

		'social_new_tab'         => array(
			'section' => 'otb_social',
			'type'    => 'toggle',
			'label'   => __( 'Open social links in a new tab', 'overtheball' ),
		),

		'contact_form'           => array(
			'section'     => 'otb_contact_forms',
			'type'        => 'select',
			'label'       => __( 'Contact Form 7 form for the Contact page', 'overtheball' ),
			'choices'     => $cf7_forms,
			'description' => defined( 'WPCF7_VERSION' ) ? '' : __( 'Install Contact Form 7 to choose a form.', 'overtheball' ),
		),
		'mailpoet_form'          => array(
			'section'     => 'otb_contact_forms',
			'type'        => 'select',
			'label'       => __( 'MailPoet newsletter form', 'overtheball' ),
			'choices'     => $mailpoet_forms,
			'description' => defined( 'MAILPOET_VERSION' ) ? '' : __( 'Install MailPoet to choose a form.', 'overtheball' ),
		),
		'mailpoet_theme_styles'  => array(
			'section' => 'otb_contact_forms',
			'type'    => 'toggle',
			'label'   => __( 'Style MailPoet forms to match the theme', 'overtheball' ),
		),
		'newsletter_shortcode'   => array(
			'section'     => 'otb_contact_forms',
			'type'        => 'text',
			'label'       => __( 'Or use any newsletter shortcode', 'overtheball' ),
			'description' => __( 'Overrides the MailPoet form, e.g. [mailpoet_form id="2"].', 'overtheball' ),
		),
	);

	foreach ( otb_social_networks() as $network => $label ) {
		$fields[ 'social_' . $network ] = array(
			'section' => 'otb_social',
			'type'    => 'url',
			'label'   => $label,
		);
	}

	if ( otb_is_woocommerce_active() ) {
		$fields += array(
			'shop_columns'            => array(
				'section' => 'otb_shop_layout',
				'type'    => 'range',
				'label'   => __( 'Products per row (desktop)', 'overtheball' ),
				'min'     => 2,
				'max'     => 6,
			),
			'shop_columns_mobile'     => array(
				'section' => 'otb_shop_layout',
				'type'    => 'radio',
				'label'   => __( 'Products per row (mobile)', 'overtheball' ),
				'choices' => array(
					'1' => '1',
					'2' => '2',
				),
			),
			'shop_per_page'           => array(
				'section' => 'otb_shop_layout',
				'type'    => 'number',
				'label'   => __( 'Products per page', 'overtheball' ),
				'min'     => 1,
				'max'     => 96,
			),
			'shop_filter_drawer'      => array(
				'section' => 'otb_shop_layout',
				'type'    => 'toggle',
				'label'   => __( 'Shop sidebar becomes a "Filter" drawer on mobile', 'overtheball' ),
			),
			'catalog_mode'            => array(
				'section' => 'otb_shop_layout',
				'type'    => 'toggle',
				'label'   => __( 'Catalog mode (hide prices and add to cart)', 'overtheball' ),
			),

			'card_hover_image'        => array(
				'section' => 'otb_product_cards',
				'type'    => 'toggle',
				'label'   => __( 'Show second gallery picture on hover', 'overtheball' ),
			),
			'card_show_category'      => array(
				'section' => 'otb_product_cards',
				'type'    => 'toggle',
				'label'   => __( 'Show category', 'overtheball' ),
			),
			'card_show_rating'        => array(
				'section' => 'otb_product_cards',
				'type'    => 'toggle',
				'label'   => __( 'Show star rating', 'overtheball' ),
			),
			'card_button'             => array(
				'section' => 'otb_product_cards',
				'type'    => 'radio',
				'label'   => __( 'Add to cart button', 'overtheball' ),
				'choices' => array(
					'always' => __( 'Always visible', 'overtheball' ),
					'hover'  => __( 'On hover (desktop)', 'overtheball' ),
					'none'   => __( 'Hidden', 'overtheball' ),
				),
			),
			'sale_badge'              => array(
				'section' => 'otb_product_cards',
				'type'    => 'radio',
				'label'   => __( 'Sale badge', 'overtheball' ),
				'choices' => array(
					'percent' => __( 'Percentage off', 'overtheball' ),
					'text'    => __( '"Sale"', 'overtheball' ),
				),
			),
			'new_badge_days'          => array(
				'section' => 'otb_product_cards',
				'type'    => 'number',
				'label'   => __( '"New" badge for products added within (days, 0 = off)', 'overtheball' ),
				'min'     => 0,
				'max'     => 365,
			),

			'yarn_details'            => array(
				'section'     => 'otb_single_product',
				'type'        => 'toggle',
				'label'       => __( 'Yarn Details tab and spec chips', 'overtheball' ),
				'description' => __( 'Fill in the "Yarn Details" tab when editing a product: weight, fiber, yardage, gauge, needle and hook size, care.', 'overtheball' ),
			),
			'yardage_calculator'      => array(
				'section' => 'otb_single_product',
				'type'    => 'toggle',
				'label'   => __( '"How many skeins?" calculator', 'overtheball' ),
				'active'  => array( 'yarn_details' => true ),
			),
			'dye_lot_text'            => array(
				'section'     => 'otb_single_product',
				'type'        => 'textarea',
				'label'       => __( 'Dye lot notice', 'overtheball' ),
				'description' => __( 'Shown on products where "Show dye lot notice" is ticked.', 'overtheball' ),
				'active'      => array( 'yarn_details' => true ),
			),
			'sticky_add_to_cart'      => array(
				'section' => 'otb_single_product',
				'type'    => 'toggle',
				'label'   => __( 'Sticky add-to-cart bar when scrolling', 'overtheball' ),
			),
			'related_count'           => array(
				'section' => 'otb_single_product',
				'type'    => 'range',
				'label'   => __( 'Related products', 'overtheball' ),
				'min'     => 0,
				'max'     => 8,
			),
			'gallery_zoom'            => array(
				'section' => 'otb_single_product',
				'type'    => 'toggle',
				'label'   => __( 'Gallery hover zoom', 'overtheball' ),
			),
			'gallery_lightbox'        => array(
				'section' => 'otb_single_product',
				'type'    => 'toggle',
				'label'   => __( 'Gallery lightbox', 'overtheball' ),
			),
			'gallery_slider'          => array(
				'section' => 'otb_single_product',
				'type'    => 'toggle',
				'label'   => __( 'Gallery slider', 'overtheball' ),
			),

			'free_shipping_threshold' => array(
				'section'     => 'otb_cart_checkout',
				'type'        => 'number',
				'label'       => __( 'Free shipping threshold (0 = hide progress bar)', 'overtheball' ),
				'description' => __( 'Shows "You are $X away from free shipping" in the mini cart and cart. Match it to your free shipping method.', 'overtheball' ),
				'min'         => 0,
				'max'         => 100000,
				'step'        => 0.01,
			),
			'checkout_minimal'        => array(
				'section' => 'otb_cart_checkout',
				'type'    => 'toggle',
				'label'   => __( 'Distraction-free checkout header and footer', 'overtheball' ),
			),
		);
	}

	return array(
		'panels'   => $panels,
		'sections' => $sections,
		'fields'   => $fields,
	);
}
