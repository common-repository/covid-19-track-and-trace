<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Render [attendance-register] shortcode
 *
 * @param $user_arguments
 *
 * @return string
 */
function yk_tt_shortcode_form( $user_arguments ) {

	$user_arguments = shortcode_atts( [ 'default-venue' => '', 'enabled-autocomplete' => yk_tt_site_options_as_bool('yk-tt-auto-complete', false, false ) ], $user_arguments );

	$user_arguments[ 'enabled-autocomplete' ] = yk_tt_to_bool( $user_arguments[ 'enabled-autocomplete' ] );

	// Enqueue relevant JS / CSS
	yk_tt_enqueue_files();

	$html = '';

	// Do we have a POST? If so, process form, otherwise, display it!
	if ( false === empty( $_POST ) ) {

		if ( false === yk_tt_form_process_form() ) {
			$html = yk_tt_blockquote_error( __( 'There was an error saving your form! Please try again!', YK_TT_SLUG ) );
		} else {
			$html = yk_tt_blockquote_success( __( 'Your data has been successfully saved. We hope you enjoy your visit.', YK_TT_SLUG ) );
		}
	}

	return $html . yk_tt_form( $user_arguments );
}
add_shortcode( 'track-and-trace', 'yk_tt_shortcode_form' );
add_shortcode( 'attendance-register', 'yk_tt_shortcode_form' );
