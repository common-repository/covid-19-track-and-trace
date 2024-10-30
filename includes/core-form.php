<?php

defined('ABSPATH') or die('Jog on!');

$yk_tt_tab_index = 1;

/*
	Displays form
*/
function yk_tt_form( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, yk_tt_form_settings() ) ;

	if ( true === $arguments[ 'enabled-autocomplete' ] ) {
		yk_ttp_enqueue_files();
	}

	$html = '';

	$arguments[ 'post-url' ] = yk_tt_get_url();

	$html .= sprintf( ' <div class="yk-tt-error-summary">
                            <ul></ul>
                        </div>
                        <form action="%1$s" method="post" class="yk-tt-form" id="%2$s">
							<input type="hidden" name="security" value="%3$s" />',
		esc_url_raw( $arguments[ 'post-url' ] ),
		yk_tt_component_id(),
		wp_create_nonce( 'track-and-trace-form' )
	);

	if ( true === $arguments[ 'enabled-name' ] ) {

		if ( true === $arguments[ 'enabled-autocomplete' ] ) {
			$html .= yk_tt_form_field_select( [ 'name'        => 'full_name',
			                                    'label'       => __( 'Name', YK_TT_SLUG ),
			                                    'values'	  => [],
												'css-class'   => 'autocomplete-fullname'
			]);
		} else {

			$display_name = '';

			if ( true === $arguments[ 'enabled-set-name-display-name' ] ) {

				$user = get_userdata( get_current_user_id() );

				if ( false === empty( $user ) ) {
					$display_name = $user->display_name;
				}
			}

			$display_name_exists = ! empty( $display_name );

			$html .= yk_tt_form_field_text( [ 'name' => 'full_name', 'title' => __( 'Name', YK_TT_SLUG ), 'value' => $display_name, 'disabled' => $display_name_exists ] );

			if ( true === $display_name_exists ) {
				$html .= sprintf('<input type="hidden" name="full_name" value="%s" />', esc_attr( $display_name ));
			}
		}
	}

	if ( true === $arguments[ 'enabled-phone' ] ) {
		$html .= yk_tt_form_field_text( [ 'name' => 'phone', 'title' => __( 'Telephone number', YK_TT_SLUG ) ] );
	}

	if ( true === $arguments[ 'enabled-email' ] ) {
		$html .= yk_tt_form_field_text( [ 'name' => 'email', 'title' => __( 'Email address', YK_TT_SLUG ), 'maxlength' => 200 ] );
	}

	if ( true === $arguments[ 'enabled-venue' ] ) {

		// Are we trying to load the venue from a UsersWP field?
		if ( false === empty( $arguments['userswp-venue'] ) &&
		        true === is_user_logged_in() &&
					true === function_exists( 'yk_ttp_userwp_get_field' ) &&
		                $value = yk_ttp_userwp_get_field( get_current_user_id(), $arguments['userswp-venue'] )) {

			$venue_exists   = ! empty( $value );

			$html .= yk_tt_form_field_text( [ 'name' => 'venue', 'title' => __( 'Venue', YK_TT_SLUG ), 'maxlength' => 100, 'value' => $value, 'disabled' => $venue_exists ] );

			if ( true === $venue_exists ) {
				$html .= sprintf('<input type="hidden" name="venue" value="%s" />', esc_attr( $value ));
			}

		// Do we have a restricted list?
		} elseif ( false === empty( $arguments[ 'vendor-list' ] ) ) {

			$arguments[ 'vendor-list' ] = explode( '|', $arguments[ 'vendor-list' ] );

			$arguments[ 'vendor-list' ] = array_combine( $arguments[ 'vendor-list' ], $arguments[ 'vendor-list' ] );

			$html .= yk_tt_form_field_select( [ 'name'        => 'venue',
			                                    'label'       => __( 'Venue', YK_TT_SLUG ),
			                                    'css-class'   => 'venue',
			                                    'values'	  => $arguments[ 'vendor-list' ],
												'selected'    => $arguments[ 'default-venue' ]
			]);

		} else {
			$html .= yk_tt_form_field_text( [ 'name' => 'venue', 'title' => __( 'Venue', YK_TT_SLUG ), 'maxlength' => 100, 'value' => $arguments[ 'default-venue' ] ] );
		}
	}

	if ( true === $arguments[ 'force-to-todays-date' ] ) {

		$php_format = ( 'dd/mm/yy' == $arguments[ 'date-format' ] ) ? 'd/m/y' : 'm/d/y';

		$html .= sprintf('<input type="hidden" name="arrival_date" value="%s" data-format="%s" />', date( $php_format ), $arguments[ 'date-format' ] );

	} elseif ( true === $arguments[ 'enabled-date' ] ) {
		$html .= yk_tt_form_field_date( [ 'name' => 'arrival_date', 'title' => __( 'Date', YK_TT_SLUG ) ] );
	}

	if ( true === $arguments[ 'enabled-arrival-time' ] ) {
		$html .= yk_tt_form_field_text( [   'name'        	=> 'arrival_time',
		                                    'title'       	=> __( 'Arrival Time', YK_TT_SLUG ),
		                                    'css-class'   	=> 'yk-tt-arrival-time',
											'maxlength'		=> 10
		]);
	}

	if ( true === $arguments[ 'enabled-departure-time' ] ) {
		$html .= yk_tt_form_field_text( [   'name'        => 'departure_time',
		                                    'title'       => __( 'Estimated Departure Time', YK_TT_SLUG ),
		                                    'css-class'   => 'yk-tt-departure-time',
											'maxlength'		=> 10
		]);
	}

	if ( true === $arguments[ 'enabled-number-in-party' ] ) {
		$html .= yk_tt_form_field_select( [ 'name'        => 'number_in_party',
											'label'       => __( 'Number of people in your party', YK_TT_SLUG ),
											'css-class'   => 'number-in-party',
											'values'	  => [ '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10+' ]

		]);
	}

	$html .= sprintf( '	<div id="yk-tt-additional-info"></div>
								<input type="hidden" name="google-captcha" id="google-captcha" value="">
								<input type="submit" value="%s">', __( 'Submit your data', YK_TT_SLUG ) );
	$html .= '
						</div>
					</div>
				</div>
			</form>';

	return $html;
}

