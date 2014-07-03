# WP Currencies

WP Currencies is a [WordPress](http://www.wordpress.org) plugin that imports and periodically updates a complete set of World currencies and their exchange rates from [openexchangerates.org](https://openexchangerates.org) to WordPress database for later retrieval and use.

This plugin is intended for WordPress developers who want to access to currency data from within WordPress.

## Features

* Pulls data from [openexchangerates.org](https://openexchangerates.org)
* Imports currency exchange rates into a WordPress database table
* Automatically updates currency data by set intervals
* Provides a set of PHP functions to retrieve currency data and exchange rates or perform currency conversions with PHP or Ajax
* Shortcodes

## Installation

1. Copy `wp-currencies` into your `wp-content/plugins` directory
2. From WordPress admin, navigate to the *Plugins* dashboard page
3. Locate the menu item that reads *WP Currencies*
4. Click on *Activate*

This will activate the plugin. 

**You will need an [openexchangerates.org](https://openexchangerates.org) API key to make it work properly.**

Once the plugin is activated and you have obtained a valid API key, take the following steps:

1. From your WordPress admin navigate to the *Settings* dashboard menu
2. Click on *Currencies* menu item
3. Enter your Open Exchange Rates API key
4. If you want, you can specify a different update frequency (optional)
5. Click *Save Changes* to save settings

If the API key is valid and Open Exchange Rates site is working properly, currency data will be uploaded to database at the first usage of any of its functions (see below). You may need to refresh page once. Updates are triggered by function calls and timestamps check, according to the set update frequency interval.

## Usage

This plugin will create a database table in WordPress database called `currencies` (or more likely `wp_currencies`, according to your own WordPress default database prefix). You can inspect the table with PHPMyAdmin. Each row in the table has four columns:

1. Currency ISO 4217 code (eg. USD, GBP, EUR...)
2. Currency conversion rate to US Dollar (float)
3. Currency data with currency name, symbol, symbol position, decimals count, thousands and decimals separators (json)
4. Timestamp marking the time when the last update occurred

Even if the base currency is in US Dollars (which is what [openexchangerates.org](https://openexchangerates.org) API ships with by default), you can perform any action for any currency with this data.
The plugin comes with the following PHP functions to access the data:

#### `get_exchange_rates( $currency_code = 'USD' )`
Will return a PHP array with all the currency codes and their corresponding rate.
By default USD (US Dollar) will be used to return rates, but you can pass another ISO currency code (eg. `'EUR'`) as the function argument.

#### `get_exchange_rates_json( $currency_code = 'USD' )`
This is a helper function that outputs a json object with each currency code and their corresponding rate to US dollar. You can pass a different currency as argument if you don't want the US Dollar as the base currency for the rates.

#### `convert_currency( $amount = 1, $from = 'USD', $in = 'EUR' )`
Converts a given amount from one currency into another and returns a float as result. You need to specify currency codes as arguments; will default from 1 USD to EUR.
Example: `convert_currency( '150', 'GBP', 'AUD' )` will return a number with the equivalent in AUD of 150 GBP. You can use floats too.

#### `get_exchange_rate( $currency1 = 'USD', $currency2 = 'EUR' )`
An alias of `convert_currency()` with fixed amount of 1 to return the exchange rate between these two currencies.
Example: `get_exchange_rate( 'GBP', 'CAD' )` returns a float with the current exchange rate of GBP to CAD.

#### `get_currencies()`
Returns a PHP array with currencies codes and currency data such as name in English and symbol. This function has no arguments.

#### `get_currencies_json()`
Outputs a json object with currencies codes and currency data such as currency name in English and corresponding symbol. This function has no arguments.

#### `get_currency( $currency_code = 'USD' )`
Given a specified currency code, will return a PHP array with the currency name in English language, currency symbol as html entity or currency code, currency symbol position (before or after), number of decimals, decimals and thousands separators.
Example: `get_currency( 'CHF' )` will return array data for Swiss Franc.

## Shortcodes
The plugin also provides two WordPress shortcodes:
#### `[currency_convert amount="1" from="USD" in="EUR" round="2"]`
Will print the converted amount of one currency into another, according to values specified. You can use a float too as amount to convert. `round` is optional, defaults to 2 (rounds to two decimals). For example: `[currency_convert amount='260' from="MYR" to="THB" round="0"]` will print a integer number resulting from the conversion of 260 Malaysian Ringgit to Thai Baht.

#### `[currency_symbol currency="USD"]`
Will print the currency symbol according to currency code specified. For example: `[currency_symbol currency="JPY"]` (Japanese Yen) will print `&#165;` which will render as `¥`.

## Contributing

If you find this plugin useful you're welcome to contribute by issuing bug reports, cloning this repo and submitting pull requests.

### To do:
* Mirror this repo to SVN and get it to [wordpress.org](http://www.wordpress.org/plugins) plugins repo
* Currency metadata (for symbols, etc.) works but is incomplete
* Code optimizations
* WordPress multisite support is untested (should work)

### Ideas:
* Translate currency names automatically with Google according to locale?
* WooCommerce integration for multicurrency support?
* Advanced Custom Fields currency field?
* Gravity Forms currency field and integration for multicurrency?