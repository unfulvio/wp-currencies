<?php
/**
 * Exchange Rates
 *
 * Handling of currency data and exchange rates.
 *
 * @package WP_Currencies
 */
namespace WP_Currencies;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Exchange rates.
 *
 * Class that handles currency data in WP Currencies.
 *
 * @since 1.4.0
 */
class Rates {

	/**
	 * Currencies list.
	 *
	 * @since 1.4.0
	 * @access protected
	 * @var string
	 */
	protected $currencies_list = null;

	/**
	 * Exchange rates source.
	 *
	 * @since 1.4.0
	 * @access protected
	 * @var string
	 */
	protected $currencies_rates = null;

	/**
	 * Construct.
	 *
	 * @since 1.4.0
	 */
	public function __construct() {
		// Note: afaik SSL is supported by OpenExchangeRates on their paid subscription only
		$this->currencies_list  = 'http://openexchangerates.org/api/currencies.json';
		$this->currencies_rates = 'http://openexchangerates.org/api/latest.json?app_id=';
	}

	/**
	 * Update database with currency rates
	 *
	 * Puts new currency exchange rates in Open Exchange Rates database table
	 *
	 * @uses wp_remote_get() to get currencies from Open Exchange Rates.
	 *
	 * @since 1.4.0
	 *
	 * @return array|null The method also returns the fetched values (or null on error).
	 */
	public function update() {

		// Get openexchangerates.org API key.
		$option = get_option( 'wp_currencies_settings' );
		if ( ! isset( $option['api_key'] ) ) {
			// No key set in options. Bail out early.
			return null;
		}

		// Get the currencies rates (default base currency is US dollars).
		$response = wp_remote_get( $this->currencies_rates . $option['api_key'] );
		$json = isset( $response['body'] ) ? json_decode( $response['body'] ) : $response;
		$new_rates = isset( $json->rates ) ? (array) $json->rates : $json;

		// Check for request failure.
		if ( ! $new_rates instanceof \WP_Error ) {

			// Check if rates were fetched (expected an array with >100 currencies)
			if ( is_array( $new_rates ) && count( $new_rates ) > 100 ) {

				// Check whether there are already values in db.
				$old_rates  = $this->get_rates();
				$action     = ! $old_rates || is_null( $old_rates ) ? 'insert' : 'update';

				global $wpdb;
				$table = $wpdb->prefix . 'currencies';

				// Prepare currencies meta.
				$data = $this->make_currency_data();

				// Cycle rates and write to db.
				foreach ( $new_rates as $currency_code => $rate_usd ) :

					if ( is_string( $currency_code ) && $rate_usd && isset( $data[ $currency_code ] ) ) {
						// Sanitize.
						$currency_code = strtoupper( substr( sanitize_key( $currency_code ), 0, 3 ) );
						$rate_usd      = floatval( $rate_usd );
						// Currency data for current currency.
						$currency_data = json_encode( (array) $data[ $currency_code ] );
					} else {
						// skip if invalid
						continue;
					}

					// Update currencies with new values/rates.
					if ( $action == 'update' ) {

						// The currency list has changed.
						// @todo Improve checks for new currencies while updating db.
						if ( count( $old_rates ) != count( $new_rates ) ) {
							// Better start anew.
							$wpdb->delete(
								$table, array( 'currency_code' => $currency_code, )
							);
							// Reinsert.
							$wpdb->insert(
								$table, array(
									'currency_code' => $currency_code,
									'currency_rate' => $rate_usd,
									'currency_data' => $currency_data,
									'timestamp'     => current_time( 'mysql' )
								)
							);
							// The currency list hasn't changed.
							// @todo Improve checks for new currencies while updating db.
						} else {
							$wpdb->update(
								$table, array(
								'currency_rate' => $rate_usd,
								'currency_data' => $currency_data,
								'timestamp'     => current_time( 'mysql' )
							), array( 'currency_code' => $currency_code ) );
						}

						// Insert currencies and their rates in db.
					} elseif ( $action == 'insert' ) {
						$wpdb->insert(
							$table, array(
								'currency_code' => $currency_code,
								'currency_rate' => $rate_usd,
								'currency_data' => $currency_data,
								'timestamp'     => current_time( 'mysql' )
							)
						);
					}

				endforeach;

				do_action( 'wp_currencies_updated', $old_rates, $new_rates, time() );

			}

		} else {

			// @todo When update fails, perhaps we can read errors from wp_remote_get request.
			trigger_error(
				__( 'WP Currencies: there was a problem while trying to update currencies and exchange rates. Have you entered a valid API key? If yes, you might want to check your usage quota.', 'wp_currencies' ),
				E_USER_WARNING
			);

		}

		return $new_rates;
	}

	/**
	 * Make currency data.
	 *
	 * Helper function to return data with currency information, according to currency code.
	 *
	 * @since 1.4.0
	 *
	 * @access private
	 *
	 * @return array
	 */
	public function make_currency_data() {

		$currencies = array();

		$currency_data = wp_remote_get( $this->currencies_list );
		$currency_data = isset( $currency_data['body'] ) ? (array) json_decode( $currency_data['body'] ) : $currency_data;

		// Check if remote request didn't fail.
		if ( ! $currency_data instanceof \WP_Error ) {

			// Expecting an array with over 100 currencies.
			if ( is_array( $currency_data ) && count( $currency_data ) > 100 ) {

				foreach ( $currency_data as $currency_code => $currency_name ) {

					if ( ! is_string( $currency_code ) || ! is_string( $currency_name ) ) {
						continue;
					}

					$currency_code = strtoupper( substr( sanitize_key( $currency_code ), 0, 3 ) );
					// Defaults.
					$currencies[$currency_code] = array(
						'name'          => sanitize_text_field( $currency_name ),
						'symbol'        => $currency_code,
						'position'      => 'before',
						'decimals'      => 2,
						'thousands_sep' => ',',
						'decimals_sep'  => '.'
					);

				}

			}

		}

		// Format meta for each currency.
		include_once WP_CURRENCIES_INC . 'currencies/currency-data.php';
		$currency_data = wp_currencies_format_currency_data( $currencies );

		return (array) apply_filters( 'wp_currencies_make_currency_data', $currency_data );
	}

	/**
	 * Get currency exchange rates.
	 *
	 * @since 1.4.0
	 *
	 * @return array Associative array with currency codes for keys and exchange rates for values.
	 */
	public function get_rates() {

		// @todo Check if using transients while doing get_rates() improves speed.

		global $wpdb;
		$table_name = $wpdb->prefix . 'currencies';

		$results = $wpdb->get_results(
			"SELECT * FROM $table_name", ARRAY_A
		);

		$rates = array();
		if ( $results && is_array( $results ) ) {
			foreach ( $results as $row ) {
				$rates[strtoupper($row['currency_code'])] = floatval( $row['currency_rate'] );
			}
		}

		return $rates;
	}

	/**
	 * Get currencies data.
	 *
	 * @since 1.4.0
	 *
	 * @return array Associative array with currency codes for keys and currency information for values.
	 */
	public function get_currencies() {

		// @todo Check if using transients while doing get_currencies() improves speed.

		global $wpdb;
		$table = $wpdb->prefix . 'currencies';

		$results = $wpdb->get_results(
			"SELECT * FROM {$table}", ARRAY_A
		);

		$currencies = array();
		if ( $results && is_array( $results ) ) {
			foreach ( $results as $row ) {
				$currencies[$row['currency_code']] = (array) json_decode( $row['currency_data'] );
			}
		}

		return $currencies;
	}

}
