<?php
defined('ABSPATH') or die("Jog on!");

/**
 * TTP plugin enabled?
 * @return bool
 */
function yk_tt_is_ttp_activated() {
	return function_exists('yk_ttp_is_tt_activated') ;
}

/**
 * Is there a newer version of premium plugin?
 * @return bool
 */
function yk_tt_is_premium_outdated(): bool {

	// If premium version isn't activated, then just return true as it isn't out of date
	if ( false === yk_tt_is_ttp_activated() ) {
		return false;
	}

	return ! ( YK_TT_PREMIUM_VERSION === YK_TTP_CURRENT_VERSION );
}

/**
 * Settings for rendering the form
 * @return array
 */
function yk_tt_form_settings() {

	// https://www.gov.uk/guidance/maintaining-records-of-staff-customers-and-visitors-to-support-nhs-test-and-trace

	$settings = [	'date-format'                   => yk_tt_site_options( 'date-format', 'dd/mm/yy' ),
					'enabled-name' 					=> yk_tt_site_options_as_bool('enabled-name' ),
					'enabled-set-name-display-name' => yk_tt_site_options_as_bool('enabled-set-name-display-name' ),
					'enabled-phone' 				=> yk_tt_site_options_as_bool('enabled-phone' ),
					'enabled-email' 				=> yk_tt_site_options_as_bool('enabled-email' ),
					'enabled-date' 					=> yk_tt_site_options_as_bool('enabled-date' ),
					'enabled-venue' 				=> yk_tt_site_options_as_bool('enabled-venue' ),
					'enabled-arrival-time' 			=> yk_tt_site_options_as_bool('enabled-arrival-time' ),
					'enabled-departure-time'		=> yk_tt_site_options_as_bool('enabled-departure-time', false, false ),
					'enabled-number-in-party'		=> yk_tt_site_options_as_bool('enabled-number-in-party', false, false ),
					'enabled-autocomplete'			=> yk_tt_site_options_as_bool('yk-tt-auto-complete', false, false ),
					'enabled-jquery-validation'		=> true,
					'force-to-todays-date' 			=> yk_tt_site_options_as_bool('force-to-todays-date', false, false ),
					'google-site-key'				=> yk_tt_site_options( 'google-site-key', '' ),
					'vendor-list'					=> yk_tt_site_options( 'yk-tt-venue-list', '', '' ),
					'userswp-venue'					=> yk_tt_site_options( 'yk-tt-userswp-venue', NULL, NULL ),
					'default-venue'					=> ''
	];

	if ( false === yk_tt_is_ttp_activated() ) {
		$settings[ 'enabled-departure-time' ] 	= false;
		$settings[ 'enabled-number-in-party' ] 	= false;
		$settings[ 'enabled-autocomplete' ] 	= false;
		$settings[ 'force-to-todays-date' ] 	= false;
		$settings[ 'userswp-venue' ] 			= NULL;
	}

	$settings[ 'recaptcha-enabled' ] = ( true === yk_tt_recaptcha_enabled() ) ? 'y' : 'n';

	return apply_filters( 'yk_tt_settings', $settings );
}

/**
 * Enqueue relevant JS / CSS
 */
