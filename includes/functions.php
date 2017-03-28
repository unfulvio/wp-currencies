<?php
/**
 * WP Currencies functions
 *
 * Public functions library.
 *
 * @package WP_Currencies
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get currency exchange rates.
 *
 * Outputs an array or json object with a list of currency exchange rates.
 *
 * @since 1.0.0
 *
 * @param  string $currency	The base currency for the rates (default USD).
 * @return array|null Associative array with currency codes for keys and rates for values.
 */
function get_exchange_rates( $currency = 'USD' ) {

	$rates = wp_currencies()->get_rates();
	if ( is_array( $rates ) && $currency !== 'USD' ) :

		if ( ! currency_exists( $currency ) ) {
			trigger_error(
				esc_html__( 'WP Currencies: Base currency to get rates for was not found in database.', 'wp_currencies' ),
				E_USER_WARNING
			);
			return null;
		}

		$new_rates = array();
		$base_rate = $rates[strtoupper( $currency )];

		while ( $array_key = current( $rates ) ) :
			$key = key( $rates );
			$new_rates[$key] = 1 * $rates[$key] / $base_rate;
			next( $rates );
		endwhile;

		$rates = $new_rates;

	endif;

	return $rates;
}

/**
 * Get a json object with currency exchange rates
 *
 * Sends a json object with currency exchange rates for given currency
 *
 * @since 1.0.0
 *
 * @param string $currency The base currency for the rates (default USD).
 */
function get_exchange_rates_json( $currency = 'USD' ) {
	$rates = get_exchange_rates( strtoupper( $currency ) );
	wp_send_json( $rates );
}
add_action( 'wp_ajax_nopriv_get_exchange_rates', 'get_exchange_rates_json' );
add_action( 'wp_ajax_get_exchange_rates', 'get_exchange_rates_json' );

/**
 * Convert one currency into another.
 *
 * Converts an amount from a currency into another, according to rates.
 *
 * @since 	1.0.0
 *
 * @param  int 	  $amount The amount of currency to convert from (default 1 = exchange rate).
 * @param  string $from	  The currency code to convert from (default USD).
 * @param  string $in     The currency code to convert in (default EUR).
 *
 * @return float|int The resulting converted amount.
 */
function convert_currency( $amount = 1, $from = 'USD', $in = 'EUR' ) {

	$rates = wp_currencies()->get_rates();

	$error = $result = '';
	// check first if rates exist
	if ( $rates && is_array( $rates ) && count( $rates ) > 100 ) {

		if ( ! currency_exists( $from ) || ! currency_exists( $in ) ) {
			trigger_error(
				esc_html__( 'WP Currencies: Currency does not exist or was not found in database.', 'wp_currencies' ),
				E_USER_WARNING
			);
			$error = true;
		}

		if ( ! is_numeric( $amount ) ) {
			trigger_error(
				esc_html__( 'WP Currencies: Amount to convert must be a number.', 'wp_currencies' ),
				E_USER_WARNING
			);
			$error = true;
		}

		if ( ! $error === true ) {
			$from   = strtoupper( $from );
			$in     = strtoupper( $in );
			$result = $rates[ $from ] && $rates[ $in ] ? (float) $amount * (float) $rates[ $in ] / (float) $rates[ $from ] : (float) $amount;
		}

	} else {

		trigger_error(
			esc_html__( 'WP Currencies: There was a problem fetching currency data from database. Is your API key valid?', 'wp_currencies' ),
			E_USER_WARNING
		);

	}

	return $result;
}

/**
 * Get the currency exchange rate.
 * Gets the exchange rate of one currency to another.
 *
 * @since 	1.0.0
 *
 * @param  string $currency       Currency code to convert from.
 * @param  string $other_currency Currency code to convert to.
 * @return float|int
 */
function get_exchange_rate( $currency, $other_currency ) {

	$currency       = strtoupper( $currency );
	$other_currency = strtoupper( $other_currency );

	return $currency === $other_currency ? 1 : convert_currency( 1, $currency, $other_currency );
}

/**
 * Get currencies.
 *
 * @since 1.0.0
 *
 * @return array An associative array with currency codes for keys and currency data for values.
 */
function get_currencies() {
	return wp_currencies()->get_currencies();
}

/**
 * List of currencies as json object.
 *
 * Outputs a json object containing currency codes and currency data for each currency.
 *
 * @uses wp_send_json()
 *
 * @since 1.0.0
 */
function get_currencies_json() {
	$currencies = get_currencies();
	if ( $currencies && is_array( $currencies ) ) {
		wp_send_json( $currencies );
	}
}
add_action( 'wp_ajax_nopriv_get_currencies', 'get_currencies_json' );
add_action( 'wp_ajax_get_currencies', 'get_currencies_json' );

/**
 * Get currency data.
 *
 * Returns an array with currency data corresponding to a specified currency code passed as argument
 *
 * @since 	1.0.0
 *
 * @param  string $currency_code The currency code of the currency to retrieve
 * @return array  Returns an array with specified currency data.
 */
function get_currency( $currency_code = 'USD' ) {

	if ( ! is_string( $currency_code ) || strlen( $currency_code ) !== 3 ) {
		trigger_error(
			esc_html__( 'WP Currencies: you need to pass a valid currency code for argument and it must be a string of three characters long', 'wp_currencies' ),
			E_USER_WARNING
		);
		return null;
	}

	$currency_data = get_currencies();

	if ( ! array_key_exists( strtoupper( $currency_code ), $currency_data ) ) {
		trigger_error(
			esc_html__( 'WP Currencies: the specified currency could not be found', 'wp_currencies' ),
			E_USER_WARNING
		);
		return null;
	}

	return (array) $currency_data[strtoupper( $currency_code )];
}

/**
 * Format currency.
 * Formats an amount in one specified currency according to currency data.
 *
 * @since 1.1.0
 *
 * @param 	int|float	$amount				the amount to format
 * @param	string 		$currency_code		the currency
 * @param	bool		$currency_symbol	true outputs the currency symbol, false does not (default true)
 *
 * @return	string	returns a string with formatted currency number and currency symbol
 */
function format_currency( $amount, $currency_code, $currency_symbol = true ) {

	if ( ! $amount || ! $currency_code OR is_nan( $amount ) )
		return '';

	$currency = get_currency( strtoupper( $currency_code ) );

	if ( null === $currency ){
		return '';
	}

	if ( ! $currency ) {
		$symbol = $currency_symbol === true ? strtoupper( $currency_code ) : '';
		$result = $amount . ' ' . $symbol;
	} else {
		$formatted = number_format( $amount, $currency['decimals'], $currency['decimals_sep'], $currency['thousands_sep'] );
		if ( $currency_symbol === false ) {
			$result = $formatted;
		} else {
			$result = $currency['position'] === 'before' ? $currency['symbol'] . ' ' . $formatted : $formatted . ' ' . $currency['symbol'];
		}
	}

	return html_entity_decode( $result );
}

/**
 * Check if a currency code is valid.
 *
 * Helper function to check whether a currency exists in database by its code.
 *
 * @since   1.2.0
 *
 * @param  string $currency_code A three-letter ISO currency code.
 * @return bool|null True if the currency exists, false if it is unrecognized. Null on error.
 */
function currency_exists( $currency_code ) {

	$currencies = get_currencies();
	$codes      = array();

	if ( $currencies && is_array( $currencies ) ) {
		foreach ( $currencies as $key => $value ) {
			$codes[] = $key;
		}
	}

	return $codes && is_array( $codes ) ? in_array( strtoupper( $currency_code ), (array) $codes, false ) : null;
}
