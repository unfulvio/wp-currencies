=== WP Currencies ===
Contributors: nekojira
Donate link: https://www.paypal.com/uk/cgi-bin/webscr?cmd=_flow&SESSION=SUJDJhsqyxThi-AbCT2HmIpMmBar3yAXDTYxlcNqruUIneC0_cxfT29SdIq&dispatch=5885d80a13c0db1f8e263663d3faee8d5402c249c5a2cfd4a145d37ec05e9a5e
Tags: currency, currencies
Requires at least: 3.6.0
Tested up to: 4.1.0
Stable tag: 1.2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Bring currency data and updated currency exchange rates into WordPress.


== Description ==

WP Currencies is a WordPress plugin that helps you fetch currency data from http://www.openexchangerates.org/.

The currencies and their exchange rates will be periodically updated at intervals that can be set in WordPress dashboard settings by a WordPress administrator.

The plugin is intended for developers that want to access currencies data and currency exchange rates from within WordPress. The plugin comes with functions to retrieve currency data and exchange rates as PHP arrays or json objects. Furthermore, it extends JSON REST WP API https://wordpress.org/plugins/json-rest-api/ with new routes, and, if you use Advanced Custom Fields https://wordpress.org/plugins/advanced-custom-fields/ WP Currencies will also add a new "Currency" field. If you use WPML http://wpml.org/ and have both WooCommerce https://wordpress.org/plugins/woocommerce/ and WooCommerce MultiLingual https://wordpress.org/plugins/woocommerce-multilingual/ installed, this plugin will also dynamically update WCML currency rates for you.

You will need an API key from http://www.openexchangerates.org/ to pull currency data and make this plugin work properly (either choose the forever free plan or one of their premium subscriptions).

To contribute with bug reports or submit pull requests, please refer to https://github.com/nekojira/wp-currencies


== Installation ==

Install as you would do with any other WordPress plugin to get started.

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'WP Currencies'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `wp-currencies.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `wp-currencies.zip`
2. Extract `wp-currencies` directory to your computer
3. Upload  `wp-currencies` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard

= Setting up the plugin =

After installation, you will need to navigate to `Currencies` settings page in the `Settings` admin menu in your WordPress admin dashboard.
From here you will have to specify your Open Exchange Rates API key (get yours at http://www.openexchangerates.org/) and hit the `Save Changes` button.
You may as well specify an update frequency of your currency exchange rates. Default is once per week (7 days). If you set it to 0, the plugin will update your database hourly (please note that the free Open Exchange Rate plan has a maximum of 1,000 queries per month).

Please note that the currencies will be first pushed to your WordPress database at the first usage of any of the plugin functions after having entered a valid API key. You may need to refresh your application/page once.


== Functions ==

`get_exchange_rates( $currency_code = 'USD' )`
Will return a PHP array with all the currency codes and their corresponding rate.
By default USD (US Dollar) will be used to return rates, but you can pass another ISO currency code (eg. `'EUR'`) as the function argument.

`get_exchange_rates_json( $currency_code = 'USD' )`
This is a helper function that outputs a json object with each currency code and their corresponding rate to US dollar. This function's output will be the response to Ajax requests to `'get_exchange_rates'`.

`convert_currency( $amount = 1, $from = 'USD', $in = 'EUR' )`
Converts a given amount from one currency into another and returns a float as result. You need to specify currency codes as arguments; will default from 1 USD to EUR.
Example: `convert_currency( '150', 'GBP', 'AUD' )` will return a number with the equivalent in AUD of 150 GBP. You can use floats too.

`get_exchange_rate( $currency1 = 'USD', $currency2 = 'EUR' )`
An alias of `convert_currency()` with fixed amount of 1 to return the exchange rate between these two currencies.
Example: `get_exchange_rate( 'GBP', 'CAD' )` returns a float with the current exchange rate of GBP to CAD.

`get_currencies()`
Returns a PHP array with currencies codes and currency data such as name in English and symbol. This function has no arguments.

`get_currencies_json()`
Outputs a json object with currencies codes and currency data such as currency name in English and corresponding symbol. This function's output will be the response to Ajax requests to `'get_currencies'`.

`get_currency( $currency_code = 'USD' )`
Given a specified currency code, will return a PHP array with the currency name in English language, currency symbol as html entity or currency code, currency symbol position (before or after), number of decimals, decimals and thousands separators.
Example: `get_currency( 'CHF' )` will return array data for Swiss Franc.

`format_currency( $amount, $currency_code, $symbol )`
Formats a given amount (integer or float) using specified currency data and returns the number with the currency symbol. For example: `format_currency( 1025.980, 'USD' )` will return `1,025.98 $`. Pass `$symbol` to false if you don't want the currency symbol to appear in the output.

`currency_exists( $currency_code )`
You can use this helper function to check if a currency (as a 3-letter ISO code) exists in database and can be used in operations with the other functions.


== API ==

If you use WP API (https://wordpress.org/plugins/json-rest-api/), WP Currencies will automatically register the following routes:

`/currencies/`
will respond with currencies data (currency code, currency name, symbol, separators, etc).

`/currencies/rates/`
will respond with currency exchange rates for US Dollar in each currency.

`/currencies/rates?currency={currency_code}`
will respond with currency exchange rates with given base currency code.
(For example: `/currencies/rates?currency=EUR` will output exchange rates for the Euro.)


== Shortcodes ==

The plugin also provides two WordPress shortcodes:

`[currency_convert amount="{number}" from="{currency_code1}" in="{currency_code2}" round="2"]`
Will print the converted amount of one currency into another, according to values specified. You can use a float too as amount to convert. `round` is optional, defaults to 2 (rounds to two decimals). For example: `[currency_convert amount='260' from="MYR" to="THB" round="0"]` will print a rounded integer number resulting from the conversion of 260 Malaysian Ringgit to Thai Baht.

`[currency_symbol currency="{currency_code}"]`
Will print the currency symbol according to currency code specified. For example: `[currency_symbol currency="JPY"]` (Japanese Yen) will print `&#165;` which will render as `¥`.


