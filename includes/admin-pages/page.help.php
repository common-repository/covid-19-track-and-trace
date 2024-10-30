<?php

defined('ABSPATH') or die('Jog on!');

function yk_tt_help_page() {

	?>

    <div class="wrap ws-ls-admin-page">

	<div id="icon-options-general" class="icon32"></div>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-3">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">
						<h3 class="hndle"><span><?php echo __( 'How to use', YK_TT_SLUG ); ?> </span></h3>
						<div style="padding: 0px 15px 0px 15px">

							<h3>Configure the form</h3>
							<p><?php echo __( 'Start by reviewing the settings of this plugin and enable the data fields you wish each use to complete.', YK_TT_SLUG ); ?></p>

							<p><a href="<?php echo yk_tt_link_admin_page(); ?>"><?php echo __( 'View Settings', YK_TT_SLUG ); ?></a></p>

							<h3>Please the shortcode</h3>
							<p><?php echo __( 'Create a page or post where you wish the form to appear, then place the following shortcode:', YK_TT_SLUG ); ?></p>

							<p style="font-size: 20px">[attendance-register]</p>

							<p><?php echo __( 'We would recommend creating a page called "attendance-register" and publish. This will give you an easy URL like https://yourwebsite.com/attendance-register.', YK_TT_SLUG ); ?></p>

							<p><?php echo __( 'Now save (or publish) and view the public facing side of the site.', YK_TT_SLUG ); ?></p>
						</div>
					</div>

                    <div class="postbox">
                        <h3 class="hndle"><span><?php echo __( 'Custom modifications / web development', YK_TT_SLUG ); ?> </span></h3>
                        <div style="padding: 0px 15px 0px 15px">
	                        <?php yk_tt_custom_notification_html(); ?>
                        </div>
                    </div>


                    <div class="postbox">
                        <h3 class="hndle"><span><?php echo __( 'Contact', YK_TT_SLUG ); ?> </span></h3>
                        <div style="padding: 0px 15px 0px 15px">
                            <p>If you have any questions or bugs to report, then please contact us at <a href="mailto:email@yeken.uk">email@yeken.uk</a>.</p>
                        </div>
                    </div>


				</div>
				<!-- .meta-box-sortables .ui-sortable -->

			</div>
			<!-- post-body-content -->

		</div>
		<!-- #post-body .metabox-holder .columns-2 -->

		<br class="clear">
	</div>
	<!-- #poststuff -->

</div> <!-- .wrap -->
<?php

}
