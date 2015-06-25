# WP Currencies

[![GitHub version](https://badge.fury.io/gh/nekojira%2Fwp-currencies.svg)](http://badge.fury.io/gh/nekojira%2Fwp-currencies)
[![Join the chat at https://gitter.im/nekojira/wp-currencies](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/nekojira/wp-currencies?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

WP Currencies is a [WordPress](http://www.wordpress.org) plugin that imports and periodically updates a complete set of World currencies and their exchange rates from [openexchangerates.org](https://openexchangerates.org) to WordPress database for later retrieval and use.

This plugin is intended for WordPress backend and frontend developers who want to build with currency data from within WordPress.

[![Download from WordPress.org](https://github.com/nekojira/wp-currencies/blob/master/assets/wordpress-download-btn.png)](https://wordpress.org/plugins/wp-currencies/)


## Features

* Pulls currency exchange rates from [openexchangerates.org](https://openexchangerates.org)
* Imports rates and currency data into a local database table
* Periodically and automatically updates currency rates by set cron intervals
* If you use [WooCommerce](https://github.com/woothemes/woocommerce) and [WPML WooCommerce Multilingual](https://wordpress.org/plugins/woocommerce-multilingual/), you can use [WCML hooks](https://wpml.org/documentation/related-projects/woocommerce-multilingual/multi-currency-support-woocommerce/) together with WP Currencies functions to change currency rates on the fly.
* Packed with a handy [PHP functions library](https://github.com/nekojira/wp-currencies/wiki/Functions) to retrieve currency data and exchange rates or perform currency conversions on the fly via PHP or [Ajax](https://github.com/nekojira/wp-currencies/wiki/Ajax)
* Registers new [JSON REST API](https://wordpress.org/plugins/json-rest-api/) [routes](https://github.com/nekojira/wp-currencies/wiki/WP-REST-API) for currency data and exchange
* Adds a [Currency Field](https://github.com/nekojira/wp-currencies/wiki/Advanced-Custom-Field) to [Advanced Custom Fields](https://www.advancedcustomfields.com/) plugin, if installed
* Provides [Shortcodes](https://github.com/nekojira/wp-currencies/wiki/Shortcodes) to print currency data or conversion rates from a WordPress post or page
* Comes complete with [action and filter hooks](https://github.com/nekojira/wp-currencies/wiki/Hooks) for extensions and customisations

## Requirements

* WordPress >= 4.0
* PHP >= 5.4
* MySQL >= 5.5
* A valid API key for [openexchangerates.org](https://openexchangerates.org)

## Documentation

Read the [WP Currencies documentation on Github wiki](https://github.com/nekojira/wp-currencies/wiki).

## Contributing
If you find this plugin useful you're welcome to contribute by issuing bug reports, cloning this repo and submitting pull requests. Thank you.
