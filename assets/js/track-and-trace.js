jQuery( document ).ready( function ( $ ) {

  /**
   * Setup JS validation for form
   */
  $( ".yk-tt-form" ).validate({   errorClass:           "yk-tt-invalid",
                                  validClass:           "yk-tt-valid",
                                  errorContainer:       ".yk-tt-error-summary",
                                  errorLabelContainer:  ".yk-tt-error-summary ul",
                                  wrapper:              "li",
                                  rules:                yk_tt_config[ 'validation-rules' ],
                                  messages:             yk_tt_config[ 'validation-messages' ],
                                  submitHandler: function(form) {   if ( 'y' == yk_tt_config[ 'recaptcha-enabled' ] ) {

                                                                      grecaptcha.ready(function() {
                                                                        grecaptcha.execute(yk_tt_config[ 'google-site-key' ], {action: 'submit'}).then(function(token) {

                                                                          $( '#google-captcha' ).val( token );

                                                                          form.submit();
                                                                        });
                                                                      });

                                                                    } else {
                                                                      form.submit();
                                                                    }
                                  }
  });

  /**
   * Initiate Date pickers
   */
  $( '.yk-mt-datepicker' ).each( function() {
      $( this ).datepicker( {   changeMonth         : false,
                                changeYear          : false,
                                dateFormat          : yk_tt_config[ 'date-format' ],
                                yearRange           : '-1:+0',
                                showButtonPanel     : true,
                                closeText           : yk_tt_config[ 'date-picker-locale' ][ 'closeText' ],
                                currentText         : yk_tt_config[ 'date-picker-locale' ][ 'currentText' ],
                                monthNames          : yk_tt_config[ 'date-picker-locale' ][ 'monthNames' ],
                                monthNamesShort     : yk_tt_config[ 'date-picker-locale' ][ 'monthNamesShort' ],
                                dayNames            : yk_tt_config[ 'date-picker-locale' ][ 'dayNames' ],
                                dayNamesShort       : yk_tt_config[ 'date-picker-locale' ][ 'dayNamesShort' ],
                                dayNamesMin         : yk_tt_config[ 'date-picker-locale' ][ 'dayNamesMin' ],
                                firstDay            : yk_tt_config[ 'date-picker-locale' ][ 'firstDay' ]
      }).datepicker( 'setDate', new Date() );
  });

  /**
   * Arrival Time picker
   */
  if ( '1' == yk_tt_config[ 'enabled-arrival-time' ] ) {
    $( '.yk-tt-arrival-time' ).timepicker({   listWidth         : 1,
      minTime           : new yk_mt_get_current_date_to_nearest_5(),
      scrollDefault     : 'now',
      step              : 15
    }).timepicker( 'setTime', new Date() );
  }

  /**
   * Departure Time picker
   */
  if ( '1' == yk_tt_config[ 'enabled-departure-time' ] ) {
    $( '.yk-tt-departure-time' ).timepicker({   listWidth         : 1,
      scrollDefault     : 'now',
      step              : 15,
      minTime           : new yk_mt_get_current_date_to_nearest_5()
    });
  }

  /**
   * Get current time to nearest 5 minutes
   * @returns {Date}
   */
  function yk_mt_get_current_date_to_nearest_5() {
    let coeff     = 1000 * 60 * 5;
    let date      = new Date();
    return new Date(Math.round(date.getTime() / coeff) * coeff)
  }

});
