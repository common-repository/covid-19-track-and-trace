<?php

    defined('ABSPATH') or die('Jog on!');

    function yk_tt_advertise_pro() {

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', YK_TT_SLUG ) );
        }

        ?>

        <div class="wrap ws-ls-admin-page">
            <div id="icon-options-general" class="icon32"></div>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder">
                    <div id="post-body-content">

                        <div class="meta-box-sortables ui-sortable">
                            <div class="postbox">
                                <h3 class="hndle"><span>Upgrade / your License</span></h3>
                                <div class="inside">
										<h3>Premium Add-on</h3>
										<p>Purchase the Premium add-on and get these additional feature:</p>
										<?php echo yk_tt_features_display(); ?>
										<br />
										<br />
										<hr />
										<br />
										<?php yk_tt_upgrade_button();  ?>

								</div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php
        }
?>