/**
 * Process form post!
 * @return bool
 */
function yk_tt_form_process_form() {

	if ( true === empty( $_POST ) ) {
		yk_tt_error_log( ' Nothing found in form post!' );
		return false;
	}

	// Check form posted from this site
	if ( false === wp_verify_nonce( $_POST[ 'security' ], 'track-and-trace-form' ) ) {
		return false;
	}

	// Validate recaptcha
	if ( true === yk_tt_recaptcha_enabled() && false === yk_tt_recaptcha_validate( $_POST[ 'google-captcha' ] ) ) {
		yk_tt_error_log( ' Issue with recaptcha' );
		return false;
	}

	$data =  $_POST;

	// Fetch all keys that we are interested in
	$data = yk_tt_db_mysql_strip_invalid( $data );

	// Convert arrival data to ISO
	if ( false === empty( $data[ 'arrival_date' ] ) ) {
		$data[ 'arrival_date' ] = yk_tt_convert_date_to_iso( $data[ 'arrival_date' ] );
	}

	return yk_tt_db_entry_add( $data );
}

/**
 * Display a text field
 * @param array $arguments
 * @return string
 */
function yk_tt_form_field_text( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [	'type'                  => 'text',
												'name'                  => '',
												'value'                 => NULL,
												'placeholder'           => NULL,
												'show-label'            => true,
												'title'                 => '',
												'css-class'             => '',
												'size'                  => 22,
												'maxlength'             => 40,
												'trailing-html'         => '',
												'include-div'           => true,
												'required' 				=> true,
												'disabled' 				=> false
	] );

	$html = '';

	$arguments[ 'id' ] = sanitize_title( $arguments[ 'name' ] );

	if ( true === $arguments[ 'include-div' ] ) {
		$html .= sprintf( '<div id="%1$s-row" class="yk-tt-form-row">', $arguments[ 'name' ] );
	}

	if ( true === $arguments[ 'show-label' ] ) {
		$html .= sprintf( '<label for="%1$s" class="">%2$s</label>', $arguments[ 'id' ], $arguments[ 'title' ]);
	}

	$html .= sprintf( '<input type="text" name="%1$s" id="%2$s" tabindex="%3$d" value="%4$s" placeholder="%5$s" maxlength="%9$s" size="%9$d" class="%7$s" %8$s %10$s />',
		$arguments[ 'name' ],
		esc_attr( $arguments[ 'id' ] ),
		yk_tt_form_tab_index_next(),
		esc_attr( $arguments[ 'value' ] ),
		esc_attr( $arguments[ 'placeholder' ] ),
		$arguments[ 'size' ],
		$arguments[ 'name' ] . ' ' . $arguments[ 'css-class' ],
		( true === $arguments[ 'required' ] ) ? 'required="required"' : '',
		$arguments[ 'maxlength' ],
		( true === $arguments[ 'disabled' ] ) ? 'disabled="disabled"' : ''
	);

	if ( false === empty( $arguments[ 'trailing-html' ] ) ) {
		$html .= $arguments[ 'trailing-html' ];
	}

	if ( true === $arguments[ 'include-div' ] ) {
		$html .= '</div>';
	}

	return $html;
}

