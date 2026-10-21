<?php
/**
 * Appearance → Over The Ball: welcome/setup checklist, picture importer,
 * recommended plugins, and settings export / import / reset.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the admin page.
 */
function otb_admin_menu() {
	add_theme_page(
		__( 'Over The Ball Theme', 'overtheball' ),
		__( 'Over The Ball', 'overtheball' ),
		'edit_theme_options',
		'overtheball',
		'otb_render_theme_page'
	);
}
add_action( 'admin_menu', 'otb_admin_menu' );

/**
 * Admin assets for the theme page and the meta box.
 *
 * @param string $hook Current admin page.
 */
function otb_admin_assets( $hook ) {
	if ( 'appearance_page_overtheball' !== $hook ) {
		return;
	}
	wp_enqueue_style( 'otb-admin', OTB_URI . 'assets/css/admin.css', array(), OTB_VERSION );
	wp_enqueue_script( 'otb-admin', OTB_URI . 'assets/js/admin.js', array(), OTB_VERSION, true );
	wp_localize_script(
		'otb-admin',
		'otbAdmin',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'otb_pictures' ),
			'i18n'    => array(
				/* translators: 1: done, 2: total. */
				'progress' => __( 'Imported %1$d of %2$d…', 'overtheball' ),
				/* translators: 1: library size, 2: slots filled. */
				'done'     => __( 'Done! %1$d pictures in your library; %2$d image slots filled.', 'overtheball' ),
				'error'    => __( 'Some pictures could not be imported. Reload the page to retry.', 'overtheball' ),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'otb_admin_assets' );

/**
 * Plugins the theme integrates with.
 *
 * @return array slug => [name, file, purpose].
 */
function otb_recommended_plugins() {
	return array(
		'woocommerce'    => array( 'WooCommerce', 'woocommerce/woocommerce.php', __( 'Online store: products, cart, checkout. Enables the shop layouts, mini cart, yarn details and shop-by-weight.', 'overtheball' ) ),
		'contact-form-7' => array( 'Contact Form 7', 'contact-form-7/wp-contact-form-7.php', __( 'Contact forms, styled by the theme and used by the Contact page template.', 'overtheball' ) ),
		'mailpoet'       => array( 'MailPoet', 'mailpoet/mailpoet.php', __( 'Newsletter signups in the footer strip and homepage newsletter section.', 'overtheball' ) ),
		'elementor'      => array( 'Elementor', 'elementor/elementor.php', __( 'Drag-and-drop page building. Works with the Full Width and Blank Canvas templates, and Elementor Pro Theme Builder locations.', 'overtheball' ) ),
	);
}

/**
 * Render the admin page.
 */
function otb_render_theme_page() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- tab navigation only.
	$tab  = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'welcome';
	$tabs = array(
		'welcome'  => __( 'Welcome', 'overtheball' ),
		'pictures' => __( 'Pictures', 'overtheball' ),
		'plugins'  => __( 'Plugins', 'overtheball' ),
		'tools'    => __( 'Settings Tools', 'overtheball' ),
	);
	if ( ! isset( $tabs[ $tab ] ) ) {
		$tab = 'welcome';
	}
	?>
	<div class="wrap otb-admin">
		<div class="otb-admin__hero">
			<img src="<?php echo esc_url( OTB_URI . 'assets/images/logo.png' ); ?>" alt="<?php esc_attr_e( 'Over The Ball Yarns', 'overtheball' ); ?>" width="260">
			<div>
				<h1><?php esc_html_e( 'Over The Ball Theme', 'overtheball' ); ?> <span class="otb-admin__version"><?php echo esc_html( OTB_VERSION ); ?></span></h1>
				<p><?php esc_html_e( 'A yarn & fiber shop theme with WooCommerce, Contact Form 7, MailPoet and Elementor support.', 'overtheball' ); ?></p>
			</div>
		</div>

		<?php otb_admin_result_notice(); ?>

		<nav class="nav-tab-wrapper">
			<?php foreach ( $tabs as $slug => $label ) : ?>
				<a class="nav-tab<?php echo $slug === $tab ? ' nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'themes.php?page=overtheball&tab=' . $slug ) ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>

		<div class="otb-admin__body">
			<?php call_user_func( 'otb_admin_tab_' . $tab ); ?>
		</div>
	</div>
	<?php
}

/**
 * Success/error message after an admin action.
 */
