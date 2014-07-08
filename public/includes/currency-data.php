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

			// Brazilian real
			case 'BRL' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> 'R&#36;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> '&nbsp;',
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

			// Brunei Dollar
			case 'BTC' :
			case 'XBT' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> $currency_code,
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
					'decimals' 			=> 3,
					'thousands_sep'    	=> '&nbsp;',
					'decimals_sep'      => ',',
				);
				break;

			// Swiss Franc
			case 'CHF' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> 'SFr.',
					'position' 			=> 'after',
					'decimals' 			=> 3,
					'thousands_sep'    	=> '&nbsp;',
					'decimals_sep'      => ',',
				);
				break;

			// Chinese Yuan
			case 'CNY' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> '&#165',
					'position' 			=> 'before',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;


			// Danish krone
			case 'DKK' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> 'kr.',
					'position' 			=> 'after',
					'decimals' 			=> 3,
					'thousands_sep'    	=> '&nbsp;',
					'decimals_sep'      => ',',
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
					'decimals' 			=> 0,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

			// Laos Kip
			case 'LAK' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> '&#8365;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> '.',
					'decimals_sep'      => ',',
				);
				break;

			// Hong Kong Dollar
			case 'HKD' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> 'HK&#36;',
					'position' 			=> 'before',
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
					'thousands_sep'    	=> '.',
					'decimals_sep'      => ',',
				);
				break;

			// Burmese Kyat
			case 'MMK' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> 'Ks',
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
					'thousands_sep'    	=> '.',
					'decimals_sep'      => ',',
				);
				break;

			// Mexican Peso
			case 'MXN' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> 'Mex#36;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> '&nbsp;',
					'decimals_sep'      => ',',
				);
				break;

			// New Zealand Dollar
			case 'NZD' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> 'NZ&#36;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

			// Norwegian krone
			case 'NOK' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> 'kr',
					'position' 			=> 'after',
					'decimals' 			=> 3,
					'thousands_sep'    	=> '.',
					'decimals_sep'      => ',',
				);
				break;

			// Polish złoty
			case 'PLN' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> 'z&#322;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> '.',
					'decimals_sep'      => ',',
				);
				break;

			// Philippine Peso
			case 'PHP' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> '&#8369;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

			// Romanian Leu
			case 'RON' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> 'Lei',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> '.',
					'decimals_sep'      => ',',
				);
				break;

			// Russian Ruble
			case 'RUB' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> '&#8381;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> '.',
					'decimals_sep'      => ',',
				);
				break;

			// Saudi Ryal
			case 'SAR' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> 'SR',
					'position' 			=> 'after',
					'decimals' 			=> 3,
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
					'thousands_sep'    	=> '.',
					'decimals_sep'      => ',',
				);
				break;

			// Swedish krona
			case 'SEK' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> 'kr',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> '.',
					'decimals_sep'      => ',',
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

			// Turkish Lira
			case 'TRY' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> '&#8378;',
					'position' 			=> 'after',
					'decimals' 			=> 2,
					'thousands_sep'    	=> ',',
					'decimals_sep'      => '.',
				);
				break;

			// Taiwan Dollar
			case 'TWD' :
				$data[$currency_code] = array(
					'name' 				=> $currency_name,
					'symbol'			=> 'NT&#36;',
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
					'thousands_sep'    	=> '.',
					'decimals_sep'      => ',',
				);
				break;

		endswitch;

	endforeach;

endif;