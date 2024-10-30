<?php

defined('ABSPATH') or die('Jog on!');

function yk_tt_settings_page_generic() {

    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' , YK_TT_SLUG ) );
    }

	// Rebuild mysql tables?
	if ( false === empty( $_GET[ 'recreate-tables' ] ) ) {
		yk_tt_missing_database_table_fix();

		printf( '<div class="notice"><p>%1$s.</p></div>', __( 'All database tables have been rebuilt', YK_TT_SLUG ) );
	}

	$mysql_table_check = yk_tt_missing_database_table_any_issues();

	if ( false !== $mysql_table_check ) {

		printf(
			'<div class="error">
						<p>%1$s</p>
						<p><a href="%2$s?page=yk-tt-settings&amp;recreate-tables=y">%3$s</a></p>
					</div>',
			__( 'One or more database tables are missing for this plugin. They must be rebuilt if you wish to use the plugin.', YK_TT_SLUG ),
			get_permalink(),
			__( 'Rebuild them now', YK_TT_SLUG )

		);
	}

    ?>
    <div id="icon-options-general" class="icon32"></div>

    <div id="poststuff">

        <div id="post-body" class="metabox-holder columns-3 yk-mt-settings">

            <!-- main content -->
            <div id="post-body-content">

                <div class="meta-box-sortables ui-sortable">

                    <div class="postbox">
                        <h3 class="hndle">
                            <span>
                                <?php echo __( YK_TT_TITLE . ' Settings', YK_TT_SLUG); ?>
                            </span>
                        </h3>
                        <div class="inside">
                            <form method="post" action="options.php">
                                <?php

                                settings_fields( 'yk-tt-options-group' );
                                do_settings_sections( 'yk-tt-options-group' );

                                ?>

								<?php
									if ( false === yk_tt_is_ttp_activated() ) {
										yk_tt_display_pro_upgrade_notice();
									}
								?>
								<h3><?php echo __( 'Form Fields' , YK_TT_SLUG); ?></h3>
								<table class="form-table">
									<tr>
										<th scope="row"><?php echo __( 'Full Name' , YK_TT_SLUG); ?></th>
										<td>
											<?php
												$enabled = yk_tt_site_options_as_bool('enabled-name' );
											?>
											<select id="enabled-name" name="enabled-name">
												<option value="true" <?php selected( $enabled, true ); ?>><?php echo __('Yes', YK_TT_SLUG); ?></option>
												<option value="false" <?php selected( $enabled, false ); ?>><?php echo __('No', YK_TT_SLUG); ?></option>
											</select>
											<p><?php echo __('If enabled, the "Full Name" field will be visible on the form.', YK_TT_SLUG); ?></p>
										</td>
									</tr>
									<tr>
										<th scope="row"><?php echo __( 'Set Full Name to display name?' , YK_TT_SLUG); ?></th>
										<td>
											<?php
											$enabled = yk_tt_site_options_as_bool('enabled-set-name-display-name' );
											?>
											<select id="enabled-set-name-display-name" name="enabled-set-name-display-name">
												<option value="true" <?php selected( $enabled, true ); ?>><?php echo __('Yes', YK_TT_SLUG); ?></option>
												<option value="false" <?php selected( $enabled, false ); ?>><?php echo __('No', YK_TT_SLUG); ?></option>
											</select>
											<p><?php echo __('If enabled and the user is logged in, then set their Full name to their display name.', YK_TT_SLUG); ?></p>
										</td>
									</tr>
									<tr>
										<th scope="row"><?php echo __( 'Phone Number' , YK_TT_SLUG); ?></th>
										<td>
											<?php
											$enabled = yk_tt_site_options_as_bool('enabled-phone' );
											?>
											<select id="enabled-phone" name="enabled-phone">
												<option value="true" <?php selected( $enabled, true ); ?>><?php echo __('Yes', YK_TT_SLUG); ?></option>
												<option value="false" <?php selected( $enabled, false ); ?>><?php echo __('No', YK_TT_SLUG); ?></option>
											</select>
											<p><?php echo __('If enabled, the "Telephone number" field will be visible on the form.', YK_TT_SLUG); ?></p>
										</td>
									</tr>
									<tr>
										<th scope="row"><?php echo __( 'Email Address' , YK_TT_SLUG); ?></th>
										<td>
											<?php
											$enabled = yk_tt_site_options_as_bool('enabled-email' );
											?>
											<select id="enabled-phone" name="enabled-email">
												<option value="true" <?php selected( $enabled, true ); ?>><?php echo __('Yes', YK_TT_SLUG); ?></option>
												<option value="false" <?php selected( $enabled, false ); ?>><?php echo __('No', YK_TT_SLUG); ?></option>
											</select>
											<p><?php echo __('If enabled, the "Email Address" field will be visible on the form.', YK_TT_SLUG); ?></p>
										</td>
									</tr>
									<tr>
										<th scope="row"><?php echo __( 'Arrival Date' , YK_TT_SLUG); ?></th>
										<td>
											<?php
											$enabled = yk_tt_site_options_as_bool('enabled-date' );
											?>
											<select id="enabled-date" name="enabled-date">
												<option value="true" <?php selected( $enabled, true ); ?>><?php echo __('Yes', YK_TT_SLUG); ?></option>
												<option value="false" <?php selected( $enabled, false ); ?>><?php echo __('No', YK_TT_SLUG); ?></option>
											</select>
											<p><?php echo __('If enabled, the "Date" field will be visible on the form.', YK_TT_SLUG); ?></p>
										</td>
									</tr>
									<tr>
										<th scope="row"><?php echo __( 'Arrival Time' , YK_TT_SLUG); ?></th>
										<td>
											<?php
											$enabled = yk_tt_site_options_as_bool('enabled-arrival-time' );
											?>
											<select id="enabled-arrival-time" name="enabled-arrival-time">
												<option value="true" <?php selected( $enabled, true ); ?>><?php echo __('Yes', YK_TT_SLUG); ?></option>
												<option value="false" <?php selected( $enabled, false ); ?>><?php echo __('No', YK_TT_SLUG); ?></option>
											</select>
											<p><?php echo __('If enabled, the "Arrival Time" field will be visible on the form.', YK_TT_SLUG); ?></p>
										</td>
									</tr>
									<tr>
										<th scope="row"><?php echo __( 'Venue' , YK_TT_SLUG); ?></th>
										<td>
											<?php
											$enabled = yk_tt_site_options_as_bool('enabled-venue' );
											?>
											<select id="enabled-venue" name="enabled-venue">
												<option value="true" <?php selected( $enabled, true ); ?>><?php echo __('Yes', YK_TT_SLUG); ?></option>
												<option value="false" <?php selected( $enabled, false ); ?>><?php echo __('No', YK_TT_SLUG); ?></option>
											</select>
											<p><?php echo __('If enabled, allow the user to enter the venue they are attending. If you wish to force the venue to a given value, add the argument "default-venue" to the shortcode e.g. [attendance-register default-venue="Venue A"]', YK_TT_SLUG); ?></p>
										</td>
									</tr>
								</table>
								<h3><?php echo __( 'Date Format' , YK_TT_SLUG); ?></h3>
								<table class="form-table">
									<tr>
										<th scope="row"><?php echo __( 'UK or US?' , YK_TT_SLUG); ?></th>
										<td>
											<?php
											$date_format = yk_tt_site_options('date-format', 'dd/mm/yy' );
											?>
											<select id="date-format" name="date-format">
												<option value="mm/dd/yy" <?php selected( $date_format, 'mm/dd/yy' ); ?>><?php echo __('US', YK_TT_SLUG); ?> (mm/dd/yy)</option>
												<option value="dd/mm/yy" <?php selected( $date_format, 'dd/mm/yy' ); ?>><?php echo __('UK', YK_TT_SLUG); ?> (dd/mm/yy)</option>
											</select>
										</td>
									</tr>
								</table>

								<h3><?php echo __( 'Premium Add on' , YK_TT_SLUG); ?></h3>
								<?php

									if ( false === yk_tt_is_ttp_activated() ) {

										printf( '<p>%s:</p>', __( 'Purchase the Premium add-on and get these additional features', YK_TT_SLUG ) );
										echo yk_tt_features_display();

									} else {
										do_action( 'yk-tt-premium-settings' );
									}

								?>
                                <?php submit_button(); ?>
                            </form>
                        </div>
                    </div>
                 </div>
            </div>
        </div>
        <!-- #poststuff -->

    </div> <!-- .wrap -->

    <?php

}

/**
 * Register fields to save
 */
function yk_tt_register_settings(){
    register_setting( 'yk-tt-options-group', 'enabled-name' );
	register_setting( 'yk-tt-options-group', 'enabled-set-name-display-name' );
	register_setting( 'yk-tt-options-group', 'enabled-phone' );
	register_setting( 'yk-tt-options-group', 'enabled-email' );
	register_setting( 'yk-tt-options-group', 'enabled-date' );
	register_setting( 'yk-tt-options-group', 'enabled-arrival-time' );
	register_setting( 'yk-tt-options-group', 'date-format' );
	register_setting( 'yk-tt-options-group', 'enabled-venue' );
}
add_action( 'admin_init', 'yk_tt_register_settings' );