function otb_admin_result_notice() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
	$result   = isset( $_GET['otb_result'] ) ? sanitize_key( $_GET['otb_result'] ) : '';
	$messages = array(
		'imported' => array( 'success', __( 'Settings imported.', 'overtheball' ) ),
		'reset'    => array( 'success', __( 'Theme settings reset to their defaults.', 'overtheball' ) ),
		'assigned' => array( 'success', __( 'Empty image slots were filled from your picture library.', 'overtheball' ) ),
		'cleared'  => array( 'success', __( 'Picture library list cleared (the pictures stay in your media library).', 'overtheball' ) ),
		'badfile'  => array( 'error', __( 'That file is not a valid Over The Ball settings export.', 'overtheball' ) ),
		'pictures' => array( 'success', __( 'Pictures imported.', 'overtheball' ) ),
	);
	if ( isset( $messages[ $result ] ) ) {
		printf( '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>', esc_attr( $messages[ $result ][0] ), esc_html( $messages[ $result ][1] ) );
	}
}

/**
 * Customizer deep link.
 *
 * @param string $type  panel|section.
 * @param string $id    Panel/section ID.
 * @return string
 */
function otb_customizer_link( $type, $id ) {
	return admin_url( 'customize.php?autofocus[' . $type . ']=' . $id );
}

/**
 * Welcome tab: setup checklist and quick links.
 */
