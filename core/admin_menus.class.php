<?php

class WishlistDojo_admin_menus {

    var $main_menu_name;
    var $main_menu_slug;
    var $main_menu_position;

    var $_pluginName;
    var $_pluginSlug;
    var $_menuName;
    var $license;

    var $plugin_parameters;


    function __construct ($plugin_parameters){

        $this->_pluginName = $plugin_parameters->plugin_name;
        $this->_menuName =  $plugin_parameters->menu_name;
        $this->_pluginSlug = $plugin_parameters->plugin_slug;

        $this->main_menu_name = $plugin_parameters->main_menu_name;
        $this->main_menu_slug = $plugin_parameters->main_menu_slug;
        $this->main_menu_position = $plugin_parameters->main_menu_position;

         // var_dump( $this->main_menu_position);

        $this->plugin_parameters = $plugin_parameters;


        // Add Menus

        add_action('admin_menu', array ($this, 'admin_menu'));
        add_action('admin_init' , array ($this, 'save_settings'));


        add_action('wp_print_scripts', array($this,'scripts'));


        // Set Default Settings
        $this->setDefaultSettings();




    }

    function setDefaultSettings (){




        // Set default setting to display footer

        //delete_option ('wlppp_settings');

        $settings = get_option('wlppp_settings');

        if (!isset ($settings['wlppp_affiliate'])) {
            $settings['wlppp_affiliate'] = 'yes';
            $updated = update_option( "wlppp_settings", $settings );
        }


        if (!isset ($settings['wlppp_not_logged_content'])) {
            $settings['wlppp_not_logged_content'] = 'You are currently not logged in to the site.<br/>If you are a member please log in to view your most recent purchase.';
            $updated = update_option( "wlppp_settings", $settings );
        }


        if (!isset ($settings['wlppp_not_purchased_content'])) {
            $settings['wlppp_not_purchased_content'] = 'You haven\'t bought any guides yet. Please visit our guides\' section and check out all the premium guides that we offer.';
            $updated = update_option( "wlppp_settings", $settings );
        }

    }



    function scripts () {

        wp_register_style( 'wlm_cb_notify_style', plugins_url('/css/style.css', __FILE__));
        wp_enqueue_style( 'wlm_cb_notify_style' );

        wp_register_style( 'dojo_other_products_tab', plugins_url('/css/other_products_tab.css', __FILE__));
        wp_enqueue_style( 'dojo_other_products_tab' );

        wp_enqueue_script( 'dojo_mixitup', plugins_url('/js/jquery.mixitup.min.js', __FILE__) , array('jquery'), '1.0.0', true );
        wp_enqueue_script( 'dojo_install_plugins', plugins_url('/js/install_plugins.js', __FILE__) , array('jquery' , 'dojo_mixitup'), '1.0.0', true );

    }

    function admin_menu (){

        /* Add Menus items */

        if (!$this->find_my_menu_item($this->main_menu_slug)){

       //  add_menu_page(  $this->main_menu_name,  $this->main_menu_name, 'create_users',   $this->main_menu_slug,  array ($this,'dashboard'), plugins_url( 'images/menu_icon.png' , __FILE__ ) , $this->main_menu_position);



        }


        add_menu_page(  $this->_menuName, $this->_menuName , 'create_users',   $this->_pluginSlug ,  array ($this,'tabs_pages'), plugins_url( 'images/menu_icon.png' , __FILE__ ) , $this->main_menu_position);

		add_submenu_page(  $this->main_menu_slug, $this->_menuName, $this->_menuName ,'create_users', $this->_pluginSlug, array ($this,'tabs_pages'));
		add_submenu_page(  $this->main_menu_slug, "Dashboard" ,  "Dashboard" , 'create_users', "wishlist-dojo&tab=dashboard" , array ($this,'tabs_pages'));
		add_submenu_page(  $this->main_menu_slug, "Settings" ,   "Settings" , 'create_users', "wishlist-dojo&tab=settings" , array ($this,'tabs_pages'));
		add_submenu_page(  $this->main_menu_slug, "Install Plugins" ,  "Install Plugins" , 'create_users', "wishlist-dojo&tab=install-products" , array ($this,'tabs_pages'));
		add_submenu_page(  $this->main_menu_slug, "News & Guides" ,  "News & Guides" , 'create_users', "wishlist-dojo&tab=news" , array ($this,'tabs_pages'));
		add_submenu_page(  $this->main_menu_slug, "Custom Development" ,  "Custom Development" , 'create_users', "wishlist-dojo&tab=custom-development" , array ($this,'tabs_pages'));



        remove_submenu_page( $this->main_menu_slug, $this->main_menu_slug);


    }