/**
 * Display a date field
 * @param array $arguments
 *
 * @return string
 */
function yk_tt_form_field_date( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [	'type'                  => 'date',
												'name'                  => '',
												'value'                 => NULL,
												'placeholder'           => NULL,
												'show-label'            => true,
												'title'                 => '',
												'css-class'             => 'yk-mt-datepicker',
												'css-class-row'         => '',
												'size'                  => 22,
												'trailing-html'         => '',
												'include-div'           => true,
												'required'         		=> true,
												'disabled'         		=> false,
		]);
	$html = '';

	$arguments[ 'id' ] = sanitize_title( $arguments[ 'name' ] );

	if ( true === $arguments[ 'include-div' ] ) {
		$html .= sprintf( '<div id="%1$s-row" class="yk-tt-form-row%2$s">', $arguments[ 'name' ], ( false === empty( $arguments[ 'css-class-row' ] ) ) ? ' ' . esc_attr( $arguments[ 'css-class-row' ] ) : '' );
	}

	if ( true === $arguments[ 'show-label' ] ) {
		$html .= sprintf( '<label for="%1$s" class="">%2$s</label>', $arguments[ 'id' ], $arguments[ 'title' ] );
	}

	$html .= sprintf( '<input type="text" name="%1$s" id="%2$s" tabindex="%3$d" value="%4$s" placeholder="%5$s" size="%6$d" class="%7$s" %8$s %9$s />',
		$arguments[ 'name' ],
		esc_attr( $arguments[ 'id' ] ),
		yk_tt_form_tab_index_next(),
		esc_attr( $arguments[ 'value' ] ),
		esc_attr( $arguments[ 'placeholder' ] ),
		$arguments[ 'size' ],
		$arguments[ 'name' ] . ' ' . $arguments[ 'css-class' ],
		( true === $arguments[ 'required' ] ) ? 'required="required"' : '',
		( true === $arguments[ 'disabled' ] ) ? 'disabled="disabled"' : ''
	);

	if ( false === empty( $arguments[ 'trailing-html' ] ) ) {
		$html .= $arguments[ 'trailing-html' ];
	}

	if ( true === $arguments[ 'include-div' ] ) {
		$html .= '</div>';
	}

	return $html;
}

/**
 * HTML field for textarea
 * @param array $arguments
 *
 * @return string
 */
function yk_tt_form_field_textarea( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [	'type'                  => 'date',
												'name'                  => '',
												'value'                 => NULL,
												'placeholder'           => __( 'Notes', WE_LS_SLUG ),
												'show-label'            => false,
												'title'                 => '',
												'css-class'             => '',
												'trailing-html'         => '',
												'cols'                  => 39,
												'rows'                  => 4
	]);

	$html = sprintf( '<div id="%1$s-row" class="yk-tt-form-row">', $arguments[ 'name' ] );

	if ( true === $arguments[ 'show-label' ] ) {
		$html .= sprintf( '<label for="%1$s" class="yk-mt__label %3$s">%2$s</label>', $arguments[ 'name' ], $arguments[ 'title' ], $arguments[ 'css-class' ] );
	}

	$html .= sprintf( '<textarea name="%1$s" id="%1$s" tabindex="%2$d" placeholder="%3$s" cols="%4$d" rows="%5$d" class="%6$s" >%7$s</textarea>',
		$arguments[ 'name' ],
		yk_tt_form_tab_index_next(),
		esc_attr( $arguments[ 'placeholder' ] ),
		$arguments[ 'cols' ],
		$arguments[ 'rows' ],
		$arguments[ 'name' ] . ' ' . $arguments[ 'css-class' ],
		esc_textarea( $arguments[ 'value' ] )
	);

	if ( false === empty( $arguments[ 'trailing-html' ] ) ) {
		$html .= $arguments[ 'trailing-html' ];
	}

	return $html . '</div>';
}



