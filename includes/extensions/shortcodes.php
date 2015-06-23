<?php
/**
 * WP Currencies shortcodes
 *
 * @package WP_Currencies\Shortcodes
 */
namespace WP_Currencies;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Shortcodes.
 *
 * Add WordPress shortcodes for WP Currencies.
 *
 * @since 1.4.0
 */
class Shortcodes {

	/**
	 * Register shortcodes.
	 *
	 * @uses add_shortcode()
	 *
	 * @since 1.4.0
	 */
	public function __construct() {
		add_shortcode( 'currency_convert', array( $this, 'currency_conversion_shortcode' ) );
		add_shortcode( 'currency_symbol',  array( $this, 'currency_symbol_shortcode' ) );
	}

	/**
	 * Shortcode callback function to convert value in one currency into another
	 *
	 * @uses convert_currency()
	 *
	 * @since 1.4.0
	 *
	 * @param  array $atts Shortcode attributes.
	 * @return string The resulting converted amount
	 */
	public function currency_conversion_shortcode( $atts ) {

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
	 * Shortcode callback function to output a currency symbol.
	 *
	 * @uses get_currency()
	 *
	 * @since 1.4.0
	 *
	 * @param  array  $atts	Shortcode attributes.
	 * @return string HTML entity of the symbol of the specified currency code.
	 */
	public function currency_symbol_shortcode( $atts ) {

		$args = shortcode_atts( array(
			'currency' 	=> '',
		), $atts );

		// get currency data
		$currency_data = get_currency( strtoupper( $args['currency'] ) );
		$symbol = $currency_data['symbol'];

		return '<span class"currency currency-symbol">' . $symbol . '</span>';
	}


}

new Shortcodes();
