<?php

defined('ABSPATH') or die("Jog on!");

/**
 *  Run on every version change
 */
function yk_tt_upgrade() {

	if( update_option( 'yk-tt-version-number', YK_TT_CURRENT_VERSION ) ) {

		// Build DB tables
		yk_tt_db_tables_create();

		yk_tt_activate();

		do_action( 'yk_tt_db_upgrade' );
	}
}
add_action( 'admin_init', 'yk_tt_upgrade' );

/**
 * Set up cron jobs upon plugin activation
 */
function yk_tt_activate() {

	if ( !wp_next_scheduled( 'attendance_tracker_daily' ) ) {
		wp_schedule_event( time(), 'daily', 'attendance_tracker_daily' );
	}
}

/**
 * If we have missing database tables then attempt to fix!
 */
function yk_tt_missing_database_table_fix() {

	yk_tt_db_tables_create();

	do_action( 'yk_tt_db_fixed' );
}

/**
 * Check all database tables exist!
 * @return bool - return true if any tables are missing
 */
function yk_tt_missing_database_table_any_issues() {

	$error_text = '';
	global $wpdb;

	$tables_to_check = [    $wpdb->prefix . YK_TT_DB_DATA ];

	$count = $wpdb->get_var( 'SELECT COUNT(1) FROM information_schema.tables WHERE table_schema="' . DB_NAME .'" AND table_name in ( "' . implode('", "', $tables_to_check ) . '" )' );

	return ! ( count( $tables_to_check ) === (int) $count );
}
