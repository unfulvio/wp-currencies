<?php
/**
 * WP Currencies
 *
 * @package   WP Currencies
 * @author    nekojira <fulvio@nekojira.com>
 * @license   GPL-2.0+
 * @link      https://github.com/nekojira/wp-currencies/
 * @copyright 2014 nekojira
 */

/**
 * WP Currencies main class
 *
 * @package WP Currencies
 * @author  nekojira <fulvio@nekojira.com>
 */
class WP_Currencies {

	/**
	 * Plugin version
	 *
	 * @since	1.0.0
	 *
	 * @var		string
	 */
	const VERSION = '1.0.0';

	/**
	 * Plugin unique identifier, also used for textdomain
	 *
	 * @since   1.0.0
	 *
	 * @var		string
	 */
	protected $plugin_slug = 'wp-currencies';

	/**
	 * Instance of this class.
	 *
	 * @since	1.0.0
	 *
	 * @var  	object
	 */
	protected static $instance = null;

	/**
	 * Remote URL for currencies list
	 *
	 * @since	1.0.0
	 *
	 * @var		string
	 */
	protected $currency_names;

	/**
	 * Remote URL for currencies rates
	 *
	 * @since	1.0.0
	 *
	 * @var		string
	 */
	protected $exchange_rates;

	/**
	 * Initialize the plugin and setting localization
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		$this->currency_names = 'http://openexchangerates.org/api/currencies.json';
		$this->exchange_rates = 'http://openexchangerates.org/api/latest.json?app_id=';

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Textdomain stuff
		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Return the plugin slug
	 *
	 * @since	1.0.0
	 *
	 * @return	string	the plugin slug variable
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
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
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();

					restore_current_blog();
				}

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

					restore_current_blog();

				}

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {

		global $wpdb;
		$table_name = $wpdb->prefix . 'currencies';

		// creates a database table to store and update currencies later
		$sql = "CREATE TABLE $table_name (
			currency_code VARCHAR(3) CHARACTER SET UTF8 NOT NULL,
			currency_rate FLOAT NOT NULL,
			currency_data VARCHAR(5000) CHARACTER SET UTF8 NOT NULL,
			timestamp TIMESTAMP DEFAULT 0 ON UPDATE CURRENT_TIMESTAMP,
			UNIQUE KEY currency_code (currency_code)
		);";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		// default database table update frequency (in days)
		add_option( 'wp_currencies_freq', 7 );

	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {

		delete_option( 'wp_currencies_freq' );

	}

	/**
	 * Update database with currency rates
	 * Puts new currency exchange rates in Open Exchange Rates database table
	 *
	 * @param	string	$action	database action to perform, must be either 'insert' or 'update'
	 *
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function update_data( $action = 'update' ) {

		// get openexchangerates.org API key
		$api_key = get_option( 'openexchangerates_key' );
		if ( ! $api_key ) {
			trigger_error( __( 'No valid openexchangerates.org API key found, go to WP Currencies settings page to register one', $this->plugin_slug ), E_USER_WARNING );
			die;
		}

		// check if there are already values in db
		$stored_rates = $this->get_rates( false );
		$action = empty( $stored_rates ) || $action == 'insert' ? 'insert' : 'update';

		// get the currencies rates with US dollars as base
		$rates = $this->get_json( $this->exchange_rates . $api_key, $key = 'rates' );

		// check if rates were fetched (expected an array with more than 100 currencies)
		if ( is_array( $rates ) && count( $rates ) > 100 ) :

			global $wpdb;
			$table = $wpdb->prefix . 'currencies';

			// prepare currency metadata
			$data = $this->make_currency_data();

			// cycle rates and write to db
			foreach ( $rates as $currency_code => $rate_usd ) :

				// currency metadata according to current key as currency code in array
				$currency_data = json_encode( $data[$currency_code] );

				if ( $action == 'update' ) {

					$wpdb->update(
						$table, array(
							'currency_rate' => $rate_usd, 				// current rate to USD
							'currency_data' => $currency_data, 			// currency data
							'timestamp' 	=> current_time( 'mysql' ) 	// store a timestamp to compare later
						),
						array( 'currency_code' => $currency_code )
					);

				} elseif ( $action == 'insert' ) {

					$wpdb->insert(
						$table, array(
							'currency_code' => $currency_code, 			// currency code
							'currency_rate' => $rate_usd, 				// current rate to USD
							'currency_data' => $currency_data, 			// currency data
							'timestamp' 	=> current_time( 'mysql' ) 	// store a timestamp to compare later
						)
					);

				}

			endforeach;

		else :

			trigger_error( __( 'An error occurred while updating exchange rates', $this->plugin_slug ), E_USER_NOTICE );
			die;

		endif;

	}

	/**
	 * Make currency data
	 * Returns data with currency information, according to currency code
	 *
	 * @since	1.0.0
	 *
	 * @return 	array
	 */
	private function make_currency_data() {
		$data = array();
		include_once dirname(__FILE__) . '/includes/currency-data.php';
		return $data;
	}

