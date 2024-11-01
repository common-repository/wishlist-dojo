<?php
require(dirname(__FILE__) . '/includes/init_lcs.php');
require( dirname( __FILE__ ) . '/tracking.php' );
require_once(dirname(__FILE__) . '/wishlist-dojo.class.php');
require(dirname(__FILE__) . '/admin_menus.class.php');

/* Init Plugin Actions */
new WishlistDojo(${'plugin_parameters_'.$pluginSlug});
new WishlistDojo_admin_menus(${'plugin_parameters_'.$pluginSlug});


