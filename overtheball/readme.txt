=== Over The Ball ===
Contributors: overtheballyarns
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: e-commerce, blog, custom-logo, custom-menu, featured-images, theme-options, translation-ready, left-sidebar, right-sidebar, footer-widgets

A yarn & fiber shop theme for Over The Ball Yarns, built for WooCommerce, Contact Form 7, MailPoet and Elementor.

== Description ==

Over The Ball is a boutique WooCommerce theme for online yarn and fiber sales. Its violet-and-ink palette, script accents and knit-stitch textures come straight from the Over The Ball Yarns logo, which ships with the theme and is installed as the site logo (plus a matching site icon) on activation.

= Highlights =

* Homepage section builder: hero slider, feature highlights, shop by category, product carousel with New / Best Sellers / On Sale tabs, shop by yarn weight, promo banner, about/story, testimonials, latest posts, picture gallery, newsletter and page content — each switchable and drag-and-drop re-orderable.
* Yarn Details for every product: weight (Craft Yarn Council 0–7), fiber, yardage/meters, skein weight, gauge, needle and hook size, care and origin. Shown as spec chips, a "Yarn Details" tab, a loop line ("Worsted · 220 yd"), a dye-lot reminder and a "How many skeins do I need?" calculator that fills in the quantity.
* Shop by Weight links (`/shop/?yarn_weight=dk`) and a Yarn Weights sidebar widget.
* Slide-out mini cart with a free-shipping progress bar, quantity +/- buttons, sale percentage and New / Sold out badges, hover image swap, sticky add-to-cart bar, mobile filter drawer, distraction-free checkout and an optional catalog mode.
* Picture importer: on activation every picture from the previously active theme (its image folders and the pictures set in its Customizer) is copied to the Media Library, placed in the hero, promo, about and banner slots, and shown in the homepage gallery.

== Installation ==

1. Appearance → Themes → Add New → Upload Theme, choose `overtheball.zip`, then Activate. (Run `bin/build-zip.sh` from the repository to create the zip.)
2. Over The Ball imports the previous theme's pictures and sets the logo automatically. Review them in Appearance → Over The Ball → Pictures.
3. Install the supported plugins from Appearance → Over The Ball → Plugins: WooCommerce, Contact Form 7, MailPoet and Elementor.
4. Settings → Reading: choose a static homepage (or keep "latest posts") — the homepage sections appear either way.
5. Appearance → Menus: assign the Primary menu. Add the CSS class `mega-menu` to a top-level item to turn its sub-menu into a mega menu.
6. Appearance → Customize: everything else.

== Theme Settings (Customizer) ==

* Site Identity: logo width (desktop / mobile), light logo for dark areas.
* Theme: General & Layout — full-width or boxed layout, container width, corner radius, button shape and case, animations, back-to-top, sidebar position for blog, posts, pages, search, shop and products (left / right / none), sticky sidebar, title banner style (tint / plain / image) and alignment, breadcrumbs, performance switches.
* Theme: Colors & Fonts — 11 brand colors and 10 header/footer colors with live preview; body, heading and script fonts (bundled fonts plus Google Fonts, or none), base size, heading weight and sizes, menu case and spacing.
* Theme: Header — announcement bar (rotating, dismissible), top bar (text, contact, menu, social or widget on each side), three header layouts (Classic, Boutique centered, Shop with search bar), sticky / shrinking / transparent header, padding, border, search, account and cart icons, cart total, cart drawer or cart page, mobile drawer side and breakpoint.
* Theme: Footer — newsletter strip, 0–4 widget columns with width presets, logo/about/social brand block, copyright tokens ({year}, {site}, {copy}), payment methods.
* Theme: Homepage — every section's content, pictures and on/off switch, plus section order.
* Theme: Blog — grid / list / classic layouts, columns, excerpt length, meta, featured image, tags, share links, author box, post navigation, related posts.
* Theme: Contact, Social & Forms — phone, email, address, hours and map; Facebook, Instagram, Pinterest, Ravelry, YouTube, TikTok, X and Etsy; Contact Form 7 form for the Contact template; MailPoet form (or any newsletter shortcode).
* WooCommerce → Theme sections — products per row and page, filter drawer, catalog mode, product card options, badges, Yarn Details, skein calculator, dye-lot text, sticky add to cart, related products, gallery zoom/lightbox/slider, free shipping threshold, distraction-free checkout.

