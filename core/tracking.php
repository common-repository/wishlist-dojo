<?php
/*
 * Tracking functions for reporting plugin usage to the EDD site for users that have opted in
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class WishlistDojo_Tracking {

	public function __construct() {
		add_action ('admin_init', array ($this, 'save_tracking_settings'));
		add_action( 'admin_notices', array( $this, 'admin_notice' ));
		add_action('wp_print_scripts', array($this,'scripts'));
	}

	function scripts(){

		// Check if tracking is allowed
		$settings = get_option( "wldojo_settings" );

		if ($settings['allow_tracking'] && is_admin()) {

			wp_enqueue_script( 'dojo_fb_track', plugins_url('/js/fb_track.js', __FILE__) , array(), '1.0.0', true );
			wp_enqueue_script( 'dojo_google_track', plugins_url('/js/gg_track.js', __FILE__) , array(), '1.0.0', true );
			wp_enqueue_script( 'dojo_google_conversion', '//www.googleadservices.com/pagead/conversion.js' , array('dojo_google_track'), '1.0.0', true );

		}




	}


	function save_tracking_settings(){

		// delete_option ("wldojo_settings");

		// var_dump ($_POST);

		/* Process Settings */

		if ($_POST['wldojo_action']  &&  check_admin_referer('wldojo-notice')) {


			$settings = get_option( "wldojo_settings" );

			$action = $_POST['wldojo_action'];

			if ($action=='I Will Pass') {

				$settings['tracking_notice'] = 1;
				$settings['allow_tracking'] = 0;
				$updated = update_option( "wldojo_settings", $settings );
				$action = "disalllow_tracking";
			}

			if ($action=='Receive Discount Coupon') {

				$settings['tracking_notice'] = 1;
				$settings['allow_tracking'] = 1;
				$updated = update_option( "wldojo_settings", $settings );
				$action = "alllow_tracking";

			}

			$args = array ('email' => $_POST['email'],
						   'admin_email'=> get_bloginfo('admin_email'),
						   'website_url'=>  get_bloginfo( 'url' ),
						   'action' => $action,
						   'website_url'=> get_bloginfo('url'),
						   'website_ip' => $_SERVER['SERVER_ADDR'],
						   'user_ip' =>  $_SERVER['REMOTE_ADDR']
			);


			$query = http_build_query ($args);
			$url = 'http://downloads.wishlistdojo.com/allow_tracking.php?'.$query;
			$response = wp_remote_get( 'http://downloads.wishlistdojo.com/allow_tracking.php?'.$query);


		}

	}



	public function admin_notice() {

		/* Get admin Email */
		$admin_email = get_option( 'admin_email');




		// global $edd_options;

		$settings = get_option( "wldojo_settings" );

		$hide_notice = $settings[ 'tracking_notice' ];

		if( $hide_notice )
			return;

		if( isset( $settings['allow_tracking'] ) )
			return;

		if( ! current_user_can( 'manage_options' ) )
			return;

		if(
			stristr( network_site_url( '/' ), 'dev'       ) !== false ||
			stristr( network_site_url( '/' ), 'localhost' ) !== false ||
			stristr( network_site_url( '/' ), ':8888'     ) !== false // This is common with MAMP on OS X
		) {

		} else {


			echo '<div class="updated"><p>';
			echo __ ('<strong>Get a Special Offer from Wishlist Dojo</strong><br>','wldojo');
			echo __ ('Allow us to notify you about new Wishlist Member plugins.<br>

Opt-in to our newsletter and tracking and immediately <strong>receive 10% discount coupon</strong> to shop on <a href="https://happyplugins.com" target="_blank">HappyPlugins store</a>.<br><em>
No sensitive data is tracked</em>.', 'wldojo');
			?>
			<br><br>
			<form method="post">
			<input type="text" name="email" value="<?php echo $admin_email; ?>">
			<input type="submit" class="button-primary" name="wldojo_action" value="<?php _e( 'Receive Discount Coupon', 'wldojo' ); ?>">
			<input type="submit" class="button-secondary" name="wldojo_action" value="<?php _e( "I Will Pass", 'wldojo' ); ?>">
			<?php wp_nonce_field( "wldojo-notice" ); ?>
			</form>
			</p></div>
			<?php
		}


	}

}
$WishlistDojo_Tracking = new WishlistDojo_Tracking;
