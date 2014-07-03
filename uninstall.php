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

if ( is_multisite() ) {

	// delete_option('OPTION_NAME');

	$blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );

	if ( $blogs ) :

		foreach ( $blogs as $blog ) :

			switch_to_blog( $blog['blog_id'] );

			delete_option( 'openexchangerates_key' );
			delete_option( 'wp_currencies_freq' );

			$GLOBALS['wpdb']->query("DROP TABLE '".$GLOBALS['wpdb']->prefix."currencies'");
			$GLOBALS['wpdb']->query("OPTIMIZE TABLE '" .$GLOBALS['wpdb']->prefix."currencies'");

			restore_current_blog();

		endforeach;

	endif;

} else {

	delete_option( 'openexchangerates_key' );
	delete_option( 'wp_currencies_freq' );

	$GLOBALS['wpdb']->query("DROP TABLE '".$GLOBALS['wpdb']->prefix."currencies'");
	$GLOBALS['wpdb']->query("OPTIMIZE TABLE '" .$GLOBALS['wpdb']->prefix."currencies'");

}