function otb_admin_tab_welcome() {
	$checks = array(
		array( (bool) get_theme_mod( 'custom_logo' ), __( 'Logo set', 'overtheball' ), otb_customizer_link( 'section', 'title_tagline' ) ),
		array( (bool) count( otb_theme_picture_ids() ), __( 'Pictures from your previous theme imported', 'overtheball' ), admin_url( 'themes.php?page=overtheball&tab=pictures' ) ),
		array( has_nav_menu( 'primary' ), __( 'Primary menu assigned', 'overtheball' ), admin_url( 'nav-menus.php?action=locations' ) ),
		array( 'page' === get_option( 'show_on_front' ), __( 'A static homepage chosen (Settings → Reading)', 'overtheball' ), admin_url( 'options-reading.php' ) ),
		array( otb_is_woocommerce_active(), __( 'WooCommerce active', 'overtheball' ), admin_url( 'themes.php?page=overtheball&tab=plugins' ) ),
		array( (bool) otb_option( 'contact_email' ) || (bool) otb_option( 'contact_phone' ), __( 'Contact details added', 'overtheball' ), otb_customizer_link( 'section', 'otb_contact_info' ) ),
		array( (bool) otb_active_social_links(), __( 'Social profiles linked (Instagram, Ravelry…)', 'overtheball' ), otb_customizer_link( 'section', 'otb_social' ) ),
		array( (bool) ( function_exists( 'otb_mailpoet_form_id' ) && otb_mailpoet_form_id() ) || (bool) otb_option( 'newsletter_shortcode' ), __( 'Newsletter form connected (MailPoet)', 'overtheball' ), otb_customizer_link( 'section', 'otb_contact_forms' ) ),
	);

	$links = array(
		array( 'admin-appearance', __( 'Colors & fonts', 'overtheball' ), otb_customizer_link( 'panel', 'otb_style' ) ),
		array( 'align-wide', __( 'Header options', 'overtheball' ), otb_customizer_link( 'panel', 'otb_header_panel' ) ),
		array( 'editor-insertmore', __( 'Footer options', 'overtheball' ), otb_customizer_link( 'panel', 'otb_footer_panel' ) ),
		array( 'admin-home', __( 'Homepage sections', 'overtheball' ), otb_customizer_link( 'panel', 'otb_home' ) ),
		array( 'columns', __( 'Layout & sidebars', 'overtheball' ), otb_customizer_link( 'panel', 'otb_general' ) ),
		array( 'cart', __( 'Shop settings', 'overtheball' ), otb_customizer_link( 'panel', 'woocommerce' ) ),
		array( 'welcome-write-blog', __( 'Blog settings', 'overtheball' ), otb_customizer_link( 'panel', 'otb_blog_panel' ) ),
		array( 'email', __( 'Contact, social & forms', 'overtheball' ), otb_customizer_link( 'panel', 'otb_contact_panel' ) ),
		array( 'menu', __( 'Menus', 'overtheball' ), admin_url( 'nav-menus.php' ) ),
		array( 'welcome-widgets-menus', __( 'Widgets & sidebars', 'overtheball' ), admin_url( 'widgets.php' ) ),
	);
	?>
	<div class="otb-admin__grid">
		<section class="otb-card">
			<h2><?php esc_html_e( 'Setup checklist', 'overtheball' ); ?></h2>
			<ul class="otb-checklist">
				<?php foreach ( $checks as $check ) : ?>
					<li class="<?php echo $check[0] ? 'is-done' : ''; ?>">
						<span class="dashicons <?php echo $check[0] ? 'dashicons-yes-alt' : 'dashicons-marker'; ?>"></span>
						<a href="<?php echo esc_url( $check[2] ); ?>"><?php echo esc_html( $check[1] ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
		<section class="otb-card">
			<h2><?php esc_html_e( 'Customize', 'overtheball' ); ?></h2>
			<ul class="otb-links">
				<?php foreach ( $links as $link ) : ?>
					<li><a href="<?php echo esc_url( $link[2] ); ?>"><span class="dashicons dashicons-<?php echo esc_attr( $link[0] ); ?>"></span><?php echo esc_html( $link[1] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</section>
		<section class="otb-card otb-card--wide">
			<h2><?php esc_html_e( 'Good to know', 'overtheball' ); ?></h2>
			<ul class="otb-tips">
				<li><?php esc_html_e( 'Menu locations: Primary, Top Bar, Mobile, Footer Links and Footer Bottom (Appearance → Menus → Manage Locations).', 'overtheball' ); ?></li>
				<li><?php esc_html_e( 'Mega menu: add the CSS class "mega-menu" to a top-level Primary menu item. Its sub-items become columns.', 'overtheball' ); ?></li>
				<li><?php esc_html_e( 'Widget areas: Blog, Page, Shop (mobile filter drawer), Product, Top Bar, Header, Mobile Menu Drawer, Before Footer and Footer Columns 1–4.', 'overtheball' ); ?></li>
				<li><?php esc_html_e( 'Every page has a "Page Layout" box: sidebar, full-width content, transparent header, and hiding the title, breadcrumbs, header or footer.', 'overtheball' ); ?></li>
				<li><?php esc_html_e( 'Page templates: Full Width, Blank Canvas (for Elementor landing pages) and Contact.', 'overtheball' ); ?></li>
				<li><?php esc_html_e( 'Products have a "Yarn Details" tab (weight, fiber, yardage, gauge, needle & hook size, care). It powers spec chips, the skein calculator and Shop by Weight.', 'overtheball' ); ?></li>
				<li><?php esc_html_e( 'Shortcodes for Elementor or blocks: [otb_section name="hero"], [otb_section name="weights"], [otb_newsletter], [otb_social], [otb_contact].', 'overtheball' ); ?></li>
			</ul>
		</section>
	</div>
	<?php
}

/**
 * Pictures tab: import pictures from any installed theme.
 */
function otb_admin_tab_pictures() {
	$sources  = otb_picture_sources();
	$previous = otb_previous_theme();
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only scan options.
	$source   = isset( $_GET['source'] ) ? sanitize_text_field( wp_unslash( $_GET['source'] ) ) : ( $previous ? $previous : 'otb-library' );
	$min_size = isset( $_GET['min_size'] ) ? absint( $_GET['min_size'] ) : 120;
	// phpcs:enable
	if ( ! isset( $sources[ $source ] ) ) {
		$source = 'otb-library';
	}

	$scan     = otb_scan_pictures( $source, $min_size );
	$pending  = array_values( wp_list_filter( $scan['import'], array( 'imported' => 0 ) ) );
	$library  = otb_theme_picture_ids();
	$mod_pics = 'otb-library' === $source ? array() : otb_theme_mod_pictures( $source );
	?>
	<div class="otb-card">
		<h2><?php esc_html_e( 'Bring your pictures over', 'overtheball' ); ?></h2>
		<p>
			<?php esc_html_e( 'Over The Ball copies every picture from your previous theme into the Media Library, adds pictures that theme had set in the Customizer, and uses them in the homepage hero, promo, about section, title banner and picture gallery.', 'overtheball' ); ?>
			<?php if ( $previous ) : ?>
				<?php
				/* translators: %s: theme name. */
				printf( esc_html__( 'Your previous theme was %s.', 'overtheball' ), '<strong>' . esc_html( wp_get_theme( $previous )->get( 'Name' ) ) . '</strong>' );
				?>
			<?php endif; ?>
		</p>

		<form method="get" class="otb-scan-form">
			<input type="hidden" name="page" value="overtheball">
			<input type="hidden" name="tab" value="pictures">
			<label><?php esc_html_e( 'Source', 'overtheball' ); ?>
				<select name="source">
					<?php foreach ( $sources as $slug => $info ) : ?>
						<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $source, $slug ); ?>><?php echo esc_html( $info['label'] ); ?><?php echo $slug === $previous ? ' ' . esc_html__( '(previous theme)', 'overtheball' ) : ''; ?></option>
					<?php endforeach; ?>
				</select>
			</label>
			<label><?php esc_html_e( 'Skip pictures smaller than', 'overtheball' ); ?>
				<input type="number" name="min_size" value="<?php echo esc_attr( $min_size ); ?>" min="0" max="2000" class="small-text"> px
			</label>
			<button class="button"><?php esc_html_e( 'Scan', 'overtheball' ); ?></button>
		</form>

		<div class="otb-scan-summary">
			<p>
				<?php
				printf(
					/* translators: 1: pictures found, 2: already imported, 3: skipped small images, 4: pictures from theme settings. */
					esc_html__( 'Found %1$d pictures (%2$d already imported), skipped %3$d small icons/sprites, plus %4$d pictures set in that theme\'s Customizer.', 'overtheball' ),
					count( $scan['import'] ),
					count( $scan['import'] ) - count( $pending ),
					count( $scan['skipped'] ),
					count( $mod_pics )
				);
				?>
			</p>
			<button type="button" class="button button-primary button-hero otb-import-start" data-source="<?php echo esc_attr( $source ); ?>" data-keys="<?php echo esc_attr( wp_json_encode( wp_list_pluck( $pending, 'key' ) ) ); ?>"<?php echo ( $scan['import'] || $mod_pics ) ? '' : ' disabled'; ?>>
				<?php
				echo $pending
					/* translators: %d: number of pictures. */
					? esc_html( sprintf( _n( 'Import %d picture and use it', 'Import %d pictures and use them', count( $pending ), 'overtheball' ), count( $pending ) ) )
					: esc_html__( 'Add to library & fill empty image slots', 'overtheball' );
				?>
			</button>
			<div class="otb-progress" hidden><div class="otb-progress__bar"><span></span></div><p class="otb-progress__text" aria-live="polite"></p></div>
		</div>

		<?php if ( $scan['import'] ) : ?>
			<ul class="otb-thumbs">
				<?php foreach ( $scan['import'] as $item ) : ?>
					<li class="<?php echo $item['imported'] ? 'is-imported' : ''; ?>" data-key="<?php echo esc_attr( $item['key'] ); ?>">
						<?php
						$src = $item['imported']
							? wp_get_attachment_image_url( $item['imported'], 'thumbnail' )
							: otb_picture_preview_url( $item['path'] );
						?>
						<?php if ( $src ) : ?>
							<img src="<?php echo esc_url( $src ); ?>" alt="" loading="lazy">
						<?php endif; ?>
						<span class="otb-thumbs__name" title="<?php echo esc_attr( $item['relative'] ); ?>"><?php echo esc_html( basename( $item['relative'] ) ); ?></span>
						<span class="otb-thumbs__meta"><?php echo esc_html( $item['width'] . '×' . $item['height'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php elseif ( 'otb-library' === $source ) : ?>
			<p class="description"><?php esc_html_e( 'This folder is empty. You can copy pictures into wp-content/themes/overtheball/assets/images/library/ and scan again.', 'overtheball' ); ?></p>
		<?php endif; ?>
	</div>

	<div class="otb-card">
		<h2>
			<?php
			/* translators: %d: number of pictures. */
			printf( esc_html__( 'Your picture library (%d)', 'overtheball' ), count( $library ) );
			?>
		</h2>
		<p><?php esc_html_e( 'The homepage picture gallery shows all of these unless you pick specific pictures in the Customizer.', 'overtheball' ); ?></p>
		<?php if ( $library ) : ?>
			<ul class="otb-thumbs otb-thumbs--library">
				<?php
				$slots = array();
				foreach ( otb_picture_slots() as $slot => $info ) {
					$slots[ absint( get_theme_mod( $slot ) ) ][] = $info[0];
				}
				foreach ( $library as $id ) :
					?>
					<li>
						<?php echo wp_get_attachment_image( $id, 'thumbnail' ); ?>
						<span class="otb-thumbs__name"><?php echo esc_html( get_the_title( $id ) ); ?></span>
						<?php if ( isset( $slots[ $id ] ) ) : ?>
							<span class="otb-thumbs__used"><?php echo esc_html( implode( ', ', $slots[ $id ] ) ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="otb-inline-form">
			<?php wp_nonce_field( 'otb_pictures_action' ); ?>
			<input type="hidden" name="action" value="otb_pictures_action">
			<button class="button" name="do" value="assign"><?php esc_html_e( 'Fill empty image slots', 'overtheball' ); ?></button>
			<button class="button" name="do" value="reassign"><?php esc_html_e( 'Re-assign all image slots', 'overtheball' ); ?></button>
			<button class="button-link button-link-delete" name="do" value="clear"><?php esc_html_e( 'Clear library list', 'overtheball' ); ?></button>
		</form>
	</div>
	<?php
}

/**
 * Preview URL for a file inside wp-content (themes are web-accessible).
 *
 * @param string $path Absolute path.
 * @return string
 */
function otb_picture_preview_url( $path ) {
	$path    = wp_normalize_path( $path );
	$content = wp_normalize_path( WP_CONTENT_DIR );
	if ( 0 !== strpos( $path, $content ) ) {
		return '';
	}
	return content_url( substr( $path, strlen( $content ) ) );
}

/**
 * Handle picture library buttons (assign / reassign / clear).
 */
function otb_handle_pictures_action() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'overtheball' ) );
	}
	check_admin_referer( 'otb_pictures_action' );

	$do     = isset( $_POST['do'] ) ? sanitize_key( $_POST['do'] ) : '';
	$result = 'assigned';

	if ( 'assign' === $do ) {
		otb_assign_pictures( false );
	} elseif ( 'reassign' === $do ) {
		otb_assign_pictures( true );
	} elseif ( 'clear' === $do ) {
		delete_option( OTB_PICTURES_OPTION );
		$result = 'cleared';
	}

	wp_safe_redirect( admin_url( 'themes.php?page=overtheball&tab=pictures&otb_result=' . $result ) );
	exit;
}
add_action( 'admin_post_otb_pictures_action', 'otb_handle_pictures_action' );

