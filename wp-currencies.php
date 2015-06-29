<?php
/**
 * Plugin Name: WP Currencies
 * Plugin URI:  https://github.com/nekojira/wp-currencies
 * Description: Currency data and updated currency exchange rates for WordPress.
 * Version:     1.4.6
 * Author:      Fulvio Notarstefano
 * Author URI:  https://github.com/nekojira
 * License:     GPLv2+
 * Text Domain: wp_currencies
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2015
 * Fulvio Notarstefano (fulvio.notarstefano@gmail.com) and contributors.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * WP Currencies requires PHP 5.4.0 minimum.
 * WordPress supports 5.2.4 and recommends 5.4.0.
 * @link https://make.wordpress.org/plugins/2015/06/05/policy-on-php-versions/
 */
if ( version_compare( PHP_VERSION, '5.4.0', '<') ) {
	add_action( 'admin_notices',
		function() {
			echo '<div class="error"><p>'.
			     sprintf( __( "WP Currencies requires PHP 5.4 or above to function properly. Detected PHP version on your server is %s. Please upgrade PHP to activate WP Currencies or remove the plugin.", 'wp_currencies' ), phpversion() ? phpversion() : '`undefined`' ) .
			     '</p></div>';
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	);
	return;
}

if ( ! class_exists( 'WP_Currencies') ) :

	// Useful global constants.
	define( 'WP_CURRENCIES_VERSION', '1.4.0' );
	define( 'WP_CURRENCIES_URL',     plugin_dir_url( __FILE__ ) );
	define( 'WP_CURRENCIES_PATH',    dirname( __FILE__ ) . '/' );
	define( 'WP_CURRENCIES_INC',     WP_CURRENCIES_PATH . 'includes/' );

	/**
	 * WP Currencies main class.
	 *
	 * @since 1.0.0
	 */
	final class WP_Currencies {

		/**
		 * WP Currencies static instance.
		 *
		 * @since 1.4.0
		 * @access protected
		 * @var WP_Currencies
		 */
		protected static $_instance;

		/**
		 * WP Currencies data and rates.
		 *
		 * @since 1.4.0
		 * @access public
		 * @var WP_Currencies\Rates
		 */
		public $currencies = null;

		/**
		 * Main WP_Currencies instance.
		 *
		 * Ensures only one instance of WP_Currencies is loaded.
		 *
		 * @since 1.4.0
		 *
		 * @return WP_Currencies
		 */
		public static function get_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Cloning is forbidden.
		 *
		 * @since 1.4.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cloning the main instance of WP_Currencies is forbidden.', 'wp_currencies' ), WP_CURRENCIES_VERSION );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 1.4.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of WP_Currencies is forbidden.', 'wp_currencies' ), WP_CURRENCIES_VERSION );
		}

		/**
		 * Construct.
		 *
		 * @since 1.4.0
		 */
		public function __construct() {
			$this->includes();
			$this->init();
			$this->currencies = new WP_Currencies\Rates();
			do_action( 'wp_currencies_loaded' );
		}

		/**
		 * Include WP Currencies library.
		 *
		 * @since 1.4.0
		 *
		 * @access private
		 */
		private function includes() {
			$path = WP_CURRENCIES_INC;
			// Core components.
			include_once $path . 'rates.php';
			include_once $path . 'functions.php';
			include_once $path . 'cron.php';
			include_once $path . 'install.php';
			// Admin settings.
			if ( is_admin() ) {
				include_once $path . 'settings.php';
			}
		}

		/**
		 * Initialize the plugin.
		 *
		 * @since 1.4.0
		 */
		public function init() {

			// Install.
			register_activation_hook(   __FILE__, array( 'WP_Currencies\\Install', 'activate' )  );
			register_deactivation_hook( __FILE__, array( 'WP_Currencies\\Install', 'deactivate' ) );

			// Load textdomain.
			$this->load_plugin_i18n();

			// Advanced Custom Fields (ACF) support.
			$enable_acf = apply_filters( 'wp_currencies_enable_acf_support', true );
			if ( $enable_acf === true ) {
				// Advanced Custom Fields "Currency" Field (ACF v4.x+).
				add_action( 'acf/register_fields', function () {
					include_once WP_CURRENCIES_INC . 'extensions/acf-v4.php';
				} );
				// Advanced Custom Fields "Currency" Field (ACF v5.x+).
				add_action( 'acf/include_field_types', function () {
					include_once WP_CURRENCIES_INC . 'extensions/acf-v5.php';
				} );
			}

			// WP API support.
			$enable_api = apply_filters( 'wp_currencies_enable_api_support', true );
			if ( $enable_api === true ) {
				include_once  WP_CURRENCIES_INC . '/extensions/wp-api.php';
				add_action( 'plugins_loaded', array( 'WP_Currencies\API', 'get_instance' ) );
				add_action( 'wp_json_server_before_serve', array( $this, 'api_init' ) );
			}

			// Shortcodes.
			$enable_shortcodes = apply_filters( 'wp_currencies_enable_shortcodes', true );
			if ( $enable_shortcodes === true ) {
				include_once WP_CURRENCIES_INC . '/extensions/shortcodes.php';
			}


		}

		/**
		 * WP Currencies i18n.
		 *
		 * @since 1.4.0
		 */
		public function load_plugin_i18n() {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'wp_currencies' );
			load_textdomain( 'wp_currencies', WP_LANG_DIR . '/wp_currencies/wp_currencies-' . $locale . '.mo' );
			load_plugin_textdomain( 'wp_currencies', false, plugin_basename( WP_CURRENCIES_PATH ) . '/languages/' );
		}

		/**
		 * Init WP Currencies API extension.
		 *
		 * @since 1.4.0
		 */
		public function api_init() {
			$wp_currencies_api = new WP_Currencies\API;
			add_filter( 'json_endpoints', array( $wp_currencies_api, 'register_routes' ) );
		}

		/**
		 * Get currencies data.
		 *
		 * @since 1.4.0
		 *
		 * @return array
		 */
		public function get_currencies() {
			$currencies = '';
			if ( $this->currencies instanceof WP_Currencies\Rates ) {
				$currencies = $this->currencies->get_currencies();
			}
			return apply_filters( 'wp_currencies_get_currencies', $currencies );
		}

		/**
		 * Get exchange rates.
		 *
		 * @since 1.4.0
		 *
		 * @return array
		 */
		public function get_rates() {
			$rates = '';
			if ( $this->currencies instanceof WP_Currencies\Rates ) {
				$rates = $this->currencies->get_rates();
			}
			return apply_filters( 'wp_currencies_get_rates', $rates );
		}

	}

