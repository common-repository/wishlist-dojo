<?php

class WishlistDojo {

    var $plugin_parameters;
	var $links_url;
	var $debug;

    function __construct ($plugin_parameters){


        $this->plugin_parameters= $plugin_parameters;
		$this->links_url = "http://downloads.wishlistdojo.com";
		add_filter ('wp_footer' , array ($this, 'footer_message'));

    }

    function installPlugin ($source){

		// echo $source;


		$this->debug =true;

        require_once ABSPATH . 'wp-admin/includes/plugin-install.php'; // Need for plugins_api
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php'; // Need for upgrade classes

        $upgrader = new Plugin_Upgrader();


        WP_Filesystem();

		// $source = "http://downloads.wordpress.org/plugin/tumblr-importer.0.8.zip";


		// Check if url exist

		$response = wp_remote_request ($source);
		$response_code = $response['response']['code'];

		if ( $response_code==404){
				$message = "License Information is correct, no plugins has been installed (404)";
				echo "<p></p>";
				$this->displayMessage ($message , "error");
				return;
		 }


		if (! $this->isZip($source)) {
			$message =  'License Information is correct, no plugins has been installed (not zip)';
			echo "<p></p>";
			$this->displayMessage ($message , "error");
			return;


		}


        if (!$upgrader->check_package($source)) {
				$message =  'License Information is correct, no plugins has been installed (package error)';
				echo "<p></p>";
				$this->displayMessage ($message , "error");
				return;
		}

		echo '<span class="installing_message">Don\'t refresh the page and wait until the installation process is finished</span>';

        $upgrader->install( $source );

        $plugin_activate = $upgrader->plugin_info(); // Grab the plugin info from the Plugin_Upgrader method
        $activate = activate_plugin( $plugin_activate );

        wp_cache_flush();




    }


    function createDownloadLink ($action, $sku, $item_name , $vendor, $website_url,  $website_ip, $user_ip, $admin_email,  $license_info) {

          switch ($vendor) {

          case 'happyplugins':

			  $request_string = array (	'action'=>$action,
				  						'vendor'=>$vendor,
			  							'sku'=>$sku,
										'product'=>$item_name,
				  						'website_url' => $website_url,
				  						'website_ip' => $website_ip,
				  						'user_ip'=>$user_ip,
				  						'admin_email'=>	$admin_email,
				  						'license_key' => $license_info['key'],
			  							'time'=> time()
			  );

            break;

		   case 'wishlistmember':

			   $request_string = array ('action'=>$action,
										'vendor'=>$vendor,
										'sku'=>$sku,
				   					    'product'=>$item_name,
										'website_url' => $website_url,
										'website_ip' => $website_ip,
										'user_ip'=>$user_ip,
										'admin_email'=>	urlencode($admin_email),
										'license_email' => urlencode($license_info['email']),
										'license_key' => urlencode($license_info['key']),
										'time'=> time()

			   );

		  break;

        }

		/* convert and encode url */

		$query = http_build_query ($request_string);

		// echo $query;


		if ($this->debug) {
			$this->debugMessage ($query);
		}


		/* Encode Query */
		$query = $this->encode($query);
		$downloads_link = $this->links_url."/get.php?param=".$query;

		// $this->debugMessage ($downloads_link);

        return $downloads_link;


    }

    function isInstalled ($plugin_slug){


    }

    function isActive ($class_name) {

        // Get Class Name
        if (class_exists($class_name))  return true; // Plugin is active

        return false; // Plugin is not active

    }


	 public  function safe_b64encode($string) {
		 $data = base64_encode($string);
		 $data = str_replace(array('+','/','='),array('-','_',''),$data);
		 return $data;
	 }

        public function safe_b64decode($string) {
			$data = str_replace(array('-','_'),array('+','/'),$string);
			$mod4 = strlen($data) % 4;
			if ($mod4) {
				$data .= substr('====', $mod4);
			}
			return base64_decode($data);
		}

        public  function encode($value){
			if(!$value){return false;}
			$text = $value;
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
			$crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($this->plugin_parameters->secret_key), $text, MCRYPT_MODE_ECB, $iv);
			return trim($this->safe_b64encode($crypttext));
		}

        public function decode($value){
			if(!$value){return false;}
			$crypttext = $this->safe_b64decode($value);
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
			$decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($this->plugin_parameters->secret_key), $crypttext, MCRYPT_MODE_ECB, $iv);
			return trim($decrypttext);
		}

	function debugMessage($message) {

		echo "<pre>";
		print_r ($message);
		echo "</pre>";


	}

	function displayMessage ($message , $type) {

		?>
		<div class="<?php echo $type; ?>">
		<p>
			<?php echo $message;?>
		</p>
		</div>
		<?php

	}

	function isZip ($url) {
// Check if product is a zip
		$url_headers =  get_headers (  $url,  1 );
		if ($url_headers["Content-Type"]=='application/zip') return true;
		return false;
	}

	function footer_message (){
		$website_url = get_bloginfo('url');
		$utm = "?utm_source=".$website_url."&utm_medium=footer_link&utm_campaign=WishlistDojo";
		$dojo_link = "http://wishlistdojo.com".$utm;
		$hp_link = "http://happyplugins.com".$utm;

		?>
		<div style="text-align: center;">
		Powered by <a href="<?php echo $dojo_link; ?>">Wishlist Dojo</a> & <a href="<?php echo $hp_link; ?>">HappyPlugins</a> - eCommerce WordPress Plugins
		</div>
		<?php
	}



} // End Class