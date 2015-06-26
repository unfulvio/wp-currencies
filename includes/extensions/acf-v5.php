<?php
/**
 * WP Currencies ACF 5 currency field
 *
 * Support for Advanced Custom Fields version 5.x
 *
 * @package WP_Currencies\ACF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class WP Currencies ACF v4
 *
 * Extends ACF 4.x with a Currency field.
 *
 * @since 1.1.3
 */
class WP_Currency_ACF_v5 extends acf_field {

	/**
	 * Setup field data.
	 *
	 * @since 1.1.3
	 */
	function __construct() {

		// Field basic properties.
		$this->name     = 'currency';
		$this->label    = __( 'Currency', 'wp_currencies' );
		$this->category = 'choice';
		// Field defaults
		$this->defaults = array(
			'multiple' 		=>	0,		// disallows for multiple choice
			'allow_null' 	=>	0,		// disallow null choice
			'default_value'	=>	'USD'	// US Dollar as default currency
		);

		parent::__construct();

	}

	/**
	 * Create settings for Currency field.
	 *
	 * @since 1.1.3
	 *
	 * @param array	$field The field settings.
	 */
	function render_field_settings( $field ) {

		$field['default_value'] = acf_encode_choices($field['default_value']);

		// default_value
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Default Value', 'acf' ),
			'instructions'	=> __( 'Choose a default value', 'acf' ),
			'type'			=> 'currency',
			'name'			=> 'default_value',
		));

		// allow_null
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Allow Null?', 'acf' ),
			'instructions'	=> '',
			'type'			=> 'radio',
			'name'			=> 'allow_null',
			'choices'		=> array(
				1				=> __( "Yes", 'acf' ),
				0				=> __( "No", 'acf' ),
			),
			'layout'	=>	'horizontal',
		));

		// multiple
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Select multiple values?', 'acf' ),
			'instructions'	=> '',
			'type'			=> 'radio',
			'name'			=> 'multiple',
			'choices'		=> array(
				1				=> __( "Yes", 'acf' ),
				0				=> __( "No", 'acf' ),
			),
			'layout'	=>	'horizontal',
		));

	}

	/**
	 * Create the HTML interface for Currency field.
	 *
	 * @since 1.1.3
	 *
	 * @param array $field The $field being edited.
	 */
	function render_field( $field ) {

		if ( ! is_array( $field['value'] ) ) {
			if ( $field['value'] && is_string( $field['value'] ) ) {
				$field['value'] = explode( ',', $field['value'] );
			} elseif ( ! empty( $field['value'] ) ) {
				$field['value'] = array( $field['value'] );
			}
		}

		if ( empty( $field['value'] ) ) {
			// add empty value (allows '' to be selected)
			$field['value'][''] = '';
		}

		// vars
		$atts = array(
			'id'				=> $field['id'],
			'class'				=> $field['class'],
			'name'				=> $field['name'],
			'data-multiple'		=> $field['multiple'],
			'data-allow_null'	=> $field['allow_null']
		);

		// hidden input
		if( $field['multiple'] ) {
			acf_hidden_input(array(
				'type'	=> 'hidden',
				'name'	=> $field['name'],
			));
		}

		// multiple
		if( $field['multiple'] ) {
			$atts['multiple'] = 'multiple';
			$atts['size'] = 5;
			$atts['name'] .= '[]';
		}

		// special atts
		foreach( array( 'readonly', 'disabled' ) as $k ) :
			if( !empty($field[ $k ]) ) {
				$atts[ $k ] = $k;
			}
		endforeach;

		// html
		echo '<select ' . acf_esc_attr( $atts ) . '>';

			// null option
			if ( $field['allow_null'] ) {
				echo '<option value="null">- ' . __("Select",'acf') . ' -</option>';
			}

			// get currencies via WP Currencies
			$currencies = get_currencies();

			// print options
			foreach( $currencies as $currency => $data ) :
				$data = (array) $data;
				$selected = in_array($currency, $field['value']) ? 'selected="selected"' : '';
				echo '<option value="' . $currency . '" ' . $selected . '>' . $currency . ' ' . $data['name'] . '</option>' . "\n";
			endforeach;

		echo '</select>';

	}

	/**
	 * Format value.
	 *
	 * This filter is applied to the $value after it is loaded from the db and before it is returned to the template.
	 *
	 * @since 1.1.3
	 *
	 * @param  mixed $value the Value which was loaded from the database.
	 * @param  mixed $post_id   The $post_id from which the value was loaded.
	 * @param  array $field the Field array holding all the field options.
	 *
	 * @return 	bool|array	currency data
	 */
	function format_value( $value, $post_id, $field ) {

		$currency = '';

		if ( $value == 'null' || $value == '' || $value == false ) :
			$currency = false;
		else :
			if ( is_array( $value ) ) {
				foreach ( $value as $code ) :
					$currency[$code] = get_currency( $code );
				endforeach;
			} else {
				$currency[$value] = get_currency( $value );
			}
		endif;

		return $currency;
	}

}

// create field
new WP_Currency_ACF_v5();
