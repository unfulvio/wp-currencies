<?php
/**
 * WP Currencies
 *
 * A WordPress plugin to import a complete list of World currencies into WordPress.
 * Each currency will include updated exchange rates from openexchangerates.org
 *
 * @package   WP Currencies
 * @author    nekojira <fulvio@nekojira.com>
 * @license   GPL-2.0+
 * @link      https://github.com/nekojira/wp-currencies
 * @copyright 2014 nekojira
 *
 * @wordpress-plugin
 * Plugin Name:       WP Currencies
 * Plugin URI:        https://github.com/nekojira/wp-currencies
 * Description:       Bring currency data and updated currency exchange rates into WordPress.
 * Version:           1.2.4
 * Author:            nekojira
 * Author URI:        https://github.com/nekojira/
 * Text Domain:       wp-currencies
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/nekojira/wp-currencies
  */

// If this file is called directly, abort
if ( ! defined( 'WPINC' ) )
	die;

// WP Currencies main class
require_once 'public/class-wp-currencies.php';
// Instantiates the main class
add_action( 'plugins_loaded', array( 'WP_Currencies', 'get_instance' ) );

/**
 * Plugin activation hook.
 *
 * @since    1.2.2
 */
function wp_currencies_activation() {

	global $wpdb;
	$table_name = $wpdb->prefix . 'currencies';

	// skip if db table already exists
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) :

		// creates a database table to store and update currencies later
		$sql = "CREATE TABLE $table_name (
				currency_code VARCHAR(3) CHARACTER SET UTF8 NOT NULL,
				currency_rate FLOAT NOT NULL,
				currency_data VARCHAR(5000) CHARACTER SET UTF8 NOT NULL,
				timestamp TIMESTAMP DEFAULT 0 ON UPDATE CURRENT_TIMESTAMP,
				UNIQUE KEY currency_code (currency_code)
			);";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$wpdb->query( $sql );

	endif;

}
register_activation_hook( __FILE__, 'wp_currencies_activation' );

/**
 * Plugin deactivation hook.
 *
 * @since    1.2.2
 */
function wp_currencies_deactivation() {

	// Clear WP Currencies wp_cron schedule
	wp_clear_scheduled_hook( 'wp_currencies_update' );

}
register_deactivation_hook( __FILE__, 'wp_currencies_deactivation' );

// WP Currencies API class (extends JSON REST API if available)
require_once 'public/class-wp-currencies-api.php';
add_action( 'plugins_loaded', array( 'WP_Currencies_API', 'get_instance' ) );
function wp_currencies_api_init() {
	$currencies_api = new WP_Currencies_API;
	add_filter( 'json_endpoints', array( $currencies_api, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'wp_currencies_api_init' );

// Advanced Custom Fields "Currency" Field (ACF v4.x+)
add_action( 'acf/register_fields',
	function() {
		require_once 'public/class-wp-currencies-acf-v4.php';
	}
);
// Advanced Custom Fields "Currency" Field (ACF v5.x+)
add_action('acf/include_field_types',
	function() {
		require_once 'public/class-wp-currencies-acf-v5.php';
	}
);

// Register shortcodes
add_shortcode( 'currency_convert', array( 'WP_Currencies', 'currency_conversion_shortcode' ) );
add_shortcode( 'currency_symbol', array( 'WP_Currencies', 'currency_symbol_shortcode' ) );

// Admin settings
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once 'admin/class-wp-currencies-admin.php';
	add_action( 'plugins_loaded', array( 'WP_Currencies_Admin', 'get_instance' ) );
}

// Functions
require_once 'public/functions.php';