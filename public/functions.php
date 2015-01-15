<?php
/**
 * WP Currencies functions
 *
 * @package   WP Currencies
 * @author    nekojira <fulvio@nekojira.com>
 * @license   GPL-2.0+
 * @link      https://github.com/nekojira/wp-currencies/
 * @copyright 2014 nekojira
 */

/**
 * Get currency exchange rates.
 * Outputs an array or json object with a list of currency exchange rates.
 *
 * @since 	1.0.0
 *
 * @param	string	$currency	the base currency (default USD)
 * @param	bool 	$update		if true performs a check to update stored currency rates (default true)
 *
 * @return	array	an array with a list of $currency exchange rates
 */
function get_exchange_rates( $currency = 'USD', $update = true ) {

	// get rates from database
	$currencies = new WP_Currencies();
	$rates = $currencies->get_rates( $update );

	// rearrange data with different base currency
	if ( is_array( $rates ) && $currency != 'USD' ) :

		if ( ! currency_exists( $currency ) ) {
			trigger_error( __( 'Currency to get rates for was not found in database', 'wp_currencies' ), E_USER_WARNING );
			return '';
		}

		$new_rates = array();
		$base_rate = $rates[strtoupper($currency)];

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
 * Sends a json object with currency exchange rates for given currency
 *
 * @param	string	$currency	the base currency (default USD)
 * @param	bool 	$update		if true performs a check to update stored currency rates (default true)
 *
 * @since 	1.0.0
 *
 * @return 	void
 */
function get_exchange_rates_json( $currency = 'USD', $update = true ) {

	$rates = get_exchange_rates( strtoupper( $currency ), $update );
	wp_send_json( $rates );

}
add_action( 'wp_ajax_nopriv_get_exchange_rates', 'get_exchange_rates_json' );
add_action( 'wp_ajax_get_exchange_rates', 'get_exchange_rates_json' );


/**
 * Convert one currency into another
 * Converts an amount from a currency into another, according to rates
 *
 * @param   int 	$amount the amount of currency to convert from (default 1 = exchange rate)
 * @param	string	$from	the currency code to convert from (default USD)
 * @param 	string	$in		the currency code to convert in (default EUR)
 *
 * @since 	1.0.0
 *
 * @return	float|int	the resulting converted amount
 */
function convert_currency( $amount = 1, $from = 'USD', $in = 'EUR' ) {

	$rates = get_exchange_rates();

	$error = $result = '';
	// check first if rates exist
	if ( is_array( $rates ) && count( $rates ) > 100 ) {

		if ( ! currency_exists( $from ) OR ! currency_exists( $in ) ) {
			trigger_error( __( 'Currency does not exist or was not found in database', 'wp_currencies' ), E_USER_WARNING );
			$error = 1;
		}

		if ( is_nan( $amount ) ) {
			trigger_error( __( 'Amount to convert must be a number', 'wp_currencies' ), E_USER_WARNING );
			$error = 1;
		}

		if ( ! $error ) {

			$from   = strtoupper( $from );
			$in     = strtoupper( $in );
			$result = $rates[ $from ] && $rates[ $in ] ? (float) $amount * (float) $rates[ $in ] / (float) $rates[ $from ] : $amount;

		}

	} else {

		trigger_error( __( 'There was a problem fetching currency data from database', 'wp_currencies' ), E_USER_WARNING );

	}

	return $result;
}

/**
 * Get the currency exchange rate.
 * Gets the exchange rate of one currency to another.
 *
 * @since 	1.0.0
 *
 * @param	string	$currency		currency code to convert from
 * @param 	string	$other_currency	currency code to convert to
 *
 * @return float|int
 */
function get_exchange_rate( $currency, $other_currency ) {

	$currency = strtoupper( $currency );
	$other_currency = strtoupper( $other_currency );

	$rate = $currency == $other_currency ? 1 : convert_currency( 1, $currency, $other_currency );

	return $rate;
}

/**
 * Get currencies.
 * Returns an array with currency codes as keys and currency data for each currency.
 *
 * @since 	1.0.0
 *
 * @return	array	an array with currencies and currency data for each currency
 */
function get_currencies() {

	$currencies = new WP_Currencies();
	$currency_data = $currencies->get_currencies();

	return $currency_data;
}

/**
 * List of currencies as json object.
 * Outputs a json object containing currency codes and currency data for each currency.
 *
 * @since 	1.0.0
 *
 * @return	void
 */
function get_currencies_json() {

	$currencies = get_currencies();
	wp_send_json( $currencies );

}
add_action( 'wp_ajax_nopriv_get_currencies', 'get_currencies_json' );
add_action( 'wp_ajax_get_currencies', 'get_currencies_json' );

/**
 * Get currency data.
 * Returns an array with currency data corresponding to a specified currency code passed as argument
 *
 * @since 	1.0.0
 *
 * @param	string	$currency_code	the currency code of the currency to retrieve
 *
 * @return	array	returns an array with specified currency data
 */
function get_currency( $currency_code = 'USD' ) {

	$currencies = WP_Currencies::get_instance();

	if ( ! is_string( $currency_code ) OR strlen( $currency_code ) != 3 ) {
		trigger_error( __( 'You need to pass a valid currency code for argument and it must be a string of three characters long', $currencies->get_plugin_slug() ), E_USER_WARNING );
		die;
	}

	$currency_data = get_currencies();

	if ( ! array_key_exists( strtoupper( $currency_code ), $currency_data ) ) {
		trigger_error( __( 'The specified currency could not be found', $currencies->get_plugin_slug() ), E_USER_WARNING );
		die;
	}

	$currency = (array) $currency_data[strtoupper( $currency_code )];

	return $currency;
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

	if ( ! $currency ) {

		$symbol = $currency_symbol == true ? strtoupper( $currency_code ) : '';
		$result = $amount . ' ' . $symbol;

	} else {

		$formatted = number_format( $amount, $currency['decimals'], $currency['decimals_sep'], $currency['thousands_sep'] );

		if ( $currency_symbol == false )
			$result = $formatted;
		else
			$result = $currency['position'] == 'before' ? $currency['symbol'] . ' ' . $formatted : $formatted . ' ' . $currency['symbol'];

	}

	return $result;
}

/**
 * Check if a currency code is valid.
 * Helper function to check whether a currency exists in database by its code.
 *
 * @since   1.2.0
 *
 * @param   string  $currency_code   a three-letter ISO currency code
 *
 * @return  bool    true if the currency exists, false if it is unrecognized
 */
function currency_exists( $currency_code ) {

	$currencies = get_currencies();
	$codes = '';
	foreach ( $currencies as $key => $value )
		$codes[] = $key;

	$exists = in_array( strtoupper( $currency_code ), (array) $codes );
	return $exists;

}