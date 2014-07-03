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
 * Version:           1.0.0
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

register_activation_hook( __FILE__, array( 'WP_Currencies', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WP_Currencies', 'deactivate' ) );

// Main class
require_once( plugin_dir_path( __FILE__ ) . 'public/class-wp-currencies.php' );
add_action( 'plugins_loaded', array( 'WP_Currencies', 'get_instance' ) );

// Register shortcodes
add_shortcode( 'currency_convert', array( 'WP_Currencies', 'currency_conversion_shortcode' ) );
add_shortcode( 'currency_symbol', array( 'WP_Currencies', 'currency_symbol_shortcode' ) );

// Admin settings
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-wp-currencies-admin.php' );
	add_action( 'plugins_loaded', array( 'WP_Currencies_Admin', 'get_instance' ) );
}

// Functions
require_once plugin_dir_path( __FILE__ ) . 'public/functions.php';