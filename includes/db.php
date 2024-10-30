<?php

defined('ABSPATH') or die("Jog on!");

define( 'YK_TT_DB_DATA', 'yk_tt_data' );

/**
 *  Build the relevant database tables
 */
function yk_tt_db_tables_create() {

	global $wpdb;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	// -------------------------------------------------
	// Data Table
	// -------------------------------------------------

	$table_name = $wpdb->prefix . YK_TT_DB_DATA;

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                full_name varchar(40) NOT NULL,
                phone varchar(40) NOT NULL,
                email varchar(200) NOT NULL,
                arrival_date datetime NOT NULL,
                arrival_time varchar(10) NOT NULL,
                departure_time varchar(10) NOT NULL,
                venue varchar(100) NOT NULL,
                number_in_party varchar(10) NOT NULL,
                added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
             UNIQUE KEY id (id)
            ) $charset_collate;";

	dbDelta( $sql );
}

/**
 * Add user data
 *
 * @param $data
 * @return bool     true if success
 */
function yk_tt_db_entry_add( $data ) {

	global $wpdb;

	$formats    = yk_tt_db_mysql_formats( $data );
	$data       = array_map( 'sanitize_text_field', $data );

	$result = $wpdb->insert( $wpdb->prefix . YK_TT_DB_DATA , $data, $formats );

	if ( false === $result ) {
		return false;
	}

	return $wpdb->insert_id;
}

/**
 * Get summary data for entries
 *
 * @param $args
 * @return null|string
 */
function yk_tt_db_entries_summary( $args ) {

	$args = wp_parse_args( $args, [
		'last-x-days'   => NULL,
		'arrival-date'  => NULL,
		'limit'         => NULL,
		'sort'          => 'arrival_date desc, arrival_time desc, added desc'
	]);

	global $wpdb;

	$sql = 'Select * from ' . $wpdb->prefix . YK_TT_DB_DATA . ' where 1=1 ';

	if ( false === empty( $args[ 'last-x-days' ] ) ) {
		$sql .= sprintf( ' and added >= NOW() - INTERVAL %d DAY and added <= NOW()', $args[ 'last-x-days' ] );
	}

	if ( false === empty( $args[ 'arrival-date' ] ) ) {
		$sql .= $wpdb->prepare( ' and arrival_date = %s', $args[ 'arrival-date' ] );
	}

	$sql .= sprintf( ' order by %s', $args[ 'sort' ] );

	// Limit
	if ( false === empty( $args[ 'limit' ] ) ) {
		$sql .= sprintf( ' limit 0, %d', $args[ 'limit' ] ) ;
	}

	$results = $wpdb->get_results( $sql, ARRAY_A );

	return $results;
}

/**
 * Return data formats
 *
 * @param $data
 * @return array
 */
function yk_tt_db_mysql_formats( $data ) {

	$formats = yk_tt_db_mysql_formats_raw();

	$formats = apply_filters( 'yk_tt_db_formats', $formats );

	$return = [];

	foreach ( $data as $key => $value) {
		if ( false === empty( $formats[ $key ] ) ) {
			$return[] = $formats[ $key ];
		}
	}

	return $return;
}

/**
 * Return formats
 * @return array
 */
function yk_tt_db_mysql_formats_raw() {
	return [
		'id'                    => '%d',
		'full_name'             => '%s',
		'phone'              	=> '%s',
		'venue'              	=> '%s',
		'email'        			=> '%s',
		'arrival_date'          => '%s',
		'arrival_time'          => '%s',
		'departure_time'        => '%s',
		'number_in_party'       => '%s'
	];
}

/**
 * Strip data values that won't go into DB
 * @param $data
 * @return array|mixed
 */
function yk_tt_db_mysql_strip_invalid( $data ) {

	if ( true === empty( $data ) ) {
		return [];
	}

	$formats 		= yk_tt_db_mysql_formats_raw();
	$allowed_keys 	= array_keys( $formats );

	$data = array_filter( $data, function( $key ) use( $allowed_keys ) {
		return in_array( $key, $allowed_keys ) ? true : false;
	}, ARRAY_FILTER_USE_KEY );

	return $data;
}