/**
 * Plugins tab: status and install/activate links.
 */
function otb_admin_tab_plugins() {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$installed = get_plugins();
	?>
	<div class="otb-card">
		<h2><?php esc_html_e( 'Supported plugins', 'overtheball' ); ?></h2>
		<table class="widefat striped otb-plugins">
			<thead><tr><th><?php esc_html_e( 'Plugin', 'overtheball' ); ?></th><th><?php esc_html_e( 'What the theme does with it', 'overtheball' ); ?></th><th><?php esc_html_e( 'Status', 'overtheball' ); ?></th></tr></thead>
			<tbody>
			<?php foreach ( otb_recommended_plugins() as $slug => $plugin ) : ?>
				<?php
				list( $name, $file, $purpose ) = $plugin;
				$is_installed                  = isset( $installed[ $file ] );
				$is_active                     = $is_installed && is_plugin_active( $file );
				?>
				<tr>
					<td><strong><?php echo esc_html( $name ); ?></strong></td>
					<td><?php echo esc_html( $purpose ); ?></td>
					<td>
						<?php if ( $is_active ) : ?>
							<span class="otb-status otb-status--ok"><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e( 'Active', 'overtheball' ); ?></span>
						<?php elseif ( $is_installed && current_user_can( 'activate_plugins' ) ) : ?>
							<a class="button button-primary" href="<?php echo esc_url( wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( $file ) ), 'activate-plugin_' . $file ) ); ?>"><?php esc_html_e( 'Activate', 'overtheball' ); ?></a>
						<?php elseif ( current_user_can( 'install_plugins' ) ) : ?>
							<a class="button" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $slug ), 'install-plugin_' . $slug ) ); ?>"><?php esc_html_e( 'Install', 'overtheball' ); ?></a>
						<?php else : ?>
							<?php esc_html_e( 'Not installed', 'overtheball' ); ?>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}

