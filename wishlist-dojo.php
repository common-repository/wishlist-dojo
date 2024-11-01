<?php
/*
Plugin Name: Wishlist Dojo
Plugin URI: http://happyplugins.com/downloads/wishlist-dojo
Description: Install in 1Click Wishlist Member Plugins or buy any Wishlist Member Dedicated Plugin. For more Unique WordPress plugins tools please visit the <a href="http://happyplugins.com" target="_blank">HappyPlugins</a>. Requires at least WordPress 3.0, Wishlist Member 2.8 and PHP 5.3
Author: HappyPlugins
Author URI: http://happyplugins.com
Version: 1.5.0
*/

require(dirname(__FILE__) . '/core/plugin_init.php');

function wishlist_dojo_plugin_updates() {

	/* Load Plugin Updater */
	 require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/plugin-updater.php' );
	//require(dirname(__FILE__) . '/core/includes/plugin-updater.php');

	/* Updater Config */
	$config = array(
		'base'      => plugin_basename( __FILE__ ), //required
		'repo_uri'    => 'http://updater.happyplugins.com',
		'repo_slug'   => 'wishlist-dojo',
	);

	/* Load Updater Class */
	// new Super_Mario_Plugin_Updater( $config );
	new Wishlist_Dojo_Plugin_Updater( $config);
}

/* hook updater to init */
add_action( 'init', 'wishlist_dojo_plugin_updates' );
