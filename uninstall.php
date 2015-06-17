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

global $wpdb;

// Legacy options that might still be in database
delete_option( 'openexchangerates_key' );
delete_option( 'wp_currencies_freq' );
delete_option( 'wp_currencies' );
// Current settings option
delete_option( 'wp_currencies_settings' );

$GLOBALS['wpdb']->query("DROP TABLE '".$GLOBALS['wpdb']->prefix."currencies'");
$GLOBALS['wpdb']->query("OPTIMIZE TABLE '" .$GLOBALS['wpdb']->prefix."currencies'");