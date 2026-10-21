<?php
/**
 * Picture importer: brings every picture from the previously active theme
 * (its folders and the images set in its Customizer) into the media library,
 * keeps them in a "theme picture library", and fills the new theme's image
 * slots (hero, promo, about, title banner, gallery) with them.
 *
 * Runs automatically when the theme is activated (within a time budget),
 * and can be re-run from Appearance → Over The Ball → Pictures or with
 * `wp overtheball pictures import`.
 *
 * Pictures dropped into this theme's assets/images/library/ folder are
 * offered as a source too.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * Option holding the library of picture attachment IDs.
 */
const OTB_PICTURES_OPTION = 'otb_theme_pictures';

/**
 * Image file extensions that are imported.
 *
 * @return string[]
 */
function otb_picture_extensions() {
	return array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'avif' );
}

/**
 * Available picture sources: installed themes (except this one) and the
 * theme's own library folder.
 *
 * @return array slug => [label, dirs[]].
 */
function otb_picture_sources() {
	$sources = array(
		'otb-library' => array(
			'label' => __( 'Over The Ball: assets/images/library folder', 'overtheball' ),
			'dirs'  => array( OTB_DIR . 'assets/images/library' ),
		),
	);

	foreach ( wp_get_themes() as $stylesheet => $theme ) {
		if ( get_template() === $stylesheet ) {
			continue;
		}
		$dirs = array( $theme->get_stylesheet_directory() );
		if ( $theme->get_template() !== $stylesheet ) {
			$dirs[] = $theme->get_template_directory();
		}
		$sources[ $stylesheet ] = array(
			/* translators: %s: theme name. */
			'label' => sprintf( __( 'Theme: %s', 'overtheball' ), $theme->get( 'Name' ) ),
			'dirs'  => array_values( array_unique( array_filter( $dirs, 'is_dir' ) ) ),
		);
	}

	return $sources;
}

/**
 * The theme that was active before Over The Ball.
 *
 * @return string Stylesheet slug or ''.
 */
function otb_previous_theme() {
	$previous = (string) get_option( 'otb_previous_theme', '' );
	return $previous && array_key_exists( $previous, otb_picture_sources() ) ? $previous : '';
}

/**
 * Scan a source for pictures.
 *
 * @param string $source   Source slug.
 * @param int    $min_size Skip images whose longest side is smaller (icons, sprites).
 * @return array{import: array, skipped: array}
 */
function otb_scan_pictures( $source, $min_size = 120 ) {
	$sources = otb_picture_sources();
	$result  = array(
		'import'  => array(),
		'skipped' => array(),
	);
	if ( ! isset( $sources[ $source ] ) ) {
		return $result;
	}

	$skip_dirs  = array( 'node_modules', '.git', 'vendor', 'languages', 'fonts', '.github' );
	$extensions = otb_picture_extensions();

	foreach ( $sources[ $source ]['dirs'] as $index => $dir ) {
		if ( ! is_dir( $dir ) ) {
			continue;
		}
		$dir      = wp_normalize_path( untrailingslashit( $dir ) );
		$iterator = new RecursiveIteratorIterator(
			new RecursiveCallbackFilterIterator(
				new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ),
				static function ( $file ) use ( $skip_dirs ) {
					return ! ( $file->isDir() && in_array( $file->getFilename(), $skip_dirs, true ) );
				}
			)
		);

		foreach ( $iterator as $file ) {
			if ( ! $file->isFile() ) {
				continue;
			}
			$ext = strtolower( $file->getExtension() );
			if ( ! in_array( $ext, $extensions, true ) ) {
				continue;
			}
			$path     = wp_normalize_path( $file->getPathname() );
			$relative = ltrim( substr( $path, strlen( $dir ) ), '/' );
			$name     = strtolower( $file->getBasename( '.' . $file->getExtension() ) );

			// WordPress theme preview screenshots are not site pictures.
			if ( 'screenshot' === $name && false === strpos( $relative, '/' ) ) {
				continue;
			}

			$size = @getimagesize( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- unreadable files are simply skipped.
			$item = array(
				'key'      => $index . ':' . $relative,
				'relative' => $relative,
				'path'     => $path,
				'width'    => $size ? (int) $size[0] : 0,
				'height'   => $size ? (int) $size[1] : 0,
				'bytes'    => (int) $file->getSize(),
				'imported' => otb_find_imported_picture( $path ),
			);

			if ( ! $size || max( $item['width'], $item['height'] ) < $min_size ) {
				$result['skipped'][] = $item;
			} else {
				$result['import'][] = $item;
			}
		}
	}

	// Largest pictures first: they are the best candidates for big slots.
	usort(
		$result['import'],
		static function ( $a, $b ) {
			return ( $b['width'] * $b['height'] ) - ( $a['width'] * $a['height'] );
		}
	);

	return $result;
}

