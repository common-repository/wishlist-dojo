<?php
/**  System Info **/

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

if (!class_exists('hp_system_info')) {

    class hp_system_info
    {
        function display_system_info($plugin_parameters)
        {
            global $wpdb;

            if (!class_exists('Browser'))
                require_once(dirname(__FILE__) . '/libraries/browser.php');

            $browser = new Browser();
            if (get_bloginfo('version') < '3.4') {
                $theme_data = get_theme_data(get_stylesheet_directory() . '/style.css');
                $theme = $theme_data['Name'] . ' ' . $theme_data['Version'];
            } else {
                $theme_data = wp_get_theme();
                $theme = $theme_data->Name . ' ' . $theme_data->Version;
            }

            // Try to identifty the hosting provider
            $host = false;
            if (defined('WPE_APIKEY')) {
                $host = 'WP Engine';
            } elseif (defined('PAGELYBIN')) {
                $host = 'Pagely';
            }
            ?>
            <h3><?php _e('System Information', 'edd'); ?></h3>
            <form action="<?php // echo esc_url( admin_url( 'edit.php?post_type=download&page=edd-system-info' ) ); ?>"
                  method="post" dir="ltr">
                <textarea readonly="readonly" onclick="this.focus();this.select()" id="system-info-textarea"
                          name="hp_sysinfo"
                          title="<?php _e('To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'edd'); ?>">
### Begin System Info ###

## Please include this information when posting support requests ##
<?php do_action('hp_system_info_before'); ?>

Store                     <?php echo $plugin_parameters->edd_store_url; ?><?php echo "\n"; ?>

Multisite:                <?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>

SITE_URL:                 <?php echo site_url() . "\n"; ?>
HOME_URL:                 <?php echo home_url() . "\n"; ?>

Plugin Name:              <?php echo $plugin_parameters->plugin_name; ?><?php echo "\n"; ?>
Plugin Version:           <?php echo $plugin_parameters->plugin_version; ?><?php echo "\n"; ?>
WordPress Version:        <?php echo get_bloginfo('version') . "\n"; ?>
Permalink Structure:      <?php echo get_option('permalink_structure') . "\n"; ?>
Active Theme:             <?php echo $theme . "\n"; ?>
<?php if ($host) : ?>
    Host:                     <?php echo $host . "\n"; ?>
<?php endif; ?>

Registered Post Stati:    <?php echo implode(', ', get_post_stati()) . "\n\n"; ?>
<?php echo $browser; ?>

PHP Version:              <?php echo PHP_VERSION . "\n"; ?>
MySQL Version:            <?php echo mysql_get_server_info() . "\n"; ?>
Web Server Info:          <?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>

PHP Safe Mode:            <?php echo ini_get('safe_mode') ? "Yes" : "No\n"; ?>
PHP Memory Limit:         <?php echo ini_get('memory_limit') . "\n"; ?>
WordPress Memory Limit:   <?php echo WP_MEMORY_LIMIT; ?><?php echo "\n"; ?>
PHP Upload Max Size:      <?php echo ini_get('upload_max_filesize') . "\n"; ?>
PHP Post Max Size:        <?php echo ini_get('post_max_size') . "\n"; ?>
PHP Upload Max Filesize:  <?php echo ini_get('upload_max_filesize') . "\n"; ?>
PHP Time Limit:           <?php echo ini_get('max_execution_time') . "\n"; ?>
PHP Max Input Vars:       <?php echo ini_get('max_input_vars') . "\n"; ?>

WP_DEBUG:                 <?php echo defined('WP_DEBUG') ? WP_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ?>

WP Table Prefix:          <?php echo "Length: " . strlen($wpdb->prefix);
echo " Status:";
if (strlen($wpdb->prefix) > 16) {
    echo " ERROR: Too Long";
} else {
    echo " Acceptable";
}
echo "\n"; ?>

Show On Front:            <?php echo get_option('show_on_front') . "\n" ?>
Page On Front:            <?php $id = get_option('page_on_front');
echo get_the_title($id) . ' (#' . $id . ')' . "\n" ?>
Page For Posts:           <?php $id = get_option('page_for_posts');
echo get_the_title($id) . ' (#' . $id . ')' . "\n" ?>

<?php
$request['cmd'] = '_notify-validate';

$params = array(
    'sslverify' => false,
    'timeout' => 60,
    'user-agent' => 'HP/' . $plugin_parameters->plugin_version,
    'body' => $request
);

$response = wp_remote_post('https://www.paypal.com/cgi-bin/webscr', $params);

if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300) {
    $WP_REMOTE_POST = 'wp_remote_post() works' . "\n";
} else {
    $WP_REMOTE_POST = 'wp_remote_post() does not work' . "\n";
}
?>
WP Remote Post:           <?php echo $WP_REMOTE_POST; ?>

Session:                  <?php echo isset($_SESSION) ? 'Enabled' : 'Disabled'; ?><?php echo "\n"; ?>
Session Name:             <?php echo esc_html(ini_get('session.name')); ?><?php echo "\n"; ?>
Cookie Path:              <?php echo esc_html(ini_get('session.cookie_path')); ?><?php echo "\n"; ?>
Save Path:                <?php echo esc_html(ini_get('session.save_path')); ?><?php echo "\n"; ?>
Use
Cookies:                  <?php echo ini_get('session.use_cookies') ? 'On' : 'Off'; ?><?php echo "\n"; ?>
Use Only
Cookies:                  <?php echo ini_get('session.use_only_cookies') ? 'On' : 'Off'; ?><?php echo "\n"; ?>

DISPLAY
ERRORS:                   <?php echo (ini_get('display_errors')) ? 'On (' . ini_get('display_errors') . ')' : 'N/A'; ?><?php echo "\n"; ?>
FSOCKOPEN:                <?php echo (function_exists('fsockopen')) ? 'Your server supports fsockopen.' : 'Your server does not support fsockopen.'; ?><?php echo "\n"; ?>
cURL:                     <?php echo (function_exists('curl_init')) ? 'Your server supports cURL.' : 'Your server does not support cURL.'; ?><?php echo "\n"; ?>
SOAP
Client:                   <?php echo (class_exists('SoapClient')) ? 'Your server has the SOAP Client enabled.' : 'Your server does not have the SOAP Client enabled.'; ?><?php echo "\n"; ?>
SUHOSIN:                  <?php echo (extension_loaded('suhosin')) ? 'Your server has SUHOSIN installed.' : 'Your server does not have SUHOSIN installed.'; ?><?php echo "\n"; ?>

ACTIVE PLUGINS:

<?php
$plugins = get_plugins();
$active_plugins = get_option('active_plugins', array());

foreach ($plugins as $plugin_path => $plugin) {
    // If the plugin isn't active, don't show it.
    if (!in_array($plugin_path, $active_plugins))
        continue;

    echo $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
}

if (is_multisite()) :
    ?>

    NETWORK ACTIVE PLUGINS:

    <?php
    $plugins = wp_get_active_network_plugins();
    $active_plugins = get_site_option('active_sitewide_plugins', array());

    foreach ($plugins as $plugin_path) {
        $plugin_base = plugin_basename($plugin_path);

        // If the plugin isn't active, don't show it.
        if (!array_key_exists($plugin_base, $active_plugins))
            continue;

        $plugin = get_plugin_data($plugin_path);

        echo $plugin['Name'] . ' :' . $plugin['Version'] . "\n";
    }

endif;

do_action('hp_system_info_after');
?>

### End System Info ###</textarea>
                <!--            <p class="submit">-->
                <input type="hidden" name="edd-action" value="download_sysinfo"/>
                <?php // submit_button( 'Download System Info File', 'primary', 'edd-download-sysinfo', false ); ?>
                <!--            </p>-->
            </form>
            <br>
        <?php
        }

        function hp_let_to_num($v)
        {
            $l = substr($v, -1);
            $ret = substr($v, 0, -1);

            switch (strtoupper($l)) {
                case 'P':
                    $ret *= 1024;
                case 'T':
                    $ret *= 1024;
                case 'G':
                    $ret *= 1024;
                case 'M':
                    $ret *= 1024;
                case 'K':
                    $ret *= 1024;
                    break;
            }

            return $ret;
        }

    }

}