Appearance → Over The Ball also offers a setup checklist, plugin status, the picture importer, and settings export / import / reset.

== Menus ==

Primary, Top Bar, Mobile (falls back to Primary), Footer Links and Footer Bottom / Legal.

== Widget Areas ==

Blog Sidebar, Page Sidebar, Shop Sidebar (becomes a "Filter" drawer on mobile), Product Sidebar, Top Bar, Header, Mobile Menu Drawer, Before Footer and Footer Columns 1–4.

== Per-page options ==

The "Page Layout" box on pages, posts and products: sidebar override, full-width content (for page builders), transparent header, and hiding the title banner, breadcrumbs, header or footer.

Page templates: Full Width, Blank Canvas (no header or footer) and Contact.

== Plugin support ==

* WooCommerce — layouts, product cards, mini cart, fragments, cart/checkout blocks styling, account area, Yarn Details.
* Contact Form 7 — themed forms, Contact page template, optional on-demand script loading.
* MailPoet — themed forms in the footer strip and homepage section; the first active form is used until you pick one.
* Elementor — Elementor Pro Theme Builder locations (header, footer, single, archive), "Header Footer Elementor" support, Hide Title support, Elementor Canvas / Full Width, and theme fonts/colors as defaults. Theme sections are available as shortcodes for the Shortcode widget.

== Shortcodes ==

* `[otb_section name="hero|features|categories|products|weights|promo|about|testimonials|blog|gallery|newsletter"]`
* `[otb_newsletter]`, `[otb_social]`, `[otb_contact]`

== WP-CLI ==

* `wp overtheball pictures import [--source=<theme>] [--min-size=<px>]`
* `wp overtheball pictures assign [--overwrite]`
* `wp overtheball pictures list`

== Copyright ==

Over The Ball WordPress Theme, Copyright 2026 Over The Ball Yarns.
Over The Ball is distributed under the terms of the GNU GPL v2 or later.

The Over The Ball Yarns logo (assets/images/logo.png, logo-light.png, site-icon.png) is a trademark of Over The Ball Yarns and is not covered by the GPL.

Bundled fonts (assets/fonts), SIL Open Font License 1.1 — see the OFL-*.txt files:
* Jost — Copyright 2020 The Jost Project Authors.
* Cormorant Garamond — Copyright 2015 The Cormorant Project Authors.
* Allura — Copyright 2010 The Allura Project Authors.

Interface icons are drawn in the style of Feather Icons; some paths are adapted from Feather (https://feathericons.com), MIT License, Copyright (c) 2013-2023 Cole Bemis.

== Changelog ==

= 1.0.0 =
* Initial release of the Over The Ball yarn & fiber shop theme.
* Over The Ball Yarns logo, light logo and site icon installed on activation.
* Picture importer: every picture from the previously active theme is imported and used in the hero, promo, about, title banner and homepage gallery.
* Customizer settings for layout, sidebars, colors, typography, announcement bar, top bar, three header layouts, footer, homepage sections and order, blog, contact, social, forms and performance; settings export, import and reset.
* Five menu locations with mega menu support, twelve widget areas, per-page layout options and Full Width, Blank Canvas and Contact templates.
* WooCommerce: Yarn Details product data, spec chips, skein calculator, dye-lot notice, shop by weight, mini-cart drawer with free-shipping progress, badges, sticky add to cart, mobile filter drawer and distraction-free checkout.
* Contact Form 7, MailPoet and Elementor support.
