<?php

defined('ABSPATH') or die('Jog on!');


/**
 * Build admin menu
 */
function yk_tt_build_admin_menu() {

	add_menu_page( YK_TT_TITLE_SHORT, YK_TT_TITLE_SHORT, 'manage_options', 'yk-tt-main-menu', '', 'dashicons-id-alt' );

	// Hide duplicated sub menu (wee hack!)
	add_submenu_page( 'yk-tt-main-menu', '', '', 'manage_options', 'yk-tt-main-menu', 'yk_tt_help_page');

	add_submenu_page( 'yk-tt-main-menu', __( 'How to use', YK_TT_SLUG ),  __( 'How to use', YK_TT_SLUG ), 'manage_options', 'yk-tt-help', 'yk_tt_help_page' );

	//yk_ttp_admin_page_dashboard

	$dashboard_page = apply_filters( 'yk-tt-admin-page-dashboard', 'yk_tt_admin_page_dashboard' );

	add_submenu_page( 'yk-tt-main-menu', __( 'Entries', YK_TT_SLUG ),  __( 'Entries', YK_TT_SLUG ), 'manage_options', 'yk-tt-entries', $dashboard_page );

	if ( false === yk_tt_is_ttp_activated() ) {
		add_submenu_page( 'yk-tt-main-menu', __( 'Upgrade to Premium', YK_TT_SLUG ),  __( 'Get Premium Add-on', YK_TT_SLUG ), 'manage_options', 'yk-tt-license', 'yk_tt_advertise_pro');
	}

	add_submenu_page( 'yk-tt-main-menu', __( 'Settings', YK_TT_SLUG ),  __( 'Settings', YK_TT_SLUG ), 'manage_options', 'yk-tt-settings', 'yk_tt_settings_page_generic' );

}
add_action( 'admin_menu', 'yk_tt_build_admin_menu' );


/**
 * Enqueue admin JS / CSS
 */
function yk_tt_enqueue_admin_files() {

	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_style( 'jquery-style', plugins_url( '../assets/css/jquery-ui.min.css', __FILE__ ), [], YK_TT_CURRENT_VERSION );

	wp_enqueue_style( 'tt-font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', [], YK_TT_CURRENT_VERSION );
	wp_enqueue_style( 'tt-footable', plugins_url( '/assets/css/footable.standalone.min.css', __DIR__  ), [], YK_TT_CURRENT_VERSION );
	wp_enqueue_style( 'tt-admin', plugins_url('/assets/css/admin.css', __DIR__ ), [], YK_TT_CURRENT_VERSION );

	wp_enqueue_script( 'tt-footable', plugins_url( '/assets/js/footable.min.js', __DIR__ ), [ 'jquery', 'moment' ], YK_TT_CURRENT_VERSION, true );

	$admin_js_path = apply_filters( 'yk-tt-admin-js', plugins_url( '/assets/js/admin.js', __DIR__ ) );

	wp_enqueue_script( 'tt-admin', $admin_js_path, [ 'tt-footable', 'jquery-ui-datepicker' ], YK_TT_CURRENT_VERSION, true );

	wp_localize_script( 'tt-admin', 'yk_tt_config', yk_tt_config_js() );

}
add_action( 'admin_enqueue_scripts', 'yk_tt_enqueue_admin_files');

/**
 * Add class to admin body if Premium or not
 * @param $classes
 * @return array
 */
function yk_tt_admin_add_body_classes( $existing_classes ) {

	$class = ( true === yk_tt_is_ttp_activated() ) ? 'yk-tt-is-premium' : 'yk-tt-not-premium' ;

	return sprintf('%1$s %2$s', $existing_classes, $class );
}
add_filter( 'admin_body_class', 'yk_tt_admin_add_body_classes' );

/**
 * Display a notice if there is a premium version update
 */
function yk_tt_display_premium_outdated_notice() {

	if ( false === yk_tt_is_premium_outdated() ) {
		return;
	}

	$page = yk_tt_querystring_value( 'page' );

	if ( ! in_array( $page, [ 'yk-tt-entries', 'yk-tt-help', 'yk-tt-settings' ] ) ) {
		return;
	}

	$message = __( 'There is a new version of the Attendance Tracker Premium plugin! Download it here', YK_TT_SLUG );

	printf( '<div class="notice notice-error"><p>%1$s (%2$s): <a href="https://shop.yeken.uk/my-account/downloads/" >https://shop.yeken.uk/my-account/downloads/</a></p></div>',  esc_html( $message ), YK_TT_PREMIUM_VERSION );
}
add_action( 'admin_notices', 'yk_tt_display_premium_outdated_notice' );
