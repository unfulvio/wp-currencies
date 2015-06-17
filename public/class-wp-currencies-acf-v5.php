<?php
/**
 * WP Currencies ACF 5 currency field
 *
 * Support for Advanced Custom Fields version 5.x
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class WP Currencies ACF v5
 *
 * @package WP_Currencies
 */
class WP_Currency_ACF_v5 extends acf_field {

	/**
	 * __construct
	 * This function will setup the field type data
	 *
	 * @since 1.1.3
	 */
	function __construct() {

		$plugin = WP_Currencies::get_instance();
		$textdomain = $plugin->get_plugin_slug();

		// name of the field
		$this->name = 'currency';
		$this->label = __( 'Currency', $textdomain );
		// field type
		$this->category = 'choice';
		// defaults
		$this->defaults = array(
			'multiple' 		=>	0,		// disallows for multiple choice
			'allow_null' 	=>	0,		// disallow null choice
			'default_value'	=>	'USD'	// US Dollar default currency
		);

		// do not delete!
		parent::__construct();

	}

	/**
	 * render_field_settings()
	 * Creates settings for field
	 *
	 * @param	array	$field	the field
	 *
	 * @since 1.1.3
	 */
	function render_field_settings( $field ) {

		$plugin = WP_Currencies::get_instance();
		$textdomain = $plugin->get_plugin_slug();

		$field['default_value'] = acf_encode_choices($field['default_value']);

		// default_value
		acf_render_field_setting( $field, array(
			'label'			=> __('Default Value','acf'),
			'instructions'	=> __('Choose a default value', $textdomain ),
			'type'			=> 'currency',
			'name'			=> 'default_value',
		));

		// allow_null
		acf_render_field_setting( $field, array(
			'label'			=> __('Allow Null?','acf'),
			'instructions'	=> '',
			'type'			=> 'radio',
			'name'			=> 'allow_null',
			'choices'		=> array(
				1				=> __( "Yes", 'acf'),
				0				=> __( "No", 'acf'),
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
				1				=> __( "Yes", 'acf'),
				0				=> __( "No", 'acf'),
			),
			'layout'	=>	'horizontal',
		));

	}

	/**
	 * render_field()
	 * Create the HTML interface for your field
	 *
	 * @param	array	$field	the $field being edited
	 *
	 * @since 1.1.3
	 */
	function render_field( $field ) {

		// convert value to array
		$field['value'] = acf_force_type_array($field['value']);

		// add empty value (allows '' to be selected)
		if( empty($field['value']) ){
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
	 * format_value()
	 *
	 * This filter is applied to the $value after it is loaded from the db and before it is returned to the template
	 *
	 * @param	mixed	$value the value which was loaded from the database
	 * @param 	mixed	$post_id the $post_id from which the value was loaded
	 * @param 	array	$field the field array holding all the field options
	 *
	 * @since 1.1.3
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