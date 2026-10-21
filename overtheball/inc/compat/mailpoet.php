<?php
/**
 * MailPoet compatibility: form lookup for the Customizer, a sensible
 * default form, and themed form styles (assets/css/plugins.css).
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * Active MailPoet forms (id => name). Reads MailPoet's own table, which
 * MailPoet does not expose through its public API.
 *
 * @return array
 */
function otb_mailpoet_forms() {
	static $forms = null;
	if ( null !== $forms ) {
		return $forms;
	}
	$forms = array();

	if ( ! defined( 'MAILPOET_VERSION' ) ) {
		return $forms;
	}

	global $wpdb;
	$table = $wpdb->prefix . 'mailpoet_forms';

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
	if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
		return $forms;
	}

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is built from $wpdb->prefix.
	$rows = $wpdb->get_results( "SELECT id, name FROM {$table} WHERE deleted_at IS NULL AND (status IS NULL OR status = 'enabled') ORDER BY id ASC" );

	foreach ( (array) $rows as $row ) {
		/* translators: %d: MailPoet form ID. */
		$forms[ (int) $row->id ] = $row->name ? $row->name : sprintf( __( 'Form #%d', 'overtheball' ), $row->id );
	}

	return $forms;
}

/**
 * Form ID to render: the chosen form, otherwise the first active form.
 *
 * @return int
 */
function otb_mailpoet_form_id() {
	$chosen = absint( otb_option( 'mailpoet_form' ) );
	$forms  = otb_mailpoet_forms();

	if ( $chosen && isset( $forms[ $chosen ] ) ) {
		return $chosen;
	}
	if ( $chosen && ! $forms ) {
		// Table unreadable but a form was chosen; trust the setting.
		return $chosen;
	}
	return $forms ? (int) array_key_first( $forms ) : 0;
}
