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
		add_action( 'wp_currencies_update', array( $this, 'update_currencies' ) );
	}

	/**
	 * Update currencies.
	 *
	 * Callback for 'wp_currencies_update' wp-cron job.
	 *
	 * @uses WP_Currencies\Rates\update() to update the currencies in db.
	 *
	 * @since 1.4.0
	 */
	public function update_currencies() {
		if ( defined( 'DOING_CRON' ) ) {
			$rates = new Rates();
			$rates->update();
		}
	}

	/**
	 * Schedule currency rates updates.
	 *
	 * Schedules a wp_cron job to update currencies at set interval.
	 *
	 * @since 1.4.0
	 */
	public function schedule_updates() {

		$option = get_option( 'wp_currencies_settings' );
		$interval = $option['update_interval'] ? $option['update_interval'] : 'weekly';

		if ( ! wp_next_scheduled( 'wp_currencies_update' ) ) {
			wp_schedule_event(   time(), $interval, 'wp_currencies_update' );
		} else {
			wp_reschedule_event( time(), $interval, 'wp_currencies_update' );
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
