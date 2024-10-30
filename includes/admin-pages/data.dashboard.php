<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function yk_tt_admin_page_dashboard() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.', YK_TT_SLUG ) );
	}

    ?>
    <div class="wrap ws-ls-user-data ws-ls-admin-page">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <?php yk_tt_display_pro_upgrade_notice(); ?>
                   <div class="postbox">
                        <h2 class="hndle"><span><?php echo __('View Entries' , YK_TT_SLUG ); ?></span></h2>
                        <div class="inside">
                            <?php yk_tt_table_user_entries(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
    <?php
}
