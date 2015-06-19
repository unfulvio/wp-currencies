<?php
/**
 * WP Currencies uninstall
 *
 * Fired when the plugin is uninstalled.
 *
 * @package WP_Currencies
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; // exit if accessed directly
}

delete_option( 'wp_currencies_settings' );

global $wpdb;
$wpdb->hide_errors();

$currency_table = $wpdb->prefix . 'currencies';
$wpdb->query( "DROP TABLE IF EXISTS {$currency_table}" );