/**
 * Tools tab: export, import and reset theme settings.
 */
function otb_admin_tab_tools() {
	?>
	<div class="otb-admin__grid">
		<section class="otb-card">
			<h2><?php esc_html_e( 'Export settings', 'overtheball' ); ?></h2>
			<p><?php esc_html_e( 'Download every Over The Ball Customizer setting as a JSON file — handy for backups or moving to a staging site.', 'overtheball' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'otb_export' ); ?>
				<input type="hidden" name="action" value="otb_export">
				<button class="button button-primary"><?php esc_html_e( 'Download export', 'overtheball' ); ?></button>
			</form>
		</section>
		<section class="otb-card">
			<h2><?php esc_html_e( 'Import settings', 'overtheball' ); ?></h2>
			<p><?php esc_html_e( 'Upload a file exported from this theme. Every value is validated before it is saved.', 'overtheball' ); ?></p>
			<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'otb_import' ); ?>
				<input type="hidden" name="action" value="otb_import">
				<input type="file" name="otb_settings" accept="application/json,.json" required>
				<button class="button button-primary"><?php esc_html_e( 'Import', 'overtheball' ); ?></button>
			</form>
		</section>
		<section class="otb-card">
			<h2><?php esc_html_e( 'Reset settings', 'overtheball' ); ?></h2>
			<p><?php esc_html_e( 'Return all Over The Ball Customizer settings to their defaults. Menus, widgets, the logo and your content are not touched.', 'overtheball' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( 'Reset all theme settings to defaults?', 'overtheball' ) ); ?>');">
				<?php wp_nonce_field( 'otb_reset' ); ?>
				<input type="hidden" name="action" value="otb_reset">
				<button class="button button-link-delete"><?php esc_html_e( 'Reset to defaults', 'overtheball' ); ?></button>
			</form>
		</section>
	</div>
	<?php
}

