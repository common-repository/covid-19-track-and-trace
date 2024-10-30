<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Plugin Name:         Attendance Register
 * Description:		Add an Attendance Tracker to your website. There are endless uses whether it's to register users for a class, a Covid-19 "Track and Trace" form for your visitors, etc - it's quick to setup and start recording attendance. View and export entries for any given day.
 * Version:             1.1.15
 * Requires at least:   5.7
 * Tested up to: 	    6.0
 * Requires PHP:        7.2
 * Author:              Ali Colville
 * Author URI:          https://www.YeKen.uk
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         track-and-trace
 * Domain Path:         /includes/languages
 */

define( 'YK_TT_ABSPATH', plugin_dir_path( __FILE__ ) );
define( 'YK_TT_CURRENT_VERSION', '1.1.15' );
define( 'YK_TT_PREMIUM_VERSION', '1.1.4' );                    // Version number for latest Premium (used to warn them there is an update)
define( 'YK_TT_TITLE', 'Attendance Register' );
define( 'YK_TT_TITLE_SHORT', 'Attendance Register' );
define( 'YK_TT_SLUG', 'track-and-trace' );
define( 'YK_TT_PREMIUM_PRICE', '29.99');
define( 'YK_TT_UPGRADE_LINK', 'https://shop.yeken.uk/product/attendance-register/' );

// -----------------------------------------------------------------------------------------
// Include all relevant PHP files
// -----------------------------------------------------------------------------------------

require_once( YK_TT_ABSPATH . 'includes/db.php' );
require_once( YK_TT_ABSPATH . 'includes/activate.php' );
require_once( YK_TT_ABSPATH . 'includes/core.php' );
require_once( YK_TT_ABSPATH . 'includes/core-form.php' );
require_once( YK_TT_ABSPATH . 'includes/shortcode-form.php' );
require_once( YK_TT_ABSPATH . 'includes/hooks.php' );
require_once( YK_TT_ABSPATH . 'includes/admin-pages/page.license.php' );
require_once( YK_TT_ABSPATH . 'includes/admin-pages/page.help.php' );
require_once( YK_TT_ABSPATH . 'includes/admin-pages/page.settings.php' );
require_once( YK_TT_ABSPATH . 'includes/admin-pages/data.dashboard.php' );

// -----------------------------------------------------------------------------------------
// Load relevant language files (https://wpallinfo.com/complete-list-of-wordpress-locale-codes/)
// -----------------------------------------------------------------------------------------
function yk_tt_load_textdomain() {
	load_plugin_textdomain( YK_TT_SLUG, false, dirname( plugin_basename( __FILE__ )  ) . '/includes/languages/' );
}
add_action('plugins_loaded', 'yk_tt_load_textdomain');
