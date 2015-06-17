# WP Currencies

[![GitHub version](https://badge.fury.io/gh/nekojira%2Fwp-currencies.svg)](http://badge.fury.io/gh/nekojira%2Fwp-currencies)
[![Join the chat at https://gitter.im/nekojira/wp-currencies](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/nekojira/wp-currencies?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

WP Currencies is a [WordPress](http://www.wordpress.org) plugin that imports and periodically updates a complete set of World currencies and their exchange rates from [openexchangerates.org](https://openexchangerates.org) to WordPress database for later retrieval and use.

This plugin is intended for WordPress backend and frontend developers who want to build with currency data from within WordPress.

[![Download from WordPress.org](https://github.com/nekojira/wp-currencies/blob/master/assets/wordpress-download-btn.png)](https://wordpress.org/plugins/wp-currencies/)


## Features

* Pulls currency data from [openexchangerates.org](https://openexchangerates.org)
* Imports currency exchange rates into a local database table
* Periodically and automatically updates currency data by set cron intervals
* If you use [WooCommerce](https://github.com/woothemes/woocommerce) and [WPML WooCommerce Multilingual](https://wordpress.org/plugins/woocommerce-multilingual/), you can use [WCML hooks](https://wpml.org/documentation/related-projects/woocommerce-multilingual/multi-currency-support-woocommerce/) together with WP Currencies functions to change currency rates on the fly.
* Provides a set of PHP functions to retrieve currency data and exchange rates or perform currency conversions via PHP or Ajax
* Registers [JSON REST API](https://wordpress.org/plugins/json-rest-api/) endpoints with currency and exchange rates data
* Adds a *Currency Field* to [Advanced Custom Fields](https://www.advancedcustomfields.com/) plugin, if installed
* Provides 2 shortcodes to print currency data or conversion rates from a WordPress post or page

## Requirements

You will need a valid [openexchangerates.org](https://openexchangerates.org) API Key.


## Installation
How to install and activate:

1. Git clone or download zip and copy `wp-currencies` into your WordPress `plugins` directory
2. From WordPress administration area, navigate to the *Plugins* dashboard page
3. Locate *WP Currencies* among installed plugins
4. Click on *Activate*

**You will need an [openexchangerates.org](https://openexchangerates.org) API key to make it work properly.**

Once the plugin is activated and you have obtained a valid API key, take the following steps:

1. From your WordPress admin navigate to the *Settings* dashboard menu
2. Click on *Currencies* menu item
3. Enter your Open Exchange Rates API key
4. If you want, you can specify a different update frequency (optional)
5. Click *Save Changes* to save settings

If the API key is valid and Open Exchange Rates site is working properly, currency data will be uploaded to database at the first usage of any of its functions (see below). You may need to refresh page once. Updates are triggered by function calls and timestamps check, according to the set update frequency interval.


## Usage
This plugin will create a database table in WordPress database called `currencies` (or more likely `wp_currencies`, according to your own WordPress default database prefix). Each row in the table has four columns:

1. Currency represented by an ISO 4217 code (string, eg. USD, GBP, EUR...)
2. Currency conversion rate to US Dollar (float)
3. Currency data with currency name, symbol, symbol position, decimals count, thousands and decimals separators (json data)
4. Timestamp marking the time when the last update occurred (date)

Even if the base currency is in US Dollars (which is what [openexchangerates.org](https://openexchangerates.org) API ships with by default anyway), you can perform any action for any currency with this data.

The plugin comes with the following PHP functions to access the currency data:

##### `get_exchange_rates( $currency_code = 'USD' )`
Will return a PHP array with all the currency codes and their corresponding rate.
By default USD (US Dollar) will be used to return rates, but you can pass another ISO currency code (eg. `'EUR'`) as the function argument.

##### `get_exchange_rates_json( $currency_code = 'USD' )`
This is a helper function that outputs a json object with each currency code and their corresponding rate to US dollar. This function's output will be the response to Ajax requests to `'get_exchange_rates'`.

##### `convert_currency( $amount = 1, $from = 'USD', $in = 'EUR' )`
Converts a given amount from one currency into another and returns a float as result. You need to specify currency codes as arguments; will default from 1 USD to EUR.
Example: `convert_currency( '150', 'GBP', 'AUD' )` will return a number with the equivalent in AUD of 150 GBP. You can use floats too.

##### `get_exchange_rate( $currency1 = 'USD', $currency2 = 'EUR' )`
An alias of `convert_currency()` with fixed amount of 1 to return the exchange rate between these two currencies.
Example: `get_exchange_rate( 'GBP', 'CAD' )` returns a float with the current exchange rate of GBP to CAD.

##### `get_currencies()`
Returns a PHP array with currencies codes and currency data such as name in English and symbol. This function has no arguments.

##### `get_currencies_json()`
Outputs a json object with currencies codes and currency data such as currency name in English and corresponding symbol. This function's output will be the response to Ajax requests to `'get_currencies'`.

##### `get_currency( $currency_code = 'USD' )`
Given a specified currency code, will return a PHP array with the currency name in English language, currency symbol as html entity or currency code, currency symbol position (before or after), number of decimals, decimals and thousands separators.
Example: `get_currency( 'CHF' )` will return array data for Swiss Franc.

##### `format_currency( $amount, $currency_code, $symbol )`
Formats a given amount (integer or float) using specified currency data and returns the number with the currency symbol. For example: `format_currency( 1025.980, 'USD' )` will return `1,025.98 $`. Pass `$symbol` to false if you don't want the currency symbol to appear in the output.

##### `currency_exists( $currency_code )` ####
You can use this helper function to check if a currency (as a 3-letter ISO code) exists in database and can be used in operations with the other functions.


## JSON REST API
If you use [WP API](https://wordpress.org/plugins/json-rest-api/), WP Currencies will automatically register the following endpoints:

* **`/currencies/`** will respond with currencies data (currency code, currency name, symbol, separators, etc).

* **`/currencies/rates/`** will respond with currency exchange rates for US Dollar in each currency.

* **`/currencies/rates?currency={currency_code}`** will respond with currency exchange rates with given base currency code.
For example: `/currencies/rates?currency=EUR` will output exchange rates for the Euro.


## Shortcodes
The plugin also features two shortcodes:

##### `[currency_convert amount="{number}" from="{currency1}" in="{currency2}" round="2"]`
Will print the converted amount of one currency into another, according to values specified. You can use a float too as amount to convert. `round` is optional, defaults to 2 (rounds to two decimals). For example: `[currency_convert amount='260' from="MYR" in="THB" round="0"]` will print a integer number resulting from the conversion of 260 Malaysian Ringgit to Thai Baht.

##### `[currency_symbol currency="{currency}"]`
Will print the currency symbol according to currency code specified. For example: `[currency_symbol currency="JPY"]` (Japanese Yen) will print `&#165;` which will render as `¥`.

## Contributing
If you find this plugin useful you're welcome to contribute by issuing bug reports, cloning this repo and submitting pull requests. Thank you.