/**
 * Download the settings export.
 */
function otb_handle_export() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'overtheball' ) );
	}
	check_admin_referer( 'otb_export' );

	$settings = array();
	foreach ( array_keys( otb_defaults() ) as $key ) {
		$settings[ $key ] = otb_option( $key );
	}

	$payload = array(
		'theme'    => 'overtheball',
		'version'  => OTB_VERSION,
		'exported' => gmdate( 'c' ),
		'settings' => $settings,
	);

	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=overtheball-settings-' . gmdate( 'Y-m-d' ) . '.json' );
	echo wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	exit;
}
add_action( 'admin_post_otb_export', 'otb_handle_export' );

/**
 * Apply an uploaded settings export.
 */
function otb_handle_import() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'overtheball' ) );
	}
	check_admin_referer( 'otb_import' );

	$redirect = admin_url( 'themes.php?page=overtheball&tab=tools&otb_result=' );
	$file     = isset( $_FILES['otb_settings']['tmp_name'] ) ? sanitize_text_field( $_FILES['otb_settings']['tmp_name'] ) : '';

	if ( ! $file || ! is_uploaded_file( $file ) ) {
		wp_safe_redirect( $redirect . 'badfile' );
		exit;
	}

	$data = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local upload.
	if ( ! is_array( $data ) || ( $data['theme'] ?? '' ) !== 'overtheball' || ! isset( $data['settings'] ) || ! is_array( $data['settings'] ) ) {
		wp_safe_redirect( $redirect . 'badfile' );
		exit;
	}

	$fields = otb_customizer_fields();
	foreach ( $data['settings'] as $key => $value ) {
		$key = sanitize_key( $key );
		if ( isset( $fields[ $key ] ) ) {
			set_theme_mod( $key, otb_sanitize_value( $key, $value ) );
		}
	}

	wp_safe_redirect( $redirect . 'imported' );
	exit;
}
add_action( 'admin_post_otb_import', 'otb_handle_import' );

/**
 * Reset all theme settings.
 */
function otb_handle_reset() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'overtheball' ) );
	}
	check_admin_referer( 'otb_reset' );

	foreach ( array_keys( otb_defaults() ) as $key ) {
		remove_theme_mod( $key );
	}

	wp_safe_redirect( admin_url( 'themes.php?page=overtheball&tab=tools&otb_result=reset' ) );
	exit;
}
add_action( 'admin_post_otb_reset', 'otb_handle_reset' );

/**
 * "Over The Ball" shortcut under Appearance in the front-end admin bar.
 *
 * @param WP_Admin_Bar $wp_admin_bar Admin bar.
 */
function otb_admin_bar_link( $wp_admin_bar ) {
	if ( ! current_user_can( 'edit_theme_options' ) || is_admin() ) {
		return;
	}
	$wp_admin_bar->add_node(
		array(
			'parent' => 'appearance',
			'id'     => 'otb-theme-page',
			'title'  => __( 'Over The Ball', 'overtheball' ),
			'href'   => admin_url( 'themes.php?page=overtheball' ),
		)
	);
}
add_action( 'admin_bar_menu', 'otb_admin_bar_link', 100 );
