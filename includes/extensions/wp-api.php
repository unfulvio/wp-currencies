<?php
/**
 * WP Currencies API
 *
 * Support for WordPress JSON REST API.
 *
 * @package WP_Currencies\API
 */

namespace WP_Currencies;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WP Currencies API class.
 *
 * Extends WP API with currencies routes.
 *
 * @since 1.4.0
 */
class API {

	/**
	 * Instance of this class.
	 *
	 * @since 1.4.0
	 * @access protected
	 * @var API
	 */
	protected static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.4.0
	 *
	 * @return API A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Register JSON API routes.
	 * Provides two endpoints for the API `/currencies/` and `/currencies/rates/`.
	 *
	 * @since 1.4.0
	 *
	 * @param  array $routes The REST API routes to filter.
	 * @return array Returns the filtered routes for the REST API.
	 */
	public function register_routes( $routes ) {

		$routes['/currencies'] = array(
			array( array( $this, 'get_currencies' ), \WP_JSON_Server::READABLE ),
		);
		$routes['/currencies/rates'] = array(
			array( array( $this, 'get_rates' ), \WP_JSON_Server::READABLE ),
		);
		$routes['/currencies/rates?currency='] = array(
			array( array( $this, 'get_rates' ), \WP_JSON_Server::READABLE ),
		);

		return $routes;
	}

	/**
	 * Get currency data API callback function.
	 *
	 * @since 1.4.0
	 *
	 * @return array Currencies data
	 */
	public function get_currencies() {
		return get_currencies();
	}

	/**
	 * Get currency rates API callback function
	 *
	 * @since 1.4.0
	 *
	 * @param  string $currency	(optional) Base currency, default US Dollars.
	 * @return array  Currency rates.
	 */
	public function get_rates( $currency = 'USD' ) {
		return get_exchange_rates( $currency );
	}

}
