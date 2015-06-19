<?php
/**
 * Currency data
 *
 * @package WP_Currencies\Rates
 */

/**
 * Helper function to format currency data.
 *
 * @todo Use gettext for $currency_data['name'].
 * @todo Update currency symbols, symbol postions and separators.
 *
 * @since 1.4.0
 *
 * @param  $currencies
 *
 * @return array
 */
function wp_currencies_format_currency_data( $currencies = '' ) {

	$data = $currencies;

	if ( $currencies && is_array( $currencies ) && count( $currencies ) > 100 ) :

		foreach ( $currencies as $currency_code => $currency_data ) :

			if ( ! $currency_data['symbol'] || ! isset( $currency_data['name'] ) ) {
				continue;
			}

			if ( $currency_code == 'AUD' ) {

				// Australian Dollar
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => 'A&#36;',
					'position'      => 'after',
					'decimals'      => 2,
					'thousands_sep' => ',',
					'decimals_sep'  => '.',
				);

			} elseif ( $currency_code == 'BRL' ) {

				// Brazilian Real
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => 'R&#36;',
					'position'      => 'after',
					'decimals'      => 2,
					'thousands_sep' => '&nbsp;',
					'decimals_sep'  => '.',
				);

			} elseif ( $currency_code == 'BND' ) {

				// Brunei Dollar
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => 'B&#36;',
					'position'      => 'before',
					'decimals'      => 2,
					'thousands_sep' => ',',
					'decimals_sep'  => '.',
				);

			} elseif ( $currency_code == 'BTC' || $currency_code == 'XBT') {

				// Bitcoin
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => $currency_data['symbol'],
					'position'      => 'before',
					'decimals'      => 2,
					'thousands_sep' => ',',
					'decimals_sep'  => '.',
				);

			} elseif ( $currency_code == 'CAD' ) {

				// Canadian Dollar
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => 'C&#36;',
					'position'      => 'after',
					'decimals'      => 3,
					'thousands_sep' => '&nbsp;',
					'decimals_sep'  => ',',
				);

			} elseif ( $currency_code == 'CHF' ) {

				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => 'SFr.',
					'position'      => 'after',
					'decimals'      => 3,
					'thousands_sep' => '&nbsp;',
					'decimals_sep'  => ',',
				);

			} elseif ( $currency_code == 'CNY' ) {

				// Chinese Renmimbi (Yuan)
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => '&#165',
					'position'      => 'before',
					'decimals'      => 2,
					'thousands_sep' => ',',
					'decimals_sep'  => '.',
				);

			} elseif ( $currency_code == 'DKK' ) {

				// Danish Crown
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => 'kr.',
					'position'      => 'after',
					'decimals'      => 3,
					'thousands_sep' => '&nbsp;',
					'decimals_sep'  => ',',
				);

			} elseif ( $currency_code == 'EUR' ) {

				// Euro
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => '&#8364;',
					'position'      => 'before',
					'decimals'      => 2,
					'thousands_sep' => '.',
					'decimals_sep'  => ',',
				);

			} elseif ( $currency_code == 'GBP' ) {

				// British Pound
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => '&#163;',
					'position'      => 'after',
					'decimals'      => 2,
					'thousands_sep' => ',',
					'decimals_sep'  => '.',
				);

			} elseif ( $currency_code == 'JPY' ) {

				// Japanese Yen
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => '&#165;',
					'position'      => 'after',
					'decimals'      => 0,
					'thousands_sep' => ',',
					'decimals_sep'  => '.',
				);

			} elseif ( $currency_code == 'LAK' ) {

				// Laos Kip
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => '&#8365;',
					'position'      => 'after',
					'decimals'      => 2,
					'thousands_sep' => '.',
					'decimals_sep'  => ',',
				);

			} elseif ( $currency_code == 'HKD' ) {

				// Hong Kong Dollar
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => 'HK&#36;',
					'position'      => 'before',
					'decimals'      => 2,
					'thousands_sep' => ',',
					'decimals_sep'  => '.',
				);

			} elseif ( $currency_code == 'IDR' ) {

				// Indonesian Rupee
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => '&#8377;',
					'position'      => 'after',
					'decimals'      => 2,
					'thousands_sep' => '.',
					'decimals_sep'  => ',',
				);

			} elseif ( $currency_code == 'MMK' ) {

				// Burmese Kyat
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => 'Ks',
					'position'      => 'after',
					'decimals'      => 2,
					'thousands_sep' => ',',
					'decimals_sep'  => '.',
				);

			} elseif ( $currency_code == 'MYR' ) {

				// Malaysian Ringgit
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => 'RM',
					'position'      => 'before',
					'decimals'      => 2,
					'thousands_sep' => '.',
					'decimals_sep'  => ',',
				);

			} elseif ( $currency_code == 'MXN' ) {

				// Mexican Peso
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => 'Mex#36;',
					'position'      => 'after',
					'decimals'      => 2,
					'thousands_sep' => '&nbsp;',
					'decimals_sep'  => ',',
				);

			} elseif ( $currency_code == 'NZD' ) {

				// New Zealand Dollar
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => 'NZ&#36;',
					'position'      => 'after',
					'decimals'      => 2,
					'thousands_sep' => ',',
					'decimals_sep'  => '.',
				);

			} elseif ( $currency_code == 'NOK' ) {

				// Norwegian Crown
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => 'kr',
					'position'      => 'after',
					'decimals'      => 3,
					'thousands_sep' => '.',
					'decimals_sep'  => ',',
				);

			} elseif ( $currency_code == 'PLN' ) {

				// Polish złoty
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => 'z&#322;',
					'position'      => 'after',
					'decimals'      => 2,
					'thousands_sep' => '.',
					'decimals_sep'  => ',',
				);

			} elseif ( $currency_code == 'PHP' ) {

				// Philippines Peso
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => '&#8369;',
					'position'      => 'after',
					'decimals'      => 2,
					'thousands_sep' => ',',
					'decimals_sep'  => '.',
				);

			} elseif ( $currency_code == 'RON' ) {

				// Romanian Leu
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => 'Lei',
					'position'      => 'after',
					'decimals'      => 2,
					'thousands_sep' => '.',
					'decimals_sep'  => ',',
				);

			} elseif ( $currency_code == 'RUB' ) {

				// Russian Ruble
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => '&#8381;',
					'position'      => 'after',
					'decimals'      => 2,
					'thousands_sep' => '.',
					'decimals_sep'  => ',',
				);

			} elseif ( $currency_code == 'SAR' ) {

				// Saudi Ryal
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => 'SR',
					'position'      => 'after',
					'decimals'      => 3,
					'thousands_sep' => ',',
					'decimals_sep'  => '.',
				);

			} elseif ( $currency_code == 'SGD' ) {

				// Singapore Dollar
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => 'S&#36;',
					'position'      => 'before',
					'decimals'      => 2,
					'thousands_sep' => '.',
					'decimals_sep'  => ',',
				);

			} elseif ( $currency_code == 'SEK' ) {

				// Swedish Crown
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => 'kr',
					'position'      => 'after',
					'decimals'      => 2,
					'thousands_sep' => '.',
					'decimals_sep'  => ',',
				);

			} elseif ( $currency_code == 'THB' ) {

				// Thai Baht
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => '&#3647;',
					'position'      => 'after',
					'decimals'      => 2,
					'thousands_sep' => ',',
					'decimals_sep'  => '.',
				);

			} elseif ( $currency_code == 'TRY' ) {

				// Turkish Lira
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => '&#8378;',
					'position'      => 'after',
					'decimals'      => 2,
					'thousands_sep' => ',',
					'decimals_sep'  => '.',
				);

			} elseif ( $currency_code == 'TWD' ) {

				// Taiwan New Dollar
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => 'NT&#36;',
					'position'      => 'after',
					'decimals'      => 2,
					'thousands_sep' => ',',
					'decimals_sep'  => '.',
				);

			} elseif ( $currency_code == 'USD' ) {

				// US Dollar
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => '&#36;',
					'position'      => 'before',
					'decimals'      => 2,
					'thousands_sep' => ',',
					'decimals_sep'  => '.',
				);

			} elseif ( $currency_code == 'VND' ) {

				// Vietnamese Dong
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => '&#8363;',
					'position'      => 'after',
					'decimals'      => 2,
					'thousands_sep' => ',',
					'decimals_sep'  => '.',
				);

			} elseif ( $currency_code == 'WON' ) {

				// Korean Won
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => '&#8361;',
					'position'      => 'after',
					'decimals'      => 2,
					'thousands_sep' => ',',
					'decimals_sep'  => '.',
				);

			} else {

				// All others
				$data[$currency_code] = array(
					'name'          => $currency_data['name'],
					'symbol'        => $currency_data['symbol'],
					'position'      => 'after',
					'decimals'      => 2,
					'thousands_sep' => ',',
					'decimals_sep'  => '.',
				);

			}

		endforeach;

	endif;

	return (array) $data;

}