/**
 * Attachment previously imported from the same file contents.
 *
 * @param string $path File path.
 * @return int Attachment ID or 0.
 */
function otb_find_imported_picture( $path ) {
	$hash = md5_file( $path );
	if ( ! $hash ) {
		return 0;
	}
	$found = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_otb_source_hash', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $hash, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	return $found ? (int) $found[0] : 0;
}

/**
 * Resolve a scanned item key ("dirIndex:relative/path.jpg") to a safe absolute path.
 *
 * @param string $source Source slug.
 * @param string $key    Item key.
 * @return string Path or ''.
 */
function otb_resolve_picture_path( $source, $key ) {
	$sources = otb_picture_sources();
	if ( ! isset( $sources[ $source ] ) || ! preg_match( '/^(\d+):(.+)$/', (string) $key, $m ) ) {
		return '';
	}
	$dirs = $sources[ $source ]['dirs'];
	if ( ! isset( $dirs[ (int) $m[1] ] ) ) {
		return '';
	}
	$base = realpath( $dirs[ (int) $m[1] ] );
	$path = realpath( $base . '/' . $m[2] );

	if ( ! $base || ! $path || 0 !== strpos( wp_normalize_path( $path ), trailingslashit( wp_normalize_path( $base ) ) ) ) {
		return '';
	}
	if ( ! in_array( strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ), otb_picture_extensions(), true ) ) {
		return '';
	}
	return $path;
}

/**
 * Import one picture into the media library (or reuse an earlier import).
 *
 * @param string $source Source slug.
 * @param string $key    Item key from otb_scan_pictures().
 * @return int Attachment ID or 0.
 */
function otb_import_picture( $source, $key ) {
	$path = otb_resolve_picture_path( $source, $key );
	if ( ! $path ) {
		return 0;
	}

	$id = otb_find_imported_picture( $path );
	if ( ! $id ) {
		$title = ucwords( trim( preg_replace( '/[-_]+/', ' ', pathinfo( $path, PATHINFO_FILENAME ) ) ) );
		$id    = otb_import_file_to_media( $path, $title );
		if ( ! $id ) {
			return 0;
		}
		update_post_meta( $id, '_otb_source', $source . '/' . substr( (string) $key, strpos( (string) $key, ':' ) + 1 ) );
		update_post_meta( $id, '_otb_source_hash', md5_file( $path ) );
	}

	otb_add_theme_pictures( array( $id ) );
	return $id;
}

/**
 * Pictures a theme had set through its Customizer (logo, header, background,
 * hero/slider images…). These are already in the media library.
 *
 * @param string $stylesheet Theme stylesheet slug.
 * @return int[] Attachment IDs.
 */
function otb_theme_mod_pictures( $stylesheet ) {
	$mods = get_option( 'theme_mods_' . $stylesheet );
	if ( ! is_array( $mods ) ) {
		return array();
	}

	$ids  = array();
	$walk = static function ( $value, $key ) use ( &$walk, &$ids ) {
		if ( is_array( $value ) || is_object( $value ) ) {
			foreach ( (array) $value as $k => $v ) {
				$walk( $v, is_string( $k ) ? $key . '.' . $k : $key );
			}
			return;
		}
		$key = strtolower( (string) $key );

		if ( is_numeric( $value ) && (int) $value > 0 && preg_match( '/image|logo|img|background|photo|banner|slide|hero|picture|gallery|attachment/', $key ) ) {
			if ( wp_attachment_is_image( (int) $value ) ) {
				$ids[] = (int) $value;
			}
			return;
		}

		if ( is_string( $value ) && preg_match( '/\.(jpe?g|png|gif|webp|avif)(\?.*)?$/i', $value ) ) {
			$id = attachment_url_to_postid( preg_replace( '/-\d+x\d+(?=\.[a-z]+$)/i', '', strtok( $value, '?' ) ) );
			if ( $id && wp_attachment_is_image( $id ) ) {
				$ids[] = (int) $id;
			}
		}
	};

	foreach ( $mods as $key => $value ) {
		if ( in_array( $key, array( 'nav_menu_locations', 'sidebars_widgets' ), true ) ) {
			continue;
		}
		$walk( $value, $key );
	}

	return array_values( array_unique( $ids ) );
}

/**
 * Add attachment IDs to the theme picture library.
 *
 * @param int[] $ids Attachment IDs.
 */
