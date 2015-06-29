=== WP Currencies ===
Contributors: nekojira
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=P64V9NTEYFKDL
Tags: currency, currencies, exchange-rates, finance, ecommerce, woocommerce, wcml, acf, advanced-custom-fields 
Requires at least: 4.0.0
Tested up to: 4.2.2
Stable tag: 1.4.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Currency data and updated currency exchange rates for WordPress.


== Description ==

WP Currencies uses [openexchangerates.org](http://www.openexchangerates.org/) to pull currency data and currency exchange rates in WordPress.

The currencies and their exchange rates will be periodically updated at intervals set from the WordPress dashboard by an administrator.

The plugin is intended for developers that want to access currencies data and currency exchange rates from within WordPress and perform basic currency rates conversions. WP Currencies comes with a functions library to retrieve currency data and exchange rates as PHP arrays or json objects in ajax calls. 

Furthermore, it extends [JSON REST WP API]( https://wordpress.org/plugins/json-rest-api/) with new routes, and, if you use [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/) (ACF), WP Currencies will also add a new "Currency" field for use in ACF.

If you use [WPML](http://wpml.org/) and have both [WooCommerce](https://wordpress.org/plugins/woocommerce/) and [WooCommerce MultiLingual](https://wordpress.org/plugins/woocommerce-multilingual/) installed, you can use this plugin to filter WCML currency rate using [one of their hooks](https://wpml.org/documentation/related-projects/woocommerce-multilingual/multi-currency-support-woocommerce/) and update rates on the fly.

**Important** - You will need an API key from http://www.openexchangerates.org/ to pull currency data and make this plugin work properly (either choose the forever free plan or one of their premium subscriptions).

= Documentation =

A more complete documentation for this plugin is found in the [Github wiki](https://github.com/nekojira/wp-currencies/wiki).

= Contributing =

To contribute with bug reports or submit pull requests, please refer to [WP Currencies repository on Github](https://github.com/nekojira/wp-currencies), thank you.


== Installation ==

Install as you would do with any other WordPress plugin to get started.

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard.
2. Search for 'WP Currencies'.
3. Click 'Install Now'.
4. Activate the plugin.

= Uploading in WordPress =

1. Navigate to the 'Add New' in the plugins dashboard.
2. Navigate to the 'Upload' area.
3. Select `wp-currencies.zip` from your computer.
4. Click 'Install Now'.
5. Activate the plugin.

= Using FTP =

1. Download `wp-currencies.zip`.
2. Extract `wp-currencies` directory to your computer.
3. Upload  `wp-currencies` directory to the `/wp-content/plugins/` directory.
4. Activate the plugin from the dashboard.

= Setting up the plugin =

After installation, navigate to `Currencies` settings page in the `Settings` admin menu in your WordPress admin dashboard.
From here, enter your Open Exchange Rates API key - get yours at [http://www.openexchangerates.org/](http://www.openexchangerates.org/) and hit the `Save Changes` button.

You may as well specify a different update frequency of your currency exchange rates. 


== Functions ==

For the full documentation, please refer to the [WP Currencies wiki on Github](https://github.com/nekojira/wp-currencies/wiki).

== Frequently Asked Questions ==

= I've installed the plugin but I don't see anything! =

Please read again the plugin description and read the [documentation](https://github.com/nekojira/wp-currencies/wiki). 
WP Currencies by this time doesn't offer any WYSIWYG functionality, but it's intended for developers who want to build solutions using currency data and exchange rates - which this plugin provides with an API for using those in WordPress.

= Is this plugin created or endorsed by Open Exchange Rates? =

No it is not. It just makes use of their public API within their Terms and Conditions policy: [https://openexchangerates.org/terms](https://openexchangerates.org/terms).

= Can you guarantee that the currency exchange rates provided will be accurate? =

**No.** As Open Exchange Rates itself says: "Exchange rates are provided for informational purposes only, and do not constitute financial advice of any kind. Although every attempt is made to ensure quality, NO guarantees are given whatsoever of accuracy, validity, availability, or fitness for any purpose - please use at your own risk."
It is not recommended to use this plugin in critical business scenarios. The plugin author(s) nor the currency exchange rates provider(s) will not be responsible for financial loss or damage caused by data inaccuracies. Please refer to the GPL license and Open Exchange Rates terms and conditions for further information.

= What if I exceed my Open Exchange Rates API request quota? =

This plugin will be unable to update the database and older, may throw a warning when it tries to do so (as if it would with an invalid API key) and less accurate currency rates will be used (those recorded in the database at the time of the last update).
You can monitor your quota usage according to your subscription plan by logging into your Open Exchange Rates account at [https://openexchangerates.org/](https://openexchangerates.org/).
From the plugin settings, you can specify a less frequent interval of database updates to make less API requests. Please note that if you make use of the API elsewhere and perform more requests, they will be counted by Open Exchange Rates and summed up with the ones triggered by this plugin.

= WP Currencies runs but the currencies aren't refreshed at the intervals I've set - why? =

This could be related to `cron` not working properly in your host. Please refer to the `Troubleshooting` section of [WP Currencies documentation](https://github.com/nekojira/wp-currencies/wiki).


== Screenshots ==

1. The plugin settings page
2. An example of a json object with currency rates with US Dollar as base currency


== Changelog ==

= 1.4.6 =
* Further improvements in handling wp cron and scheduled events hooks
* Introduced `wp_currencies_update()` function

= 1.4.4 =
* Removed deprecated code in ACF 5.x support that was triggering an error

= 1.4.3 =
* 'DOING_CRON' wrapper check added to wp cron callback action to prevent firing too many updates
* Added new action hooks (see documentation)

= 1.4.1 = 
* Fixes a critical bug from 1.4.0 where too many currency update requests were fired - please update

= 1.4.0 = 
* Better OOP rewrite of the whole plugin
* PHP 5.4 is the minimum requirement now
* Improved security and performance
* Introduced new hooks (see documentation)

= 1.3.0 =
* Removed support for WCML (use the new WCML hooks instead)

= 1.2.5 =
* Supports WordPress 4.2

= 1.2.4 =
* Fixes "The plugin generated ... characters of unexpected output" upon activation

= 1.2.2 =
* Fixes missing database table creation on plugin activation (you may want to deactivate and reactivate plugin after upgrade if were experiencing issues)

= 1.2.1 =
* Fixes broken link to plugin settings page

= 1.2.0 =
* Added support to WooCommerce MultiLanguage
* Updated Settings Page
* The plugin now uses `wp_cron` for periodical updates
* Introduced `currency_exists()` function
* Bugfixes

= 1.1.3 =
* Added field support for Advanced Custom Fields 5.x
* More currency data

= 1.1.1 =
* Bugfix (accidentally deactivated admin class in 1.1.0)

= 1.1.0 =
* Introduced `format_currency()` function
* Added endpoints for JSON REST API (WP API)
* Added a "Currency field" for Advanced Custom Fields 4.x

= 1.0.0 =
* First release


== Upgrade Notice ==

= 1.4.6 =
Currency updates scheduling system changed, if you encounter any issue try overwriting your settings. 

= 1.4.3 =
Further improved the wp cron mechanism against firing too many requests. Please update asap.

= 1.4.1 =
Fixes a critical bug in 1.4.0 where too many requests were fired. Please update. 

= 1.4.0 =
PHP 5.4 is now the minimum PHP version required to run the plugin.

= 1.3.0 =
WCML support was dropped since with recent changes in WCML, WP Currencies was no longer compatible with it. However, WCML now offers hooks to alter its currency rates and you can use these with WP Currencies to dynamically change them.

= 1.2.2 =
Fixed a bug where the currencies table was not created in database; you may want to deactivate and reactivate the plugin after this upgrade if you were experiencing issues.

= 1.2.0 =
Settings have changed, when you update you should re-enter your API key. In case of problems, uninstall and reinstall.

= 1.0.0 =
First release
