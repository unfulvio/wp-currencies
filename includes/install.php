<?php
/**
 * WP Currencies installation class.
 *
 * What happens when WP Currencies is activated or deactivated.
 *
 * @package WP_Currencies
 */
namespace WP_Currencies;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Installation static class.
 *
 * @since 1.4.0
 */
class Install {

	/**
	 * Activate WP Currencies.
	 *
	 * What happens when WP Currencies is activated.
	 *
	 * @since 1.4.0
	 */
	public static function activate() {

		self::create_tables();

		$cron = new Cron();
		$cron->schedule_updates();

		do_action( 'wp_currencies_activated' );

	}

	/**
	 * Deactivate WP Currencies.
	 *
	 * What happens when WP Currencies is deactivated.
	 *
	 * @since 1.4.0
	 */
	public static function deactivate() {

		// Delete options
		delete_option( 'wp_currencies_settings' );

		// Remove WP Currencies cron job.
		wp_clear_scheduled_hook( 'wp_currencies_update' );

		do_action( 'wp_currencies_deactivated' );

	}

	/**
	 * Create currency tables.
	 *
	 * @uses dbDelta()
	 * @access private
	 *
	 * @since 1.4.0
	 */
	private static function create_tables() {
		global $wpdb;
		$wpdb->hide_errors();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		// Create tables (if not exist)
		dbDelta( self::get_schema() );
	}

	/**
	 * WP Currencies schema.
	 *
	 * @since 1.4.0
	 *
	 * @access private
	 */
	private static function get_schema() {

		global $wpdb;

		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		$currency_table = $wpdb->prefix . 'currencies';

		return "
CREATE TABLE IF NOT EXISTS `{$currency_table}` (
  `currency_code` VARCHAR(3) NOT NULL,
  `currency_rate` FLOAT NOT NULL,
  `currency_data` VARCHAR(5000) NOT NULL,
  `timestamp` TIMESTAMP DEFAULT 0 ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY currency_code (currency_code)
) {$collate};
		";

	}

}