function otb_add_theme_pictures( $ids ) {
	$library = (array) get_option( OTB_PICTURES_OPTION, array() );
	$library = array_values( array_unique( array_merge( array_map( 'absint', $library ), array_map( 'absint', $ids ) ) ) );
	update_option( OTB_PICTURES_OPTION, array_filter( $library ), false );
}

/**
 * Picture library IDs that still exist as images.
 *
 * @return int[]
 */
function otb_theme_picture_ids() {
	$ids = array_map( 'absint', (array) get_option( OTB_PICTURES_OPTION, array() ) );
	return array_values( array_filter( $ids, 'wp_attachment_is_image' ) );
}

/**
 * Image slots the importer fills, in priority order, with the orientation each prefers.
 *
 * @return array setting => [label, orientation].
 */
function otb_picture_slots() {
	return array(
		'hero_1_image'      => array( __( 'Homepage hero', 'overtheball' ), 'landscape' ),
		'promo_image'       => array( __( 'Promo banner', 'overtheball' ), 'landscape' ),
		'about_image'       => array( __( 'About section', 'overtheball' ), 'portrait' ),
		'page_header_image' => array( __( 'Title banner (used when its style is "Background image")', 'overtheball' ), 'landscape' ),
	);
}

/**
 * Fill empty image slots from the picture library.
 *
 * @param bool $overwrite Replace slots that already have a picture.
 * @return array setting => attachment ID that was assigned.
 */
function otb_assign_pictures( $overwrite = false ) {
	$library = otb_theme_picture_ids();
	if ( ! $library ) {
		return array();
	}

	$used = array();
	foreach ( array_keys( otb_picture_slots() ) as $slot ) {
		$current = absint( get_theme_mod( $slot ) );
		if ( $current ) {
			$used[] = $current;
		}
	}

	$meta = array();
	foreach ( $library as $id ) {
		$data        = wp_get_attachment_metadata( $id );
		$width       = isset( $data['width'] ) ? (int) $data['width'] : 0;
		$height      = isset( $data['height'] ) ? (int) $data['height'] : 0;
		$meta[ $id ] = array(
			'area'  => $width * $height,
			'ratio' => $height ? $width / $height : 1,
		);
	}
	uasort(
		$meta,
		static function ( $a, $b ) {
			return $b['area'] - $a['area'];
		}
	);

	$assigned = array();
	foreach ( otb_picture_slots() as $slot => $slot_info ) {
		if ( ! $overwrite && absint( get_theme_mod( $slot ) ) ) {
			continue;
		}
		$pick     = 0;
		$fallback = 0;
		foreach ( $meta as $id => $info ) {
			if ( in_array( $id, $used, true ) ) {
				continue;
			}
			$fits = 'portrait' === $slot_info[1] ? $info['ratio'] <= 1.05 : $info['ratio'] >= 1.2;
			if ( $fits ) {
				$pick = $id;
				break;
			}
			if ( ! $fallback ) {
				$fallback = $id;
			}
		}
		$pick = $pick ? $pick : $fallback;
		if ( ! $pick ) {
			break;
		}
		set_theme_mod( $slot, $pick );
		$used[]            = $pick;
		$assigned[ $slot ] = $pick;
	}

	return $assigned;
}

/**
 * Import everything from a source, optionally within a time budget.
 *
 * @param string $source   Source slug.
 * @param int    $min_size Minimum longest side.
 * @param float  $budget   Seconds allowed (0 = unlimited).
 * @return array{imported: int, total: int, remaining: int}
 */
function otb_import_all_pictures( $source, $min_size = 120, $budget = 0 ) {
	$start = microtime( true );
	$scan  = otb_scan_pictures( $source, $min_size );
	$done  = 0;
	$total = count( $scan['import'] );

	foreach ( $scan['import'] as $item ) {
		if ( $budget && ( microtime( true ) - $start ) > $budget ) {
			break;
		}
		if ( $item['imported'] ) {
			otb_add_theme_pictures( array( $item['imported'] ) );
		} else {
			otb_import_picture( $source, $item['key'] );
		}
		++$done;
	}

	if ( 'otb-library' !== $source ) {
		otb_add_theme_pictures( otb_theme_mod_pictures( $source ) );
	}

	return array(
		'imported'  => $done,
		'total'     => $total,
		'remaining' => $total - $done,
	);
}

/**
 * On activation: remember the previous theme and bring its pictures over.
 *
 * @param string   $old_name  Previous theme name.
 * @param WP_Theme $old_theme Previous theme.
 */
