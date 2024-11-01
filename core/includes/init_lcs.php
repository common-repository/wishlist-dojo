<?php


require(dirname(__FILE__) . '/../plugin_parameters.php');
require_once(dirname(__FILE__) . '/plugin_config.class.php');


${'plugin_parameters_'.$pluginSlug} = new edd_plugin_config( $edd_store_url, $edd_store_name , $edd_item_name, $edd_item_slug , $plugin_version , $product_tags, $plugin_author , $plugin_db_slug , $plugin_name , $menu_name , $pluginSlug , $plugin_base , $license_local_expire_time, $license_retries , $secret_key, $main_menu_name, $main_menu_slug, $main_menu_position);

