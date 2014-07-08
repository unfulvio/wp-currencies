<?php
/**
 * WP Currencies Advanced Custom Fields currency field for ACF v4.x
 *
 * @package   WP Currencies
 * @author    nekojira <fulvio@nekojira.com>
 * @license   GPL-2.0+
 * @link      https://github.com/nekojira/wp-currencies/
 * @copyright 2014 nekojira
 */

/**
 * Class WP Currencies ACF v4
 *
 * @package WP Currencies
 * @author  nekojira <fulvio@nekojira.com>
 */
class WP_Currency_ACF_v4 extends acf_field {

	// vars
	var $settings, // will hold info such as dir / path
		$defaults; // will hold default field options

	/**
	 * Construct
	 * Set name / label needed for actions / filters
	 *
	 * @since    1.1.0
	 */
	function __construct() {

		// vars from WP Currencies
		$version = WP_Currencies::VERSION;
		$wp_currencies = WP_Currencies::get_instance();
		// textdomain
		$textdomain = $wp_currencies->get_plugin_slug();
		// field name
		$this->name = 'currency';
		$this->label = __( "Currency", $textdomain );
		// field type
		$this->category = __( "Choice",'acf' );
		// field default settings
		$this->defaults = array(
			'multiple' 		=>	0,		// disallow for multiple selection
			'allow_null' 	=>	0,		// disallow null choice
			'default_value'	=>	'USD'	// set US Dollar as default currency
		);

		// do not delete!
		parent::__construct();

		// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => $version
		);

	}

	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/

	function create_options( $field ) {

		// vars from WP Currencies
		$wp_currencies = WP_Currencies::get_instance();
		// textdomain
		$textdomain = $wp_currencies->get_plugin_slug();

		$key = $field['name'];

		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Default Value",'acf'); ?></label>
				<p class="description"><?php _e("Choose a default value", $textdomain ); ?></p>
			</td>
			<td>
				<?php

				do_action('acf/create_field', array(
					'type'	=>	'currency',
					'name'	=>	'fields['.$key.'][default_value]',
					'value'	=>	$field['default_value'],
					'allow_null' => 1,
				));

				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Allow Null?",'acf'); ?></label>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][allow_null]',
					'value'	=>	$field['allow_null'],
					'choices'	=>	array(
						1	=>	__("Yes",'acf'),
						0	=>	__("No",'acf'),
					),
					'layout'	=>	'horizontal',
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Select multiple values?",'acf'); ?></label>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][multiple]',
					'value'	=>	$field['multiple'],
					'choices'	=>	array(
						1	=>	__("Yes",'acf'),
						0	=>	__("No",'acf'),
					),
					'layout'	=>	'horizontal',
				));
				?>
			</td>
		</tr>
		<?php

	}

	/**
	 * Create field
	 * Creates the HTML interface for your field
	 *
	 * @param	array	$field	an array holding all the field's data
	 *
	 * @since	1.1.0
	 *
	 * @return	void
	 */
	function create_field( $field ) {

		// value must be array
		if( !is_array($field['value']) ) {
			// perhaps this is a default value with new lines in it?
			if( strpos($field['value'], "\n") !== false ) {
				// found multiple lines, explode it
				$field['value'] = explode("\n", $field['value']);
			} else {
				$field['value'] = array( $field['value'] );
			}
		}
		// trim value
		$field['value'] = array_map('trim', $field['value']);

		// multiple select
		$multiple = '';
		if ( $field['multiple'] ) {
			// create a hidden field to allow for no selections
			echo '<input type="hidden" name="' . $field['name'] . '" />';

			$multiple = ' multiple="multiple" size="5" ';
			$field['name'] .= '[]';
		}

		echo '<select id="' . $field['id'] . '" class="select" name="' . $field['name'] . '" ' . $multiple . '>';

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
	 * Format value for API
	 * This filter is applied to the $value after it is loaded from the db and before it is passed back to the API functions such as the_field
	 *
	 * @param	mixed	$value		the value which was loaded from the database
	 * @param	int		$post_id	the $post_id from which the value was loaded
	 * @param	array	$field		the field array holding all the field options
	 *
	 * @since	1.1.0
	 *
	 * @return	mixed	$value	the modified value
	 */
	function format_value_for_api( $value, $post_id, $field ) {

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
new WP_Currency_ACF_v4();