function otb_pictures_on_activation( $old_name, $old_theme = null ) {
	if ( ! $old_theme instanceof WP_Theme || get_template() === $old_theme->get_stylesheet() ) {
		return;
	}

	$source = $old_theme->get_stylesheet();
	update_option( 'otb_previous_theme', $source, false );

	// Without a logged-in user (e.g. WP-CLI) the import is left for the Pictures screen.
	if ( ! current_user_can( 'upload_files' ) ) {
		return;
	}

	$result = otb_import_all_pictures( $source, 120, 20 );
	$extra  = otb_import_all_pictures( 'otb-library', 120, 10 );
	otb_assign_pictures();

	set_transient(
		'otb_pictures_notice',
		array(
			'theme'     => $old_theme->get( 'Name' ),
			'imported'  => $result['imported'] + $extra['imported'],
			'remaining' => $result['remaining'] + $extra['remaining'],
			'library'   => count( otb_theme_picture_ids() ),
		),
		DAY_IN_SECONDS
	);
}
add_action( 'after_switch_theme', 'otb_pictures_on_activation', 20, 2 );

/**
 * Admin notice after activation summarizing the picture import.
 */
function otb_pictures_admin_notice() {
	$notice = get_transient( 'otb_pictures_notice' );
	if ( ! $notice || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'appearance_page_overtheball' === $screen->id ) {
		delete_transient( 'otb_pictures_notice' );
	}
	$link = admin_url( 'themes.php?page=overtheball&tab=pictures' );
	echo '<div class="notice notice-info is-dismissible"><p>';
	if ( $notice['remaining'] > 0 ) {
		printf(
			/* translators: 1: number imported, 2: previous theme, 3: number remaining. */
			esc_html__( 'Over The Ball imported %1$d pictures from %2$s. %3$d more are waiting.', 'overtheball' ),
			(int) $notice['imported'],
			esc_html( $notice['theme'] ),
			(int) $notice['remaining']
		);
		echo ' <a class="button button-primary" href="' . esc_url( $link ) . '">' . esc_html__( 'Finish importing pictures', 'overtheball' ) . '</a>';
	} else {
		printf(
			/* translators: 1: number of pictures, 2: previous theme. */
			esc_html__( 'Over The Ball brought over %1$d pictures from %2$s and placed them in your homepage hero, promo, about section and gallery.', 'overtheball' ),
			(int) $notice['library'],
			esc_html( $notice['theme'] )
		);
		echo ' <a href="' . esc_url( $link ) . '">' . esc_html__( 'Review pictures', 'overtheball' ) . '</a>';
	}
	echo '</p></div>';
}
add_action( 'admin_notices', 'otb_pictures_admin_notice' );

/**
 * AJAX: import a batch of pictures.
 */
function otb_ajax_import_pictures() {
	check_ajax_referer( 'otb_pictures', 'nonce' );
	if ( ! current_user_can( 'upload_files' ) || ! current_user_can( 'edit_theme_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'You are not allowed to import pictures.', 'overtheball' ) ), 403 );
	}

	$source = isset( $_POST['source'] ) ? sanitize_text_field( wp_unslash( $_POST['source'] ) ) : '';
	$keys   = isset( $_POST['keys'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['keys'] ) ) : array();

	$results = array();
	foreach ( array_slice( $keys, 0, 10 ) as $key ) {
		$id              = otb_import_picture( $source, $key );
		$results[ $key ] = $id ? array(
			'id'    => $id,
			'thumb' => wp_get_attachment_image_url( $id, 'thumbnail' ),
		) : false;
	}

	wp_send_json_success( array( 'results' => $results ) );
}
add_action( 'wp_ajax_otb_import_pictures', 'otb_ajax_import_pictures' );

/**
 * AJAX: finish an import (theme-mod pictures + slot assignment).
 */
function otb_ajax_finish_pictures() {
	check_ajax_referer( 'otb_pictures', 'nonce' );
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_send_json_error( null, 403 );
	}
	$source = isset( $_POST['source'] ) ? sanitize_text_field( wp_unslash( $_POST['source'] ) ) : '';
	if ( $source && 'otb-library' !== $source && array_key_exists( $source, otb_picture_sources() ) ) {
		otb_add_theme_pictures( otb_theme_mod_pictures( $source ) );
	}
	$assigned = otb_assign_pictures();
	delete_transient( 'otb_pictures_notice' );
	wp_send_json_success(
		array(
			'assigned' => count( $assigned ),
			'library'  => count( otb_theme_picture_ids() ),
		)
	);
}
add_action( 'wp_ajax_otb_finish_pictures', 'otb_ajax_finish_pictures' );
