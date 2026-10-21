<?php
/**
 * WP-CLI commands.
 *
 * Usage:
 *   wp overtheball pictures import [--source=<theme-slug>] [--min-size=<px>]
 *   wp overtheball pictures assign [--overwrite]
 *   wp overtheball pictures list
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * Manage pictures brought over from the previous theme.
 */
class OTB_Pictures_CLI {

	/**
	 * Import every picture from a theme (default: the previous theme) and fill empty image slots.
	 *
	 * ## OPTIONS
	 *
	 * [--source=<source>]
	 * : Theme stylesheet slug, or "otb-library" for assets/images/library.
	 *
	 * [--min-size=<px>]
	 * : Skip images whose longest side is smaller than this.
	 * ---
	 * default: 120
	 * ---
	 *
	 * @param array $args       Positional args.
	 * @param array $assoc_args Named args.
	 */
	public function import( $args, $assoc_args ) {
		$source = isset( $assoc_args['source'] ) ? $assoc_args['source'] : otb_previous_theme();
		if ( ! $source || ! array_key_exists( $source, otb_picture_sources() ) ) {
			WP_CLI::error( 'Unknown source. Available: ' . implode( ', ', array_keys( otb_picture_sources() ) ) );
		}
		$result   = otb_import_all_pictures( $source, absint( $assoc_args['min-size'] ) );
		$assigned = otb_assign_pictures();
		WP_CLI::success( sprintf( 'Imported %d pictures from %s; library now holds %d; filled %d image slots.', $result['imported'], $source, count( otb_theme_picture_ids() ), count( $assigned ) ) );
	}

	/**
	 * Fill image slots from the picture library.
	 *
	 * [--overwrite]
	 * : Replace pictures already chosen.
	 *
	 * @param array $args       Positional args.
	 * @param array $assoc_args Named args.
	 */
	public function assign( $args, $assoc_args ) {
		$assigned = otb_assign_pictures( ! empty( $assoc_args['overwrite'] ) );
		foreach ( $assigned as $slot => $id ) {
			WP_CLI::log( sprintf( '%s => #%d %s', $slot, $id, get_the_title( $id ) ) );
		}
		WP_CLI::success( sprintf( 'Filled %d image slots.', count( $assigned ) ) );
	}

	/**
	 * List the picture library.
	 *
	 * @subcommand list
	 */
	public function list_() {
		$rows = array();
		foreach ( otb_theme_picture_ids() as $id ) {
			$rows[] = array(
				'id'     => $id,
				'title'  => get_the_title( $id ),
				'source' => get_post_meta( $id, '_otb_source', true ),
			);
		}
		WP_CLI\Utils\format_items( 'table', $rows, array( 'id', 'title', 'source' ) );
	}
}

WP_CLI::add_command( 'overtheball pictures', 'OTB_Pictures_CLI' );