    function tabs_pages() {

        // Check if license is active


        global $pagenow;

        if ( isset ( $_GET['tab'] ) ) $this->create_admin_tabs($_GET['tab']); else $this->create_admin_tabs('dashboard');
        if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab'];  else $tab = 'dashboard';

        switch ( $tab ){

            case 'dashboard' :
                $this->dashboard_page();
                break;

            case 'settings' :
                $this->settings_page();
                break;

            case 'support' :
                $this->support_page();
                break;

            case 'install-products' :
                $this->other_products();
                break;
			case 'news' :
				$this->news_page();
				break;
			case 'custom-development' :
				$this->development_page();
				break;
            default:
                $this->dashboard_page();
                break;


        }

        }

    function create_admin_tabs( $current = 'homepage' ) {

        $tabs = array( 'dashboard' => 'Dashboard', 'settings'=>'Settings' , 'install-products'=>'Install Plugins', 'news'=>'Guides & News' , 'custom-development'=> "Custom Development" ,  /* 'support'=>'Support' */);

        echo '<div id="icon-options" class="icon32"><br></div>';
        ?>

        <h2 class="nav-tab-wrapper" style="background: url(<?php echo plugins_url( 'images/tabs_icon_32x32.png' , __FILE__ );?>) no-repeat 0px 6px; padding-bottom: 5px;
            padding-left: 38px; padding-top: 5px;">

        <?php
        foreach( $tabs as $tab => $name ){
            $class = ( $tab == $current ) ? ' nav-tab-active' : '';
            echo "<a class='nav-tab$class' href='?page=$this->_pluginSlug&tab=$tab'>$name</a>";
        }

        echo '</h2>';

    }

    function dashboard_page(){

        require(dirname(__FILE__) . '/includes/init_lcs.php');


        ?>
        <div class="wrap">
        <h2><?echo $this->_pluginName; ?> - Dashboard</h2>

        <div style="width:70%;  float:left;">



            <?php  /* Dashboard Text */  require(dirname(__FILE__) . '/templates/dashboard_intro.tmp.php');   ?>

            <hr>

            </div>

        <div style="border: 1px solid #cdcdcd; padding: 18px; width: 25%; margin-left: 12px; float:left;">

            <?php
            $utm_product_name = str_replace(' ', '', $this->plugin_parameters->edd_item_name);
            $utm = "?utm_source=plugin&utm_medium=dashboard-tab&utm_term=".$utm_product_name."&utm_content=".$utm_product_name."&utm_campaign=sidebar";
            ?>


            <?php  require(dirname(__FILE__) . '/templates/happyplugins_menu_sidebar.tmp.php'); ?>
        </div>
    <?php

    }


    function settings_page (){

        /*
         * Settings screen includes the following options:
         * when to send the email when a member cancel or when a scription ended or both
         * email address to send the notification to
         */


        $settings = get_option( "wldojo_settings" );
        $wlm_license_key = $settings['wlm_license_key'];
        $wlm_license_email = $settings['wlm_license_email'];

       ?>
        <div class="wrap">

           <h2><?php echo $this->_pluginName; ?> - Settings</h2>

        <div style="width:70%;  float:left; margin-right:13px;">




            <form method="post">
                <?php wp_nonce_field( "wldojo-settings-page" ); ?>

                <h3>Wishlist Member License Information</h3>
                <p>To save time you can enter your Wishlist Member license information, in this way you will not need to retype the license information every time you want to download & install one of Wishlist Products plugins.</p>

				<p>You can find all the plugins in <a href="?page=wishlist-dojo&tab=install-products">Install Plugins</a> tab.</p>

                <table>
                    <tr>
                        <td>Wishlist Member License Key:&nbsp;&nbsp;&nbsp;</td><td><input name="wlm_license_key" type="text" id="wlm_license_key" value="<?php echo $wlm_license_key;?>"></td>
                    </tr>
                    <tr>
                        <td>
                          Wishlist Member Licence Email:&nbsp;&nbsp;&nbsp;</td><td><input name="wlm_license_email" type="text" id="wlm_license_key" value="<?php echo $wlm_license_email;?>"></td>
                        </td>
                    </tr>

                </table>


                <br/><br/>

                <input type="hidden" name="wldojo_messages_settings" value="e4rrsw#" />
                <input class="button-primary" type="submit" value="Save Settings"/>

            </form>

        </div>

        <div style="border: 1px solid #cdcdcd; padding: 18px; width: 25%; float:left;">

            <?php
            $utm_product_name = str_replace(' ', '', $this->plugin_parameters->edd_item_name);
            $utm = "?utm_source=plugin&utm_medium=settings-tab&utm_term=".$utm_product_name."&utm_content=".$utm_product_name."&utm_campaign=sidebar";
            ?>


            <?php  require(dirname(__FILE__) . '/templates/happyplugins_menu_sidebar.tmp.php'); ?>
        </div>
        <div>

            <?php // echo  file_get_contents("http://google.com"); ?>


        </div>
    <?php

    }