== Frequently Asked Questions ==

= I've installed the plugin but I don't see anything! =

Please read again the plugin description and read the functions documentation. WP Currencies by this time doesn't offer any WYSIWYG functionality, but it's intended for developers who want to build features, themes or other plugins using currency data - which this plugin provides.

= Is this plugin created or endorsed by Open Exchange Rates? =

No it is not. It just makes use of their public API within their Terms and Conditions policy: https://openexchangerates.org/terms.

= Can you guarantee that the currency exchange rates provided will be accurate? =

No. As Open Exchange Rates itself says: "Exchange rates are provided for informational purposes only, and do not constitute financial advice of any kind. Although every attempt is made to ensure quality, NO guarantees are given whatsoever of accuracy, validity, availability, or fitness for any purpose - please use at your own risk."
It is not recommended to use this plugin in critical business scenarios. The plugin author(s) nor the currency exchange rates provider(s) will not be responsible for financial loss or damage caused by data inaccuracies. Please refer to GPL license and Open Exchange Rates terms and conditions for further information.

= What if I exceed my Open Exchange Rates API request quota? =

This plugin will be unable to update the database and older, perhaps less accurate currency rates will be used.
You can monitor your quota usage according to your subscription plan by logging into your Open Exchange Rates account at https://openexchangerates.org/.
From the plugin settings, you can specify a greater interval of database updates to make less API requests (default is 1 request per week or 7 days; setting the interval to 0 will attempt hourly database updates which will generate at most between 672 and 744 requests monthly, but more likely less than that).
Please note that if you make use of the API elsewhere and perform more requests, they will be counted by Open Exchange Rates and summed up with the ones triggered by this plugin.


== Screenshots ==

1. The plugin settings page
2. An example of a json object with currency rates with US Dollar as base currency


== Changelog ==

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

= 1.2.2 =
Fixed a bug where the currencies table was not created in database; you may want to deactivate and reactivate the plugin after this upgrade if you were experiencing issues.

= 1.2.0 =
Settings have changed, when you update you should re-enter your API key. In case of problems, uninstall and reinstall.

= 1.0.0 =
First release