<?php
/**
 * WP Currencies settings
 *
 * @package WP_Currencies
 */
namespace WP_Currencies;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Settings class.
 *
 * Uses the WordPress Settings API to handle WP Currencies settings.
 *
 * @since 1.4.0
 */
class Settings {

	/**
	 * Constructor.
	 *
	 * @since 1.4.0
	 */
	public function __construct() {

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

		// Register settings.
		add_action( 'admin_init', array( $this, 'settings_init' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . 'wp_currencies.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		// Update wp_cron job schedule when settings are updated
		add_action( 'update_option_wp_currencies_settings', array( $this, 'updated_option' ), 10, 2 );

	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 *
	 * @param  array $links
	 * @return array
	 */
	public function add_action_links( $links ) {
		return array_merge(
			array( 'settings' => '<a href="' . admin_url( 'options-general.php?page=wp_currencies' ) . '">' . __( 'Currencies', 'wp_currencies' ) . '</a>' ),
			$links
		);
	}

	/**
	 * Add an admin menu page.
	 * Uses WP Settings API for the plugin settings page.
	 *
	 * @since 1.4.0
	 */
	public function add_admin_menu(  ) {
		add_options_page(
			__( 'WP Currencies', 'wp_currencies' ),
			__( 'Currencies', 'wp_currencies' ),
			'manage_options',
			'wp_currencies',
			array( $this, 'options_page' )
		);
	}

	/**
	 * Register plugin settings.
	 *
	 * Uses WP Settings API for the plugin settings page.
	 *
	 * @since 1.4.0
	 */
	public function settings_init(  ) {
		register_setting(
			'wp_currencies',
			'wp_currencies_settings'
		);
		add_settings_section(
			'wp_currencies_settings_section',
			__( 'Settings', 'wp_currencies' ),
			array( $this, 'print_settings' ),
			'wp_currencies'
		);
		add_settings_field(
			'api_key',
			__( 'API Key', 'wp_currencies' ),
			array( $this, 'print_api_key_field' ),
			'wp_currencies',
			'wp_currencies_settings_section'
		);
		add_settings_field(
			'update_interval',
			__( 'Update Interval', 'wp_currencies' ),
			array( $this, 'print_interval_field' ),
			'wp_currencies',
			'wp_currencies_settings_section'
		);
	}

	/**
	 * Print the update interval setting form field.
	 *
	 * Plugin settings field callback function.
	 *
	 * @since 1.4.0
	 */
	public function print_interval_field(  ) {

		$option = get_option( 'wp_currencies_settings' );
		$update_frequency = isset( $option['update_interval'] ) ? esc_attr( $option['update_interval'] ) : 'weekly';

		?>
		<label for="update_interval">
			<?php _e( 'Rates update frequency:', 'wp_currencies' ); ?>
			<select name="wp_currencies_settings[update_interval]" id="update_interval">
				<option value="hourly"   <?php selected( $update_frequency, 'hourly',   true ); ?>><?php _e( 'Hourly',  'wp_currencies' ); ?></option>
				<option value="daily"    <?php selected( $update_frequency, 'daily',    true ); ?>><?php _e( 'Daily',   'wp_currencies' ); ?></option>
				<option value="weekly"   <?php selected( $update_frequency, 'weekly',   true ); ?>><?php _e( 'Weekly',  'wp_currencies' ); ?></option>
				<option value="biweekly" <?php selected( $update_frequency, 'biweekly', true ); ?>><?php _e( 'Biweekly','wp_currencies' ); ?></option>
				<option value="monthly"  <?php selected( $update_frequency, 'monthly',  true ); ?>><?php _e( 'Monthly', 'wp_currencies' ); ?></option>
			</select>
		</label>
		<br>
		<small>
			<?php _e( 'Specify the frequency when to update currencies exchange rates', 'wp_currencies' ); ?>
		</small>
		<?php

	}

	/**
	 * Print the API Key setting form field.
	 *
	 * Plugin settings field callback function.
	 *
	 * @since   1.4.0
	 */
	public function print_api_key_field() {

		$option = get_option( 'wp_currencies_settings' );
		$api_key = isset( $option['api_key'] ) ? esc_attr( $option['api_key'] ) : '';

		?>
		<label for="api_key">
			<?php _e( 'Open Exchange Rates API key:', 'wp_currencies' ); ?>
			<input type="password" id="api_key" name="wp_currencies_settings[api_key]" value="<?php echo $api_key; ?>" class="regular-text">
		</label>
		<br>
		<small>
			<?php printf(
				_x( 'Get yours at: %1s', 'URL where to get the API key', 'wp_currencies' ),
				'<a href="//openexchangerates.org/" target="_blank">openexchangerates.org</a>' ); ?>
		</small>
		<?php

	}

	/**
	 * Settings field section callback.
	 *
	 * @since   1.4.0
	 */
	public function print_settings(  ) {
		?>
		<p>
			<?php printf(
				_x( 'WP Currencies pulls currency data from %1s and imports it into the WordPress database. The exchange rates will be updated on a frequency that you can specify below.',
					'openexchangerates.org link', 'wp_currencies' ),
				'<a href="//openexchangerates.org" target="_blank">openexchangerates.org</a>' ); ?>
			<br />
			<?php _e( 'Please refer to plugin documentation for functions usage to help you creating with WordPress and WP Currencies.', 'wp_currencies' ); ?>
		</p>
		<?php
	}

	/**
	 * Print the settings option page form fields.
	 *
	 * Uses the WP Settings API.
	 *
	 * @since   1.4.0
	 */
	public function options_page(  ) {
		?>
		<form action="options.php" method="post">
			<h2><?php _e( 'WP Currencies', 'wp_currencies' ); ?></h2>
			<?php settings_fields( 'wp_currencies' ); ?>
			<?php do_settings_sections( 'wp_currencies' ); ?>
			<?php submit_button(); ?>
		</form>
		<?php
	}

	/**
	 * Updated option hook callback.
	 *
	 * @since   1.4.0
	 *
	 * @param string $old_value
	 * @param string $new_value
	 */
	public function updated_option( $old_value, $new_value ) {

		// Is there an update?
		if ( $old_value != $new_value ) {

			wp_clear_scheduled_hook( 'wp_currencies_update' );

			// Makes sure there's an API key (won't be able to tell if valid, but at least is not empty).
			$api_key = isset( $new_value['api_key'] ) ? $new_value['api_key'] : ( isset( $old_value['api_key'] ) ? $old_value['api_key'] : '' );

			if ( ! empty( $api_key ) ) {

				$interval = isset( $new_value['update_interval'] ) ? $new_value['update_interval'] : ( isset( $old_value['update_interval'] ) ? $old_value['update_interval'] : 'weekly' );

				$cron = new Cron();
				$cron->schedule_updates( $api_key, $interval );

				do_action( 'wp_currencies_rescheduled_update', time(), esc_attr( $new_value['update_interval'] ) );

			}

		}

	}

}

new Settings();