function yk_tt_enqueue_files() {

	$settings = yk_tt_form_settings();

	// Date picker?
	if ( true === $settings[ 'enabled-date' ] ) {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-style', plugins_url( '../assets/css/jquery-ui.min.css', __FILE__ ), [], YK_TT_CURRENT_VERSION );
	}

	// Time / Duration picker?
	if ( true === $settings[ 'enabled-arrival-time' ] || true === $settings[ 'enabled-departure-time' ] ) {
		wp_enqueue_script( 'yk-tt-timepicker', plugins_url('../assets/js/jquery.timepicker.min.js', __FILE__), [ 'jquery' ], YK_TT_CURRENT_VERSION );
		wp_enqueue_style( 'yk-tt-timepicker', plugins_url( '../assets/css/jquery.timepicker.min.css', __FILE__ ), [], YK_TT_CURRENT_VERSION );
	}

	// Front end validation?
	if ( true === $settings[ 'enabled-jquery-validation' ] ) {

		wp_enqueue_script( 'yk-tt-validate', plugins_url('../assets/js/jquery.validate.min.js', __FILE__), [ 'jquery' ], YK_TT_CURRENT_VERSION );
		wp_enqueue_script( 'yk-tt-validate-methods', plugins_url('../assets/js/additional-methods.min.js', __FILE__), [ 'yk-tt-validate' ], YK_TT_CURRENT_VERSION );
		wp_enqueue_script( 'yk-tt', plugins_url('../assets/js/track-and-trace.js', __FILE__), [ 'yk-tt-validate-methods' ], YK_TT_CURRENT_VERSION, true );

		wp_localize_script( 'yk-tt', 'yk_tt_config', yk_tt_config_js() );
	}

	wp_enqueue_style( 'yk-tt', plugins_url('../assets/css/track-and-trace.css', __FILE__), [], YK_TT_CURRENT_VERSION );

	// Google recaptcha?
	if ( true === yk_tt_recaptcha_enabled() ) {
		wp_enqueue_script( 'yk-tt-recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . $settings[ 'google-site-key' ], [], YK_TT_CURRENT_VERSION );
	}
}

/**
 * Google recaptcha enabled?
 * @return bool
 */
function yk_tt_recaptcha_enabled() {
	return ( true === yk_tt_is_ttp_activated() &&
				false === empty( yk_tt_site_options( 'google-site-key', '' ) ) &&
					false === empty( yk_tt_site_options( 'google-secret', '' ) ) );
}

/**
 * Validate a recaptcha response token
 * @param $response
 * @return bool
 */
function yk_tt_recaptcha_validate( $response ) {

	if ( true === empty( $response ) ) {
		return false;
	}

	if ( false === yk_tt_recaptcha_enabled() ) {
		return false;
	}

	$data = [ 'secret' => yk_tt_site_options( 'google-secret', '' ), 'response' => $response ];

	$response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', [ 'body' => $data ] );

	if ( 200 === wp_remote_retrieve_response_code( $response ) ) {

		$body = wp_remote_retrieve_body( $response );

		if ( false === empty( $body ) ) {

			$body = json_decode( $body, true );

			if ( false === empty( $body[ 'success' ] ) && true === $body[ 'success' ] ) {
				return true;
			}
		}
	}

	return false;
}



/**
 * Return the config used for JS
 * @return array
 */
function yk_tt_config_js() {

	$config                         = yk_tt_form_settings();
	$config[ 'date-picker-locale' ] = yk_mt_config_js_datapicker_locale();

	if ( true === $config[ 'enabled-jquery-validation' ] ) {

		$config[ 'validation-rules' ]       = [];
		$config[ 'validation-messages' ]    = [];

		if ( true === $config[ 'enabled-name' ] ) {
			$config[ 'validation-rules' ][ 'full_name' ]     = [ 'required' => true ];
			$config[ 'validation-messages' ][ 'full_name' ]  = __( 'Please enter your name.', YK_TT_SLUG );
		}

		if ( true === $config[ 'enabled-phone' ] ) {
			$config[ 'validation-rules' ][ 'phone' ]     = [ 'required' => true ];
			$config[ 'validation-messages' ][ 'phone' ]  = __( 'Please enter a valid phone number.', YK_TT_SLUG );
		}

		if ( true === $config[ 'enabled-email' ] ) {
			$config[ 'validation-rules' ][ 'email' ]     = [ 'required' => true, 'email' => true ];
			$config[ 'validation-messages' ][ 'email' ]  = __( 'Please enter a valid email address.', YK_TT_SLUG );
		}

		if ( true === $config[ 'enabled-venue' ] ) {
			$config[ 'validation-rules' ][ 'venue' ]     = [ 'required' => true ];
			$config[ 'validation-messages' ][ 'venue' ]  = __( 'Please enter a valid venue.', YK_TT_SLUG );
		}

		if ( true === $config[ 'enabled-date' ] ) {
			$config[ 'validation-rules' ][ 'arrival_date' ] = [ 'required' => true ];

			// US or UK format?
			if ( 'mm/dd/yy' === $config[ 'date-format' ] ) {
				$config[ 'validation-rules' ][ 'arrival_date' ][ 'date' ] = true;
			} else {
				$config[ 'validation-rules' ][ 'arrival_date' ][ 'dateITA' ] = true;
			}


			$config[ 'validation-messages' ][ 'arrival_date' ]  = sprintf( '%s%s.',
																	__( 'Please enter a date in the following format: ', YK_TT_SLUG ),
																	$config[ 'date-format' ] );

		}

		if ( true === $config[ 'enabled-arrival-time' ] ) {
			$config[ 'validation-rules' ][ 'arrival_time' ]     = [ 'required' => true ];
			$config[ 'validation-messages' ][ 'arrival_time' ]  = __( 'Please enter an arrival time.', YK_TT_SLUG );
		}

		if ( true === $config[ 'enabled-departure-time' ] ) {
			$config[ 'validation-rules' ][ 'departure_time' ]     = [ 'required' => true ];
			$config[ 'validation-messages' ][ 'departure_time' ]  = __( 'Please enter a departure time.', YK_TT_SLUG );
		}

	}

	if ( true === is_admin() ) {
		$config[ 'admin-url' ] 		= remove_query_arg( [ 'summary-fetch', 'custom-date' ] );
	} else {

		// Include relevant fields for autocomplete.
		if ( true === $config[ 'enabled-autocomplete' ] ) {
			$config[ 'ajax-url' ]				= admin_url('admin-ajax.php');
			$config[ 'ajax-security-nonce' ]	= wp_create_nonce( 'yk-tt-nonce' );
		}
	}

	return apply_filters( 'yk_tt_js_config', $config );
}

/*
	Use a combination of WP Locale and MO file to translate datepicker
	Based on: https://gist.github.com/clubdeuce/4053820
 */
function yk_mt_config_js_datapicker_locale() {

	global $wp_locale;

	return [
		'closeText'         => __( 'Done', YK_TT_SLUG ),
		'currentText'       => __( 'Today', YK_TT_SLUG ),
		'monthNames'        => array_values( $wp_locale->month ),
		'monthNamesShort'   => array_values( $wp_locale->month_abbrev ),
		'dayNames'          => array_values( $wp_locale->weekday ),
		'dayNamesShort'     => array_values( $wp_locale->weekday_abbrev ),
		'dayNamesMin'       => array_values( $wp_locale->weekday_initial ),

		// get the start of week from WP general setting
		'firstDay'          => get_option( 'start_of_week' ),
	];
}

/**
* Return a randomised ID for user controls
* @return string
*/
function yk_tt_component_id() {
	return sprintf( 'yk_tt_%1$s_%2$s', mt_rand(), mt_rand() );
}
/**
 * Get the current page URL
 * @param bool $base_64_encode
 * @return mixed|string#
 */
function yk_tt_get_url( $base_64_encode = false ) {

	$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	// Wee hack, replace removedata querystring value
	$current_url = str_replace('removedata', 'removed', $current_url);

	return ( true === $base_64_encode ) ? base64_encode( $current_url ) : $current_url;
}

/**
 * Log to error lof=g
 * @param $text
 */
function yk_tt_error_log( $text ) {

	if ( true === empty( $text ) ) {
		return;
	}

	$text = sprintf( '%s: %s', YK_TT_SLUG, $text );

	error_log( $text );
}

/**
 * Helper function to use Blockquote
 *
 * @class ws-ls-success
 * @text
 * @param $text
 * @param string $class
 * @param bool $just_echo
 * @return string
 */
function yk_tt_display_blockquote( $text, $class = '', $just_echo = false ) {

	$html_output = sprintf('	<blockquote class="yk-tt-blockquote%s">
										<p>%s</p>
									</blockquote>',
		(false === empty( $class ) ) ? ' ' . esc_attr( $class ) : '',
		esc_html( $text )
	);

	if ( true === $just_echo ) {
		echo $html_output;
		return;
	}

	return $html_output;
}

/**
 * Display a success block quote
 * @param $text
 * @param string $class
 * @param bool $just_echo
 * @return string
 */
function yk_tt_blockquote_success( $text, $class = 'yk-tt-valid' , $just_echo = false ) {
	return yk_tt_display_blockquote( $text, $class, $just_echo );
}

/**
 * Display Error Block quote for an error
 * @param $text
 * @param string $class
 * @param bool $just_echo
 * @return string
 */
function yk_tt_blockquote_error( $text, $class = 'yk-tt-invalid', $just_echo = false ) {
	return yk_tt_display_blockquote( $text, $class, $just_echo );
}

/**
 * Convert date to ISO
 *
 * @param $date
 * @return string|null
 */
function yk_tt_convert_date_to_iso( $date ) {

	if ( true === empty( $date ) ) {
		return NULL;
	}

	$settings = yk_tt_form_settings();

	if ( 'dd/mm/yy' !== $settings[ 'date-format' ] ) {
		list( $month,$day,$year ) = sscanf( $date, "%d/%d/%d" );
		$date = "$year-$month-$day";
	} else {
		list( $day,$month,$year ) = sscanf( $date, "%d/%d/%d" );
		$date = "$year-$month-$day";
	}

	return $date;
}
/**
 * Display message in admin UI
 *
 * @param $text
 * @param bool $error
 */
function yk_tt_message_display( $text, $error = false ) {

	if ( true === empty( $text ) ) {
		return;
	}

	printf( '<div class="%s"><p>%s</p></div>',
		true === $error ? 'error' : 'updated',
		esc_html( $text )
	);
}

/**
 * Display an upgrade button
 */
function yk_tt_upgrade_button( $css_class = '', $link = NULL ) {

	$link = ( false === empty( $link ) ) ? $link : YK_TT_UPGRADE_LINK ;

	echo sprintf('<a href="%s" class="button-primary sh-cd-upgrade-button%s"><i></i> %s £%s</a>',
		esc_url( $link ),
		esc_attr( ' ' . $css_class ),
		__( 'Get the Premium Add-on for ', YK_TT_SLUG ),
		esc_html( YK_TT_PREMIUM_PRICE )
	);
}

/**
 * Render features array into HTML
 * @param $features
 */
function yk_tt_features_display() {

	$features = yk_tt_features_list();

	$html = '';

	if ( false === empty( $features ) ) {

		$class  = '';
		$html   = '<table class="form-table" >';

		foreach ( $features as $title => $description ) {

			if ( false === empty( $title ) ) {

				$class = ('alternate' == $class) ? '' : 'alternate';

				$html .= sprintf( '<tr valign="top" class="%1$s">
                                            <td scope="row" style="padding-left:30px"><label for="tablecell">
                                                    &middot; <strong>%2$s:</strong> %3$s.
                                                </label></td>

                                        </tr>',
					$class,
					esc_html( $title ),
					esc_html( $description )
				);
			}
		}

		$html .= '</table>';
	}
	return $html;
}

/**
 * Fetch a site option
 *
 * @param $key
 * @param bool $default
 * @param null $non_addon_default
 *
 * @return bool|mixed|void
 */
function yk_tt_site_options( $key, $default = true, $non_addon_default = NULL ) {

	// IF not a premium user, then default the value (regardless of setting)
	if ( NULL !== $non_addon_default && false === yk_tt_is_ttp_activated() ) {
		return $non_addon_default;
	}

	return get_option( $key, $default );
}

/**
 * Get a site option as a bool
 * @param $key
 * @param bool $default
 * @param null $non_addon_default
 * @return mixed
 */
function yk_tt_site_options_as_bool( $key, $default = true, $non_addon_default = NULL ) {

	// IF not a premium user, then default the value (regardless of setting)
	if ( NULL !== $non_addon_default && false === yk_tt_is_ttp_activated() ) {
		return $non_addon_default;
	}

	$value = yk_tt_site_options( $key, $default );

	return filter_var($value, FILTER_VALIDATE_BOOLEAN );
}

/**
 * Return an array of all features
 */
function yk_tt_features_list() {

	return [
		__( 'Force to today\'s date', YK_TT_SLUG )    						=> __( 'Save the user selecting today\'s date by forcing the date to be today', YK_TT_SLUG ),
		__( 'Restrict Venue to a selection (dropdown list)', YK_TT_SLUG )   => __( 'Instead of allowing users to manually enter their venue, provide and restrict them to dropdown list of venues', YK_TT_SLUG ),
		__( 'Search and autocomplete', YK_TT_SLUG )   						=> __( 'If enabled, when typing a user\'s full name, the plugin will search the WordPress user table. When a user is selected, the user\'s email address and name shall be auto completed', YK_TT_SLUG ),
		__( 'Telephone number from WooCommerce', YK_TT_SLUG )   			=> __( 'Auto complete telephone number from WooCommerce billing_phone field', YK_TT_SLUG ),
		__( 'Estimated Departure Time', YK_TT_SLUG )    					=> __( 'An additional field to specify the estimated departure time', YK_TT_SLUG ),
		__( 'Number of People in Party', YK_TT_SLUG )   					=> __( 'An additional field to specify the number of users in the party', YK_TT_SLUG ),
		__( 'Google reCaptcha', YK_TT_SLUG )   								=> __( 'Reduce the risk of Spam form submissions with Google reCaptcha support', YK_TT_SLUG ),
		__( 'View entries for any day', YK_TT_SLUG )   						=> __( 'Additional options on the entries page for fetching entries for any given date', YK_TT_SLUG ),
		__( 'GDPR: Auto Delete older entries', YK_TT_SLUG )   				=> __( 'Every day, entries older than x days can be automatically removed form your database (requires premium version 1.1.3+)', YK_TT_SLUG ),
		__( 'Export to CSV', YK_TT_SLUG )   								=> __( 'Export your Track and Trace entries to CSV', YK_TT_SLUG )
	];
}


/**
 * HTML for mention of custom work
 */
function yk_tt_custom_notification_html() {
	?>

	<p><img src="<?php echo plugins_url( 'admin-pages/assets/images/yeken-logo.png', __FILE__ ); ?>" width="100" height="100" style="margin-right:20px" align="left" /><?php echo __( 'If require plugin modifications to Attendance Register, or need a new plugin built, or perhaps you need a developer to help you with your website then please don\'t hesitate get in touch!', YK_TT_SLUG ); ?></p>
	<p><strong><?php echo __( 'We provide fixed priced quotes.', YK_TT_SLUG ); ?></strong></p>
	<p><a href="https://www.yeken.uk" rel="noopener noreferrer" target="_blank">YeKen.uk</a> /
		<a href="https://profiles.wordpress.org/aliakro" rel="noopener noreferrer" target="_blank">WordPress Profile</a> /
		<a href="mailto:email@yeken.uk" >email@yeken.uk</a></p>
	<br clear="both"/>
	<?php
}
/**
 * Display upgrade notice
 *
 * @param bool $pro_plus
 */
function yk_tt_display_pro_upgrade_notice( ) {
	?>

	<div class="postbox yk-tt-advertise-premium">
		<h3 class="hndle"><span><?php echo __( 'Purchase the Track and Trace Premium Add-on and get more features!', YK_TT_SLUG ); ?> </span></h3>
		<div style="padding: 0px 15px 0px 15px">
			<p><?php echo __( 'Purchase the Track and Trace Premium Add-on to get additional features: view data for any day, view all fields, additional fields, Google reCaptcha and export to CSV.', YK_TT_SLUG ); ?></p>
			<p><a href="<?php echo esc_url( admin_url('admin.php?page=yk-tt-license') ); ?>" class="button-primary"><?php echo __( 'Purchase the Premium Add-on', YK_TT_SLUG ); ?></a></p>
		</div>
	</div>

	<?php
}

/**
 * Display all user's entries in a data table
 */
function yk_tt_table_user_entries() {

	$settings = yk_tt_form_settings();

	$db_args[ 'arrival-date' ] = date( 'y-m-d' );

	$entries = yk_tt_db_entries_summary( $db_args );

	?>
	<br />
	<table id="yk-tt-entries" class="yk-tt-footable yk-tt-footable-basic widefat" data-paging="true" data-state="true" data-use-parent-width="true" data-toggle="true" data-filtering="true">
		<thead>
		<tr>
			<th data-type="text"><?php echo __( 'Name', YK_TT_SLUG ); ?></th>
			<th data-type="text"><?php echo __( 'Email', YK_TT_SLUG ); ?></th>
			<th data-type="text"><?php echo __( 'Phone', YK_TT_SLUG ); ?></th>
			<th data-type="date" data-format-string="<?php echo ( 'dd/mm/yy' == $settings[ 'date-format' ] ) ? 'D/M/Y' : 'M/D/Y'; ?>"><?php echo __( 'Arrival Date', YK_TT_SLUG ); ?></th>
			<th data-type="text"><?php echo __( 'Arrival Time', YK_TT_SLUG ); ?></th>
			<th data-type="text"><?php echo __( 'Departure Time', YK_TT_SLUG ); ?></th>
			<th data-type="text"><?php echo __( 'Venue', YK_TT_SLUG ); ?></th>
			<th data-type="text" data-breakpoints="sm"><?php echo __( 'Number in Party', YK_TT_SLUG ); ?></th>
			<th data-breakpoints="sm" data-type="text"><?php echo __( 'Added By', YK_TT_SLUG ); ?></th>
		</tr>
		</thead>
		<?php
		foreach ( $entries as $entry ) {

			printf ( '    <tr>
									<td>%1$s</td>
									<td>%2$s</td>
									<td>%3$s</td>
									<td>%4$s</td>
									<td class="yk-tt-blur">%5$s</td>
									<td class="yk-tt-blur">%6$s</td>
									<td class="yk-tt-blur">%9$s</td>
									<td class="yk-tt-blur">%7$s</td>
									<td>%8$s</td>
								</tr>',
				esc_html( $entry[ 'full_name' ] ),
				esc_html( $entry[ 'email' ] ),
				esc_html( $entry[ 'phone' ] ),
				yk_tt_date_format( $entry[ 'arrival_date' ], $settings[ 'date-format' ] ),
				yk_tt_blur_text( $entry[ 'arrival_time' ] ),
				yk_tt_blur_text( $entry[ 'departure_time' ] ),
				yk_tt_blur_text( $entry[ 'number_in_party' ] ),
				esc_html( $entry[ 'added' ] ),
				yk_tt_blur_text( $entry[ 'venue' ] )
			);
		}
		?>
		</tbody>
	</table>
	<?php

}

/*
 * Format an ISO date
 */
function yk_tt_date_format( $iso_date, $format = 'dd/mm/yy' ) {

	if ( true === empty( $iso_date ) || '0000-00-00 00:00:00' === $iso_date ) {
		return '-';
	}

	$format = ( 'dd/mm/yy' == $format ) ? 'd/m/Y' : 'm/d/Y';

	$time = strtotime( $iso_date );

	return date( $format, $time );
}

/**
 * Render out links for options
 *
 * @param $key
 * @param $default
 * @param $options
 * @param null $cache_notice
 * @param null $prepend
 * @param string $page_slug
 * @param string $date_picker_value
 */
function yk_tt_admin_option_links( $key, $default,  $options, $cache_notice = NULL, $prepend = NULL, $page_slug = 'yk-tt-entries', $date_picker_value = '' ) {

	if ( false === is_array( $options ) ||
		true === empty( $options ) ) {
		return;
	}

	$current_selected = yk_tt_site_options( $key, $default );

	$url = yk_tt_link_admin_page( $page_slug );

	echo '<div class="yk-tt-link-group">';

	if ( false === empty( $prepend ) ) {
		echo esc_html( $prepend );
	}

	printf(     '<input type="text" class="yk-mt-datepicker" placeholder="%s" size="12" value="%s"/> &middot; ', __('Pick a date', YK_TT_SLUG ), esc_html( $date_picker_value ) );

	foreach ( $options as $option_key => $option_name ) {
		printf(     '<a href="%1$s" class="%2$s button">%3$s</a> ',
			esc_url( add_query_arg( $key , $option_key, $url ) ),
			( $current_selected === $option_key ) ? 'yk-tt-selected' : '',
			esc_html( $option_name )
		);
	}

	if ( false === empty ( $cache_notice ) &&
		true === yk_tt_site_options_as_bool('caching-enabled' ) ) {

		printf( '<small>%1$s %2$d %3$s.</small>', __('The above table updates every', YK_TT_SLUG ), $cache_notice, __('minutes', YK_TT_SLUG ) );
	}

	echo '</div>';
}

/**
 * Build Admin URL link
 * @param $slug
 *
 * @return string|void
 */
function yk_tt_link_admin_page( $slug = 'yk-tt-settings' ) {

	$url = sprintf( 'admin.php?page=%s', $slug );

	return admin_url( $url );
}

/**
 * Blur string if incorrect license
 *
 * @param $text
 * @param bool $number_format
 * @return string
 */
function yk_tt_blur_text( $text, $number_format = false ) {

	if ( true === yk_tt_is_ttp_activated() ) {

		return ( true === $number_format ) ?
			yk_tt_format_number( $text ) :
			$text;
	}

	$text = str_repeat( '0', strlen( $text ) + 1 );

	return esc_html( $text );
}

/**
 * Helper function for formatting numbers
 * @param $number
 * @param $decimals
 * @return string
 */
function yk_tt_format_number( $number, $decimals = 0 ) {
	return number_format( $number, $decimals );
}

/**
 * Fetch a value from the $_GET array
 *
 * @param $key
 * @param null $default
 *
 * @return null
 */
function yk_tt_querystring_value( $key, $default = NULL ) {
	return ( false === empty( $_GET[ $key ] ) ) ? $_GET[ $key ] : $default;
}
/**
 * Convert string to bool
 * @param $string
 * @return mixed
 */
function yk_tt_to_bool( $string ) {
	return filter_var( $string, FILTER_VALIDATE_BOOLEAN );
}