    function support_page(){
        ?>

        <div class="wrap">


            <h2><?php echo $this->_pluginName; ?> - Support</h2>
            <?php  /* Support Intro */  require(dirname(__FILE__) . '/templates/support_intro.tmp.php');   ?>

            <div style="width:70%;  float:left;">
                <?php

                require(dirname(__FILE__) . '/includes/system_info.php');
                $hp_system_info = new hp_system_info();
                $hp_system_info->display_system_info($this->plugin_parameters);

                ?>
            </div>
            <div style="width: 23%; margin-left: 25px; float:left;">
                <?php  /* Support Sidebar */  require(dirname(__FILE__) . '/templates/support_sidebar.tmp.php');   ?>
            </div>
            <div class="clear"></div>
            <hr>
            <div>
                <?php  /* Support Bottom */  require(dirname(__FILE__) . '/templates/support_bottom.tmp.php');   ?>
            </div>

        </div>
<?php

    }

    function other_products(){
        ?>


        <div class="wrap">

           <h2><?php echo $this->_pluginName; ?> - Install Plugins</h2>

            <?php  /* Other Products */  require(dirname(__FILE__) . '/templates/other_products.tmp.php');   ?>

        </div>

    <?php
    }

	function news_page(){
		?>
		<div class="wrap">

			<h2><?php echo $this->_pluginName; ?> - Guides & News</h2>

			<div style="width:70%;  float:left;">

				<?php
				$utm_product_name = str_replace(' ', '', $this->plugin_parameters->edd_item_name);
				$utm = "?utm_source=plugin&utm_medium=news-tab&utm_term=".$utm_product_name."&utm_content=".$utm_product_name."&utm_campaign=news-main";
				 /* Other Products */   require(dirname(__FILE__) . '/templates/news.tmp.php');
				?>

			</div>

			<div style="border: 1px solid #cdcdcd; padding: 18px; width: 25%; margin-left: 12px; float:left;">

				<?php
				$utm_product_name = str_replace(' ', '', $this->plugin_parameters->edd_item_name);
				$utm = "?utm_source=plugin&utm_medium=news-tab&utm_term=".$utm_product_name."&utm_content=".$utm_product_name."&utm_campaign=main";
				?>


				<?php  require(dirname(__FILE__) . '/templates/happyplugins_menu_sidebar.tmp.php'); ?>
			</div>



		</div>

	<?php



	}


	function development_page(){
		?>
		<div class="wrap">

			<h2><?php echo $this->_pluginName; ?> - Custom Development</h2>

		<div style="width:70%;  float:left;">

			<?php
			$utm_product_name = str_replace(' ', '', $this->plugin_parameters->edd_item_name);
			$utm = "?utm_source=plugin&utm_medium=custom-development-tab&utm_term=".$utm_product_name."&utm_content=".$utm_product_name."&utm_campaign=dashboard-main";
			/* Other Products */   require(dirname(__FILE__) . '/templates/custom_development.tmp.php');
			?>

			</div>

			<div style="border: 1px solid #cdcdcd; padding: 18px; width: 25%; margin-left: 12px; float:left;">

				<?php
				$utm_product_name = str_replace(' ', '', $this->plugin_parameters->edd_item_name);
				$utm = "?utm_source=plugin&utm_medium=custom-development-tab&utm_term=".$utm_product_name."&utm_content=".$utm_product_name."&utm_campaign=main";
				?>


				<?php  require(dirname(__FILE__) . '/templates/happyplugins_menu_sidebar.tmp.php'); ?>
			</div>

		</div>

	<?php



	}

    function save_settings (){



//		if ( !empty( $_POST ) && check_admin_referer( 'install_plugins','wldojo-install-plugins-page' ) ) {
//
//
//				require(dirname(__FILE__) . '/wishlist-dojo.class.php');
//				$wishlist_dojo = new WishlistDojo($this->plugin_parameters);
//				$wishlist_dojo->installPlugin("http://downloads.wordpress.org/plugin/theme-check.20131213.1.zip");
//
//
//
//
//
//		}


        $settings = get_option( "wldojo_settings" );

        if ($_POST['wldojo_messages_settings']=='e4rrsw#') {

            check_admin_referer('wldojo-settings-page');
            $settings['wlm_license_key'] = $_POST ['wlm_license_key'];
            $settings['wlm_license_email'] = $_POST ['wlm_license_email'];

            $updated = update_option( "wldojo_settings", $settings );

        }

    }

    function find_my_menu_item($handle, $sub = false) {
        if(!is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) {
            return false;
        }
        global $menu, $submenu;
        $check_menu = $sub ? $submenu : $menu;
        if(empty($check_menu)) {
            return false;
        }
        foreach($check_menu as $k => $item) {
            if($sub) {
                foreach($item as $sm) {
                    if($handle == $sm[2]) {
                        return true;
                    }
                }
            }
            else {
                if($handle == $item[2]) {
                    return true;
                }
            }
        }
        return false;
    }


}