<?php
/**
 * WP Currencies cron handling
 *
 * @package WP_Currencies\Cron
 */
namespace WP_Currencies;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WP Currencies cron jobs.
 *
 * Updates currency rates at period intervals.
 *
 * @since 1.4.0
 */
class Cron {

	/**
	 * Cron setup.
	 *
	 * @since 1.4.0
	 */
	public function __construct() {
		add_filter( 'cron_schedules', array( $this, 'cron_schedules' ), 10, 1 ) ;
		add_action( 'wp_currencies_update', array( __CLASS__, 'update_currencies' ) );
	}

	/**
	 * Update currencies.
	 *
	 * Callback for 'wp_currencies_update' action.
	 *
	 * @since 1.4.0
	 */
	public function update_currencies() {
		//if ( defined( 'DOING_CRON' ) ) {
			do_action( 'wp_currencies_before_update', time() );
			$rates = new Rates();
			$rates->update();
		//}
	}

	/**
	 * Update currencies scheduled event callback.
	 *
	 * Fires the 'wp_currencies_update' action.
	 *
	 * @since 1.4.6
	 */
	public function cron_update_currencies() {
		do_action( 'wp_currencies_update' );
	}

	/**
	 * Schedule currency rates updates.
	 *
	 * Schedules a wp_cron job to update currencies at set interval.
	 *
	 * @since 1.4.0
	 *
	 * @param string $api_key
	 * @param string $interval
	 */
	public function schedule_updates( $api_key = '', $interval = '' ) {

		if ( empty( $api_key ) || empty(  $interval ) ) {
			$option = get_option( 'wp_currencies_settings' );
			$api_key = isset( $option['api_key'] ) ? $option['api_key'] : '';
			$interval = isset( $option['update_interval'] ) ? $option['update_interval'] : '';
		}

		if ( $api_key && $interval ) {

			if ( ! wp_next_scheduled( 'wp_currencies_update' ) ) {
				wp_schedule_event(   time(), $interval, 'wp_currencies_update' );
			} else {
				wp_reschedule_event( time(), $interval, 'wp_currencies_update' );
			}

		}

	}

	/**
	 * Add new schedules to wp_cron.
	 *
	 * Adds weekly, biweekly and monthly schedule.
	 *
	 * @since 1.4.0
	 *
	 * @param  array $schedules Unfiltered wp_cron schedules.
	 * @return array Filtered schedules.
	 */
	public function cron_schedules( $schedules ) {
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display' => __( 'Once Weekly', 'wp_currencies' )
		);
		$schedules['biweekly'] = array(
			'interval' => 1209600,
			'display' => __( 'Once Biweekly', 'wp_currencies' )
		);
		$schedules['monthly'] = array(
			'interval' => 2419200,
			'display' => __( 'Once Monthly', 'wp_currencies' )
		);
		return $schedules;
	}

}

new Cron();