/**
 * Render a check box field
 * @param array $arguments
 * @return string
 */
function yk_tt_form_field_checkbox( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [	'type'                  => 'text',
												'id'                    => yk_tt_component_id(),
												'name'                  => '',
												'value'                 => NULL,
												'checked'               => false,
												'show-label'            => false,
												'css-class'             => '',
												'include-div'           => true,
												'required' 				=> false ]);
	$html = '';

	if ( true === $arguments[ 'include-div' ] ) {
		$html .= sprintf( '<div id="%1$s-row" class="yk-tt-form-row">', $arguments[ 'name' ] );
	}

	$html .= sprintf( '<input type="checkbox" name="%1$s" id="%2$s" tabindex="%3$d" value="%4$s" class="%5$s" %6$s />',
		$arguments[ 'name' ],
		esc_attr( $arguments[ 'id' ] ),
		yk_tt_form_tab_index_next(),
		esc_attr( $arguments[ 'value' ] ),
		$arguments[ 'css-class' ],
		true === $arguments[ 'checked' ] ? ' checked="checked" ' : ''
	);

	if ( true === $arguments[ 'show-label' ] ) {
		$html .= sprintf( '<label for="%1$s" class="">%2$s</label>', $arguments[ 'id' ], $arguments[ 'title' ]);
	}

	if ( true === $arguments[ 'include-div' ] ) {
		$html .= '</div>';
	}

	return $html;
}

/**
 * Render a <select> for the given key / value array
 *
 * @param $arguments
 *
 * @return string
 */
function yk_tt_form_field_select( $arguments ) {

	$arguments = wp_parse_args( $arguments, [	'name'                  => '',
												'label'                 => '',
												'values'                => [],
												'empty-option'          => false,
												'selected'              => NULL,
												'show-label'            => true,
												'css-class'             => '',
												'required'              => false,
												'js-on-change'          => '',
												'include-div'           => true
	]);

	$html = '';

	if ( true === $arguments[ 'show-label' ] ) {
		$html .= sprintf( '<label for="%1$s">%2$s</label>', esc_attr( $arguments[ 'name' ] ), esc_attr( $arguments[ 'label' ] ) );
	}

	if ( true === $arguments[ 'include-div' ] ) {
		$html .= sprintf( '<div id="%1$s-row" class="yk-tt-form-row">', $arguments[ 'name' ] );
	}

	$html .= sprintf( '<select id="%1$s" name="%1$s" tabindex="%2$d" class="%3$s" %4$s %5$s>',
		esc_attr( $arguments[ 'name' ] ),
		yk_tt_form_tab_index_next(),
		esc_attr( $arguments[ 'css-class' ] ),
		( true === $arguments[ 'required' ] ) ? ' required' : '',
		( false === empty( $arguments[ 'js-on-change' ] ) ) ? sprintf( ' onchange="%s"', $arguments[ 'js-on-change' ] ) : ''
	);

	if ( true === $arguments[ 'empty-option' ] ) {
		$html .= '<option value=""></option>';
	}

	foreach ( $arguments[ 'values' ] as $id => $value ) {
		$html .= sprintf('<option value="%1$s" %2$s>%3$s</option>',
			esc_attr( $id ),
			selected( $arguments[ 'selected' ], $id, false ),
			esc_html( $value )
		);
	}

	$html .= '</select>';

	if ( true === $arguments[ 'include-div' ] ) {
		$html .= '</div>';
	}

	return $html;
}

/**
 * Format form title
 * @param $title
 * @param $hide
 *
 * @return string
 */
function yk_tt_form_title( $title, $hide ) {
	return ( false === $hide ) ?
		sprintf( '<h3 class="title">%s</h3>', esc_html( $title ) ) :
		'';
}
/**
 * Keep track of the current tab index and increment
 * @return int
 */
function yk_tt_form_tab_index_next() {

	global $yk_tt_tab_index;

	$current_index = $yk_tt_tab_index;
	$yk_tt_tab_index++;

	return $current_index;
}
