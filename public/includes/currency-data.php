<?php

// get currencies names and codes from remote
$currency_names = $this->get_json( $this->currency_names, '' );

// check if data is fetched, expected array with more than 100 currencies
if ( is_array( $currency_names ) && count( $currency_names ) > 100 ) :

	foreach( $currency_names as $currency_code => $currency_name ) :

		// assign attributes to currencies
		switch( $currency_code ) :

			// Australian Dollar
			case 'AUD' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> 'A&#36;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

			// Brunei Dollar
			case 'BND' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> 'B&#36;',
					'position' 			=> 'before',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

			// Canadian Dollar
			case 'CAD' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> 'C&#36;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

			// Chinese Yuan
			case 'CNY' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> '&#20803;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

			// Euro
			case 'EUR' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> '&#8364;',
					'position' 			=> 'before',
					'decimals' 			=> 2,
					'thousands_sep'    	=> '.',
					'decimals_sep'      => ',',
				);
				break;

			// British Pound Sterling
			case 'GBP' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> '&#163;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

			// Japanese Yen
			case 'JPY' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> '&#165;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

			// Laos Kip
			case 'KIP' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> '&#8365;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

			// Indian Rupee
			case 'INR' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> '&#8377;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

			// Malaysian Ringgit
			case 'MYR' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> 'RM',
					'position' 			=> 'before',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

			// Russian Ruble
			case 'RUB' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> '&#8381;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

			// Singapore Dollar
			case 'SGD' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> 'S&#36;',
					'position' 			=> 'before',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

			// Thai Baht
			case 'THB' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> '&#3647;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

			// US Dollar
			case 'USD' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> '&#36;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

			// Vietnamese Dong
			case 'VND' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> '&#8363;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

			// Korean Won
			case 'WON' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> '&#8361;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

			// All others
			default :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> $currency_code,
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

		endswitch;

	endforeach;

endif;