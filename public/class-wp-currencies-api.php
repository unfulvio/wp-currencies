<?php
/**
 * WP Currencies API
 *
 * Support for WordPress JSON REST API.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WP Currencies API class
 *
 * @package WP_Currencies
 */
class WP_Currencies_API {

	/**
	 * Instance of this class.
	 *
	 * @since	1.1.0
	 *
	 * @var  	object
	 */
	protected static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.1.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register JSON API routes
	 * Provides two endpoints for the API `/currencies/` and `/currencies/rates/`
	 *
	 * @param 	array	$routes	the REST API routes to filter
	 *
	 * @since 	1.1.0
	 *
	 * @return 	array	returns the filtered routes for the REST API
	 */
	public function register_routes( $routes ) {

		$routes['/currencies'] = array(
			array( array( $this, 'api_get_currencies' ), WP_JSON_Server::READABLE ),
		);
		$routes['/currencies/rates'] = array(
			array( array( $this, 'api_get_rates' ), WP_JSON_Server::READABLE ),
		);
		$routes['/currencies/rates?currency='] = array(
			array( array( $this, 'api_get_rates' ), WP_JSON_Server::READABLE ),
		);

		return $routes;
	}

	/**
	 * Get currency data API callback function
	 *
	 * @since   1.1.0
	 *
	 * @return 	array	currency data
	 */
	public function api_get_currencies() {

		$currencies = get_currencies();

		return $currencies;

	}

	/**
	 * Get currency rates API callback function
	 *
	 * @param	string	$currency	(optional) base currency, default US Dollars
	 *
	 * @since   1.1.0
	 *
	 * @return 	array	currency rates
	 */
	public function api_get_rates( $currency = 'USD' ) {

		$rates = get_exchange_rates( $currency );

		return $rates;

	}

}