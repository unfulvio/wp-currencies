<?php

// Gets the openexchangerates.org API key if previously stored
$api_key = get_option( 'openexchangerates_key' );

// The number of days (int) to check for a currencies exchange rates update (defaults to one week)
$update_frequency = get_option( 'wp_currencies_freq' ) ? get_option( 'wp_currencies_freq' ) : 7;

?>
<div class="wrap">
	<h2>WP Currencies</h2>
	<p>
		<?php printf( _x( 'WP Currencies pulls currency data from %1s and imports it into the WordPress database. The exchange rates will be updated on a frequency that you can specify below.', 'openexchangerates.org link', $this->plugin_slug ), '<a href="//openexchangerates.org" target="_blank">openexchangerates.org</a>' ); ?>
		<br />
		<?php _e( 'Please refer to plugin documentation for functions usage to help you creating with WordPress and WP Currencies.', $this->plugin_slug ); ?>
	</p>
	<form action="options.php" method="post" name="options">
		<?php wp_nonce_field( 'update-options' ); ?>
		<table class="form-table">
			<tr>
				<td>
					<label for="openexchangerates_key">
						<?php _e( 'Open Exchange Rates API key:', $this->plugin_slug ); ?>
						<input type="text" id="openexchangerates_key" name="openexchangerates_key" value="<?php echo $api_key; ?>" class="regular-text">
					</label>
					<br>
					<small>
						<?php printf(
							_x( 'Get yours at: %1s', 'URL where to get the API key', $this->plugin_slug ),
							'<a href="//openexchangerates.org/" target="_blank">openexchangerates.org</a>' ); ?>
					</small>
				</td>
			</tr>
			<tr>
				<td>
					<label for="wp_currency_freq">
						<?php _e( 'Rates update frequency in days:', $this->plugin_slug ); ?>
						<input type="number" id="wp_currencies_freq" name="wp_currencies_freq" value="<?php echo $update_frequency; ?>" min="0" max="365" />
					</label>
					<br>
					<small>
						<?php _e( 'Specify the frequency in days when to update currencies exchange rates', $this->plugin_slug ); ?>
						<br>
						(<?php _e( 'if set to 0, will attempt to update the rates hourly', $this->plugin_slug ); ?>)
					</small>
				</td>
			</tr>
			<tr>
				<td>
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="page_options" value="openexchangerates_key,wp_currencies_freq" />
					<?php submit_button(); ?>
				</td>
			</tr>
		</table>
	</form>
	<hr />
	<h4><?php _e( 'Support', $this->plugin_slug ) ?></h4>
	<p>
		<?php _e( 'This plugin is free to use for any personal or commercial project and no warranty or support is guaranteed or provided.', $this->plugin_slug ); ?>
		<br />
		<?php printf( _x( 'You can contribute to WP Currencies on %1s by reporting bugs or issuing pull requests.', 'Github link', $this->plugin_slug ), '<a href="//github.com/nekojira/wp-currencies" target="_blank">Github</a>' ); ?>
		<br />
		<?php _e( 'If this plugin was helpful to any of your projects by any means, a donation with Paypal&reg; will be appreciated.', $this->plugin_slug ); ?>
	</p>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="S85XJGCAHYRR8">
		<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
		<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</form>
</div>