<?php

if (!class_exists('edd_plugin_config')) {

    class edd_plugin_config {

        var $edd_store_url;
        var $edd_item_name;
        var $edd_item_slug;
        var $plugin_version;
        var $product_tags;
        var $plugin_author;
        var $edd_store_name;
        var $plugin_name;
        var $menu_name;
        var $plugin_slug;
        var $plugin_base;
        var $license_local_expire_time; // hours
        var $license_retries; // Times
        var $secret_key;

        var $main_menu_name;
        var $main_menu_slug;
        var $main_menu_position;




        function __construct ($edd_store_url,$edd_store_name ,$edd_item_name ,$edd_item_slug , $plugin_version , $product_tags, $plugin_author , $plugin_db_slug , $plugin_name , $menu_name , $plugin_slug, $plugin_base, $license_local_expire_time = 24, $license_retries = 3 , $secret_key = 'abcdefghijklmnoprstuvwzyz' ,$main_menu_name, $main_menu_slug, $main_menu_position){

            $this->edd_store_url = $edd_store_url;
            $this->edd_store_name = $edd_store_name;
            $this->edd_item_name = $edd_item_name;
            $this->edd_item_slug = $edd_item_slug;
            $this->plugin_version = $plugin_version;
            $this->product_tags = $product_tags;
            $this->plugin_author =  $plugin_author;
            $this->plugin_db_slug =  $plugin_db_slug;
            $this->plugin_name = $plugin_name;
            $this->menu_name = $menu_name;
            $this->plugin_slug = $plugin_slug;
            $this->plugin_base = $plugin_base;

            $this->license_local_expire_time = $license_local_expire_time;
            $this->license_retries = $license_retries;
            $this->secret_key = $secret_key;

            $this->main_menu_name = $main_menu_name;
            $this->main_menu_slug = $main_menu_slug;
            $this->main_menu_position = $main_menu_position;



        }

    }

}
