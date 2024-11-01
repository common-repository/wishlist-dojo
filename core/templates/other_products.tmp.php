<?php

if ( !empty( $_POST ) && check_admin_referer( 'install_plugins','wldojo-install-plugins-page' ) ) {

//	echo "<pre>";
//	print_r ($_POST);
//	echo "</pre>";


	echo "<h3>Installing ".$_POST['plugin_name']."</h3>";

	require(dirname(__FILE__) . '/../wishlist-dojo.class.php');
	$wishlist_dojo = new WishlistDojo($this->plugin_parameters);



	$sku = $_POST['sku'];
	$item_name = $_POST['plugin_name'];
	$action = "install";
	$website_url = get_bloginfo('url');
	$website_ip = $_SERVER['SERVER_ADDR'];
	$user_ip =  $_SERVER['REMOTE_ADDR'];
	$admin_email = get_bloginfo('admin_email');

	if ($_POST['vendor'] == "WishlistProducts") $vendor = 'wishlistmember';
	if ($_POST['vendor'] == "HappyPlugins") $vendor = 'happyplugins';

	// echo "vendor: $vendor";

	switch ($vendor) {

		case 'wishlistmember':
			$license_info ['key'] = $_POST['wlm_license_key'];
			$license_info ['email'] = $_POST['wlm_license_email'];

		break;

		case 'happyplugins':

			$license_info ['key'] = $_POST['hp_license_key'];
			break;


	}

	$link = $wishlist_dojo->createDownloadLink($action, $sku, $item_name , $vendor, $website_url,  $website_ip, $user_ip, $admin_email,  $license_info);



	$wishlist_dojo->installPlugin("$link");
	echo '<a href="?page=wishlist-dojo&tab=install-products">Return to Install Plugins Page</a>';

} else {

/* Getting Wishlist Member License Key */

$settings = get_option( "wldojo_settings");
if ($settings) {

	$wlm_license_key =  $settings['wlm_license_key']!='' ? $settings['wlm_license_key'] : '' ;
	$wlm_license_email =  $settings['wlm_license_email']!='' ? $settings['wlm_license_email'] : '' ;

}

$installed_plugins = get_plugins();

foreach ($installed_plugins as $installed_plugin) {
    $plugins_names[] = $installed_plugin['Name'];
}

$url = "http://downloads.wishlistdojo.com/products_xml.php";
// $url = "http://wishlistdojo.s3.amazonaws.com/wl-dojo-plugins.xml";

	$file = wp_remote_retrieve_body (wp_remote_get ($url));
$plugins = simplexml_load_string ($file);

//$plugins = new SimpleXMLElement('https://wishlistdojo.s3.amazonaws.com/wl-dojo-plugins.xml', null, true);
// Get All Plugins Tags

$filter_tags= array();

foreach ($plugins as $plugin ) {
    $xml_product_tags = $plugin->tags->CDATA;
    $xml_product_tags = explode (',,', $xml_product_tags );
    foreach ($xml_product_tags as $xml_product_tag ) {
        if (!in_array($xml_product_tag,$filter_tags)) {
            $xml_product_tag_name =ucwords ( str_replace ('-', ' ',$xml_product_tag ));
            $filter_tags[$xml_product_tag] =  $xml_product_tag_name;
        }
    }
}
?>

<h3 style="padding-top: 16px; margin-right: 10px; float:left;">Filter:</h3>
<div class="menu-categories-container"><ul>
        <li class="filter" data-filter="all">Show All</li>
<?php foreach ($filter_tags as $filter_key=>$filter_tag) {
    $filter_key = str_replace('-','_',$filter_key);

    ?>
    <li class="filter" data-filter="<?php  echo  "filter_$filter_key"; ?>"><?php echo $filter_tag; ?></li>

 <?php } ?>
		<li class="filter" data-filter="filter_not_installed"">Not Installed</li>
</ul>

</div>
<div class="clear"></div>
<hr>

<ul id="wlm_plugins">
<?php

// $plugins = new SimpleXMLElement('http://happyplugins-resources.s3.amazonaws.com/products_xml/hp_plugins.xml', null, true);

// echo $this->plugin_parameters->product_tags;

$product_tags = explode (',,', $this->plugin_parameters->product_tags );
$utm_product_name = str_replace(' ', '', $this->plugin_parameters->edd_item_name);

//var_dump ($product_tags);


/* Display Plugins Grid */

foreach($plugins as $plugin)

{
    $product_no_spaces = str_replace(' ', '', $plugin->name);
    $vendor = $plugin->vendor;

    // $description = substr($plugin->description,0,78)."...";
    $description = $plugin->description->CDATA;
    $utm = "?utm_source=plugin&utm_medium=related-products-tab&utm_term=".$utm_product_name."&utm_content=".$utm_product_name."&utm_campaign=".$product_no_spaces;
    $link = $plugin->buylink.$utm;

    $xml_product_tags = $plugin->tags->CDATA;
    $xml_product_tags = explode (',,', $xml_product_tags );

//    echo $description;
//    echo "<br>";
    // Check if this product is related

//    $display = null;
//
//    foreach ($product_tags as $product_tag) {
//        if (in_array($product_tag, $xml_product_tags)) {
//            $display="yes";
//            break;
//        }
//    }
//
//    if ($display!="yes") { continue;}
//    //var_dump ($plugin->description);

?>

<li class="hp-dojo-extension mix<?php


    foreach ($xml_product_tags as $xml_product_tag) {

        $product_tag = str_replace('-','_',$xml_product_tag);
        echo " filter_".$product_tag; }

     if (!in_array($plugin->name, $plugins_names)) { echo " filter_not_installed"; } else { echo " filter_installed"; }

?>">

    <h3 class="hp-dojo-extension-title"><?php echo $plugin->name ?>  <em style="color: #8c8c8c">by <strong><?php echo $vendor;?></strong></em></h3>
    <a href="<?php echo $link ?>" title="<?php echo $plugin->name ?>">

        <img width="180" height="150" src="<?php  echo $plugin->picture ?>" class="attachment-extension wp-post-image" alt="<?php echo $plugin->name ?>" title="<?php  echo $plugin->name ?>"></a>
    <p></p>
    <p><?php echo $description ?></p>
    <p></p>
    <?php if (!in_array($plugin->name, $plugins_names)) { ?>
   <a href="<?php echo $link ?>" title="<?php echo $plugin->name; ?>" class="button-primary">Buy</a>
	<?php if ($plugin->install=="1")  { ?>  <a title="<?php echo $plugin->name ?>" class="button-secondary show_hide">Install</a> <?php } ?>


       <?php if ($vendor=="WishlistProducts") { ?>

        <div class="license_info">Fill license information.
			<form method="post">
            <label for="wlm_license_key">License Key:<input name="wlm_license_key" value="<?php echo $wlm_license_key; ?>"></label>
			<p class="description">Fill in the license key for the product.</p>
			<br>
            <label for="wlm_license_email">License Email:<input name="wlm_license_email" value="<?php echo $wlm_license_email; ?>"></label>
			<p class="description">Fill in the email license for the product.</p>
			<br>
            <input type="submit" class="button-primary" value="Download & Activate"/>
			<input type="hidden" name="sku" value="<?php echo $plugin->sku; ?>"/>
			<input type="hidden" name="plugin_name" value="<?php echo $plugin->storename; ?>"/>
			<input type="hidden" name="vendor" value="<?php echo $plugin->vendor; ?>"/>
			<?php wp_nonce_field( 'install_plugins','wldojo-install-plugins-page' ); ?>

			</form>
            <br><br>
            <a class="license_hide">Close</a>

            <br><br>
        </div>

		   <?php } else { ?>

			<div class="license_info" style="height: 336px;">

				<form method="post">
				<label for="hp_license_key">License Key:<input name="hp_license_key" value=""></label>
				<p class="description">Fill in the license key for the product.</p>
				<br>
				<input type="submit" class="button-primary" value="Download & Activate"/>
				<input type="hidden" name="sku" value="<?php echo $plugin->sku; ?>"/>
				<input type="hidden" name="plugin_name" value="<?php echo $plugin->storename; ?>"/>
				<input type="hidden" name="vendor" value="<?php echo $plugin->vendor; ?>"/>
				<?php wp_nonce_field( 'install_plugins','wldojo-install-plugins-page' ); ?>

				</form>
				<br><br>
				<a class="license_hide">Close</a>
				</div>

		   <?php } ?>


    <?php } else { ?>


        <p class="plugin_installed">Installed</p>

    <?php } ?>
</li>


<?php } // End Products ?>

</ul>

<div class="clear"></div>
<hr>
<h3>Looking for More?</h3>
<?php $utm = "?utm_source=plugin&utm_medium=related-products-tab&utm_term=".$utm_product_name."&utm_content=".$utm_product_name."&utm_campaign=store"; ?>
<p>Interested in more product check out <a href="http://happyplugins.com<?php echo $utm; ?>" target="_blank">HappyPlugins.com store</a>.</p>

<?php  }  ?>


