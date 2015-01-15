<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   WP Currencies
 * @author    nekojira <fulvio@nekojira.com>
 * @license   GPL-2.0+
 * @link      https://github.com/nekojira/wp-currencies/
 * @copyright 2014 nekojira
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit;

global $wpdb;

// Legacy options that might still be in database
delete_option( 'openexchangerates_key' );
delete_option( 'wp_currencies_freq' );
delete_option( 'wp_currencies' );
// Current settings option
delete_option( 'wp_currencies_settings' );

$GLOBALS['wpdb']->query("DROP TABLE '".$GLOBALS['wpdb']->prefix."currencies'");
$GLOBALS['wpdb']->query("OPTIMIZE TABLE '" .$GLOBALS['wpdb']->prefix."currencies'");