else :

	add_action( 'admin_notices',
		function() {
			echo '<div class="error"><p>'.
			     sprintf( __( "Plugin conflict: %s has been declared already by another plugin or theme and WP Currencies cannot run properly. Try deactivating other plugins and try again.", 'wp_currencies' ), '`class WP_Currencies`' ) .
			     '</p></div>';
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	);
	return;

endif;

if ( ! function_exists( 'wp_currencies' ) ) {

	/**
	 * Update currencies and exchange rates.
	 *
	 * Normally this function works as a wp cron scheduled event hook callback.
	 * However, if called directly will reschedule the event triggering an update.
	 *
	 * @internal
	 *
	 * @since 1.4.5
	 */
	function wp_currencies_update() {
		$cron = new WP_Currencies\Cron();
		$cron->cron_update_currencies();
	}

	/**
	 * WP Currencies.
	 *
	 * @since 1.4.0
	 *
	 * @return WP_Currencies
	 */
	function wp_currencies() {
		$wp_currencies = new WP_Currencies();
		return $wp_currencies::get_instance();
	}

	// Instantiate.
	wp_currencies();


} else {

	add_action( 'admin_notices',
		function() {
			echo '<div class="error"><p>'.
			     sprintf( __( "Plugin conflict: %s has been declared already by another plugin or theme and WP Currencies cannot run properly. Try deactivating other plugins and try again.", 'wp_currencies' ), '`function wp_currencies()`' ) .
			     '</p></div>';
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	);

}