	/**
	 * Compare timestamps
	 * Compares timestamps of stored currency rates and updates database if they're older than set amount of time
	 *
	 * @param	string	$currency	currency to compare last updated timestamp (default EUR)
	 *
	 * @since	1.0.0
	 *
	 * @return	bool	returns true if a database update was performed, false if not
	 */
	private function compare_timestamps( $currency = 'EUR' ) {

		global $wpdb;
		$table = $wpdb->prefix . 'currencies';

		$rates = $this->get_rates( false );
		if ( empty( $rates ) ) {
			$this->update_data( 'insert' );
			die;
		}

		// get the timestamp from db table
		$timestamp = $wpdb->get_var( $wpdb->prepare(
			"
			SELECT timestamp
			FROM $table
			WHERE currency_code = %s
			",
			$currency
		) );

		// convert to UNIX time
		$timestamp = strtotime( $timestamp );
		// get update interval user option
		$frequency = get_option( 'wp_currencies_freq' ) ? get_option( 'wp_currencies_freq' ) : 7;
		// convert to UNIX time
		$refresh = $frequency <= 0 ? '-1 hour' : '-' . $frequency . ' days';
		$set_interval = strtotime( $refresh );
		// if more time has passed than the set frequency, update the currency exchange rates in db
		if ( $set_interval > $timestamp ) {
			$this->update_data( 'update' );
			return true;
		}

		// no update occurred
		return false;
	}

	/**
	 * Get json data from remote url
	 *
	 * @param 	string	$url	URL to get data from
	 * @param 	string	$key	(optional) json object key to read data from
	 *
	 * @since	1.0.0
	 *
	 * @return	object|mixed	returns result from given parameters
	 */
	public function get_json( $url, $key ) {

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$json = curl_exec( $ch );
		curl_close( $ch );
		$obj = json_decode( $json, true );
		$data = isset( $key ) && ! empty( $key ) ? $obj[$key] : $obj;

		return $data;
	}

	/**
	 * Get currency exchange rates
	 *
	 * @param	bool	$update	when true will perform a timestamp check and maybe update database
	 *
	 * @since	1.0.0
	 *
	 * @return 	array|string	if query is valid returns an array with currency exchange rates as stored in database
	 */
	public function get_rates( $update = true ) {

		global $wpdb;
		$table_name = $wpdb->prefix . 'currencies';

		// compare rates first and maybe update
		if ( $update == true ) {
			$this->compare_timestamps( 'EUR' );
		}

		$results = $wpdb->get_results(
			"SELECT * FROM $table_name", ARRAY_A
		);

		if ( ! empty( $results ) && is_array( $results ) ) :

			$rates = array();
			foreach ( $results as $row ) :

				$rates[$row['currency_code']] = (float) $row['currency_rate'];

			endforeach;

			return $rates;

		else :

			return '';

		endif;

	}

	/**
	 * Get currencies data
	 * Returns an array with data for each currency according to currency code
	 *
	 * @return	array	an array with currency codes for each key and currency information for each
	 */
	public function get_currencies() {

		global $wpdb;
		$table = $wpdb->prefix . 'currencies';

		$results = $wpdb->get_results(
			"SELECT * FROM $table", ARRAY_A
		);

		$currencies = array();
		if ( $results ) :

			foreach ( $results as $row ) :

				$currencies[$row['currency_code']] = json_decode( $row['currency_data'] );

			endforeach;

		endif;

		return $currencies;
	}

	/**
	 * Shortcode callback function to convert value in one currency into another
	 *
	 * @param	array	$atts	shortcode attributes
	 *
	 * @since	1.0.0
	 *
	 * @return 	string	the resulting converted amount
	 */
	public function currency_conversion_shortcode( $atts ) {

		$args = shortcode_atts( array(
			'amount'=> 1,
			'from' 	=> 'USD',
			'in' 	=> 'EUR',
			'round' => 2,
		), $atts );

		// convert currency
		$conversion = convert_currency( $args['amount'], $args['from'], $args['in'] );
		// round result
		$rounding = (int) $args['round'] >= 0 ? $args['round'] : 2;
		$converted_amount = round( $conversion, $rounding );

		return '<span class="converted-currency">' . $converted_amount . '</span>';
	}

	/**
	 * Shortcode callback function to output a currency symbol
	 *
	 * @param	array	$atts	shortcode attributs
	 *
	 * @since	1.0.0
	 *
	 * @return 	string	html entity of the symbol of the specified currency code
	 */
	public function currency_symbol_shortcode( $atts ) {

		$args = shortcode_atts( array(
			'currency' 	=> 'USD',
		), $atts );

		// get currency data
		$currency_data = get_currency( $args['currency'] );
		$symbol = $currency_data['symbol'];

		return '<span class"currency-symbol">' . $symbol . '</span>';
	}
}