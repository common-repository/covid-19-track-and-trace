=== Attendance Register ===
Contributors: aliakro
Donate link: https://www.paypal.me/yeken
Tags: attendance, register, covid-19, coronavirus, track, trace, sign in, record, log, visitors, visits, users, form, export
Requires at least: 5.7
Tested up to: 6.0
Stable tag: 1.1.14
Requires PHP: 7.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add an Attendance Register to your website. There are endless uses whether it's to register users for a class, a Covid-19 "Track and Trace" form for your visitors, etc - it's quick to setup and start recording attendance. View and export entries for any given day.

== Upgrade Notice ==

== Description ==

Quickly and easily create an Attendance Register form to your website. Whether it's to register users for a class, a Covid-19 "Track and Trace" form for your visitors, etc. Specify the fields that you wish to collect and simply direct your customers to it. All entries can be view and exported via the admin interface.

= Core Features =

* Easy to place form with the following configurable fields:
 * Name
 * Phone
 * Email
 * Arrival Date
 * Arrival Time
 * Venue

= Premium Features =

Purchase the Premium plugin from [https://shop.yeken.uk/product/attendance-register/](https://shop.yeken.uk/product/attendance-register/ "https://shop.yeken.uk/product/attendance-register/") and get these additional features:

* **Force to today's date**. Save the user selecting today's date by forcing the date to be today.
* **Restrict Venue to a selection (dropdown list)**. Instead of allowing users to manually enter their venue, provide and restrict them to dropdown list of venues.
* **Search and autocomplete**. If enabled, when typing a user's full name, the plugin will search the WordPress user table. When a user is selected, the user's email address and name shall be auto completed..
* **Telephone number from WooCommerce**. Auto complete telephone number from WooCommerce billing_phone field.
* **Estimated Departure Time**. An additional field to specify the estimated departure time.
* **Number of People in Party**. An additional field to specify the number of users in the party.
* **Google reCaptcha**. Reduce the risk of Spam form submissions with Google reCaptcha support.
* **View entries for any day**. Additional options on the entries page for fetching entries for any given date.
* **GDPR: Auto Delete older entries** Every day, entries older than x days can be automatically removed form your database. (requires premium version 1.1.3+)
* **Export to CSV**. Export your Track and Trace entries to CSV.

== Installation ==

1. Login into Wordpress Admin Panel
2. Navigate to Plugins > Add New
3. Search for "Attendance Register"
4. Click Install now and activate plugin
5. View the plugin's settings page and specify which fields to ask users to complete
6. Please the shortcode [attendance-register] on a post or page.

== Frequently Asked Questions ==

= How do I get the form to show to the public? =

We would recommend creating a page called "track-and-trace" or "attendance-register", add the shortcode [attendance-register], then publish. This will give you an easy URL like https://yourwebsite.com/attendance-register.

= Language support =

The plugin is written in English (UK) but is ready for translation. If you wish to add translations, please email me at email@yeken.uk

== Screenshots ==

1. [attendance-register] example on Twenty Twenty. (Premium Add-on view)
2. Viewing customer entries (Premium Add-on view)
3. Settings Page (Premium Add-on view)

== Upgrade Notice ==

== Changelog ==

= 1.1.15 =

* Tested up to version 6.0.

= 1.1.14 =

* Bug fix: If user name loaded from user profile, then add hidden field.
* Bug fix: Fixed issue with language files loading.

= 1.1.13 =

* Improvement: If full name loaded from display name, then disable form.

= 1.1.12 =

* New feature: Added "Set Full Name to display name" setting.
* Added support for Users WP / Venue functionality to support premium plugin 1.1.14.
* Updated language files.

= 1.1.11 =

* Reduced min PHP version

= 1.1.9 =

* Updated "Tested upto" statement within readme.txt

= 1.1.8 =

* Bug fix: Corrected year when being rendered on entries page.

= 1.1.7 =

* Improvement: Added logic to detect of Premium version is out of date.

= 1.1.6 =

* New Feature: GDPR: Auto delete entries older than x days. (Requires Premium version 1.1.3+)

= 1.1.5 =

* Added support for additional information to be rendered at bottom of form (by premium plugin)

= 1.1.4 =

* Updated Pro feature list: Telephone number from WooCommerce. Auto complete telephone number from WooCommerce billing_phone field.

= 1.1.3 =

* Updated readme and tested upto version

= 1.1.2 =

* Signed off as working with 5.6

= 1.1.1 =

* Bug fix: Tweaked some name references.
* Updated Premium feature list.

= 1.1 =

* New Feature: Added "Venue" field.
* New Feature: Default "Venue" added as an argument to shortcode.
* Improvement: Renamed the plugin to "Attendance Register".
* Bug fix: Missing function when accessing querystring value.

= 1.0.3 =

* Bug fix: Renamed conflicting constant name.

= 1.0.2 =

* Bug fix: Added a built in tool to detect if the database table is missing and rebuilds.

= 1.0 =

* Initial Build
