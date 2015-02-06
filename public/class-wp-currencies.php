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
	const VERSION = '1.2.1';

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

		// Add wp_cron schedules
		add_filter( 'cron_schedules', array( $this, 'cron_schedules' ), 10, 1 ) ;

		// Define wp_cron job schedule event for currency exchange rates update
		add_action( 'wp_currencies_update', array( $this, 'update_currencies' ) );
		add_action( 'wp', array( $this, 'schedule_updates' ) );

		// Update wp_cron job schedule when settings are updated
		add_action( 'updated_option', array( $this, 'update_cron' ), 10, 3 );

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

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
	 * Fired upon plugin activation.
	 *
	 * @since    1.0.0
	 */
	private static function activate() {

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

	}

	/**
	 * Fired upon plugin deactivation.
	 *
	 * @since    1.0.0
	 */
	private static function deactivate() {

		// Clear WP Currencies wp_cron schedule
		wp_clear_scheduled_hook( 'wp_currencies_update' );

	}

	/**
	 * Update database with currency rates
	 * Puts new currency exchange rates in Open Exchange Rates database table
	 *
	 * @since	1.0.0
	 */
	public function update_currencies() {

		// get openexchangerates.org API key
		$option = get_option( 'wp_currencies_settings' );
		if ( ! isset( $option['api_key'] ) ) {
			trigger_error( __( 'No valid openexchangerates.org API key found, go to WP Currencies settings page to register one', 'wp_currencies' ), E_USER_WARNING );
			return;
		}

		// check if there are already values in db
		$stored_rates = $this->get_rates();
		$action = empty( $stored_rates ) ? 'insert' : 'update';

		// get the currencies rates with US dollars as base
		$rates = $this->get_json( $this->exchange_rates . $option['api_key'], $key = 'rates' );

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

					// the currency list has changed
					if ( count ( $stored_rates ) != count( $rates ) ) {

						// empty tables first
						$wpdb->delete(
							$table, array(
								'currency_code' => $currency_code,
							)
						);

						// reinsert
						$wpdb->insert(
							$table, array(
								'currency_code' => $currency_code,
								'currency_rate' => $rate_usd,
								'currency_data' => $currency_data,
								'timestamp' 	=> current_time( 'mysql' )
							)
						);

					} else {

						$wpdb->update(
							$table, array(
								'currency_rate' => $rate_usd,
								'currency_data' => $currency_data,
								'timestamp' 	=> current_time( 'mysql' )
							),
							array( 'currency_code' => $currency_code )
						);

					}

				} elseif ( $action == 'insert' ) {

					$wpdb->insert(
						$table, array(
							'currency_code' => $currency_code,
							'currency_rate' => $rate_usd,
							'currency_data' => $currency_data,
							'timestamp' 	=> current_time( 'mysql' )
						)
					);

				}

			endforeach;

		else :

			trigger_error( __( 'An error occurred while updating exchange rates', 'wp_currencies' ), E_USER_NOTICE );

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
		include_once dirname( __FILE__ ) . '/includes/currency-data.php';

		return $data;

	}

	/**
	 * Action fired upon frequency interval option update.
	 * Schedules a wp_cron job according to set interval.
	 *
	 * @since   1.2.0
	 *
	 * @param   $option
	 * @param   $new_value
	 * @param   $old_value
	 */
	public function update_cron( $option, $new_value, $old_value ) {

		if ( $option != 'wp_currencies_settings' )
			return;

		$saved = get_option( 'wp_currencies_settings' );

		if ( isset( $saved['update_interval'] ) ) {
			wp_clear_scheduled_hook( 'wp_currencies_update' );
			wp_schedule_event( time(), $saved['update_interval'], 'wp_currencies_update' );
			$this->update_wcml();
		}

	}

	/**
	 * Schedule currency rates updates.
	 * Schedules a wp_cron job to update currencies at set interval.
	 *
	 * @since   1.2.0
	 */
	public function schedule_updates() {

		$option = get_option( 'wp_currencies_settings' );

		// No interval set or invalid interval
		if ( ! isset( $option['update_interval'] ) )
			return;

		if ( ! wp_next_scheduled( 'wp_currencies_update' ) ) {
			wp_schedule_event( time(), $option['update_interval'], 'wp_currencies_update' );
			$this->update_wcml();
		}

	}

	/**
	 * Add new schedules to wp_cron.
	 * Adds weekly, biweekly and monthly schedule.
	 *
	 * @since   1.2.0
	 *
	 * @param   array   $schedules  wp_cron schedules
	 *
	 * @return  array
	 */
	public static function cron_schedules( $schedules ) {

		$schedules['weekly'] = array(
			'interval' => 604800,
			'display' => __( 'Once Weekly' )
		);
		$schedules['biweekly'] = array(
			'interval' => 1209600,
			'display' => __( 'Once Biweekly' )
		);
		$schedules['monthly'] = array(
			'interval' => 2419200,
			'display' => __( 'Once Monthly' )
		);

		return $schedules;

	}

	/**
	 * Updates WPML WooCommerce Multilanguage rates.
	 * If WCML extension is installed, will update the saved currency rates option.
	 *
	 * @since   1.2.0
	 */
	public function update_wcml() {

		// WCML stores exchange rates inside an option
		$wcml = get_option( '_wcml_settings' );
		// WooCommerce default store currency also is stored in an option
		$base_currency = get_option( 'woocommerce_currency' );

		// Check that both WCML and WooCommerce have been activated
		if ( ! isset( $wcml['currency_options'] ) OR ! $base_currency )
			return;

		$updated = $new_rates = '';
		// This replaces each rate and rebuilds the option data
		foreach ( $wcml['currency_options'] as $currency => $values ) {
			$new_rates[$currency] = $values;
			$new_rates[$currency]['rate'] = get_exchange_rate( $base_currency, $currency );
		}
		if ( $new_rates )
			$updated['currency_options'] = $new_rates;

		// Finally, overwrite WCML option data
		update_option( '_wcml_settings', $updated );

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
	 * @since	1.0.0
	 *
	 * @return 	array|string	if query is valid returns an array with currency exchange rates as stored in database
	 */
	public function get_rates() {

		global $wpdb;
		$table_name = $wpdb->prefix . 'currencies';

		$results = $wpdb->get_results(
			"SELECT * FROM $table_name", ARRAY_A
		);

		if ( ! empty( $results ) && is_array( $results ) ) {

			$rates = array();
			foreach ( $results as $row ) :

				$rates[ $row['currency_code'] ] = (float) $row['currency_rate'];

			endforeach;

			return $rates;

		}

		return '';

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
	public static function currency_conversion_shortcode( $atts ) {

		$args = shortcode_atts( array(
			'amount'=> '',
			'from' 	=> '',
			'in' 	=> '',
			'round' => 2,
		), $atts );

		// convert currency
		$conversion = convert_currency( floatval( $args['amount'] ), strtoupper( $args['from'] ), strtoupper( $args['in'] ) );
		// round result
		$rounding = intval( $args['round'] ) >= 0 ? intval( $args['round'] ) : 2;
		$converted_amount = round( $conversion, $rounding );

		return '<span class="currency converted-currency">' . $converted_amount . '</span>';

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
	public static function currency_symbol_shortcode( $atts ) {

		$args = shortcode_atts( array(
			'currency' 	=> '',
		), $atts );

		// get currency data
		$currency_data = get_currency( strtoupper( $args['currency'] ) );
		$symbol = $currency_data['symbol'];

		return '<span class"currency currency-symbol">' . $symbol . '</span>';

	}

}