<?php
/*
Plugin Name: PayHere WC_API Tester
Plugin URI: https://www.payhere.lk
Description: Tests a Wordpress Website's ability to receive a WC_API callback.
Version: 0.0.1
Author: PayHere (Private) Limited
Author URI: https://www.payhere.lk
*/

add_action('plugins_loaded', 'woocommerce_payhere_wc_api_tester_init', 0);

function woocommerce_payhere_wc_api_tester_init()
{
    /**
     * Gateway class
     */
    class WC_PayHere_WC_API_Tester
    {

        /**
         * Make __construct()
         **/
        public function __construct()
        {
            add_action('init', array(&$this, 'check_params'));
            add_action('woocommerce_api_' . strtolower(get_class($this)), array($this, 'check_params')); //update for woocommerce >2.0
            // add_action('woocommerce_gateway_icon', array($this, 'modify_gateway_icon_css'), 10, 2);
            // add_action('admin_enqueue_scripts', array($this, 'payhere_enqueue_admin_scripts'));
            // add_action('wp_enqueue_scripts', array($this, 'payhere_enqueue_scripts'));

            add_action('admin_init', array($this, 'phwat_settings_init'));
            add_action('admin_menu', array($this, 'phwat_options_page'));
        }

        public function phwat_settings_init()
        {
            // register a new setting for "phwat" page
            register_setting('phwat', 'phwat_options');

            add_settings_section(
                'phwat_section',
                'General Settings',
                array($this, 'phwat_section_cb'),
                'phwat'
            );

            // register a new field in the "wporg_section_developers" section, inside the "wporg" page
            add_settings_field(
                'phwat_enabled', // as of WP 4.6 this value is used only internally
                'Enabled',
                array($this, 'phwat_field_enabled_cb'),
                'phwat',
                'phwat_section',
                [
                    'label_for' => 'phwat_field_enabled',
                    'class' => 'phwat_row',
                ]
            );

            add_settings_field(
                'phwat_request_bin', // as of WP 4.6 this value is used only internally
                'Request Bin',
                array($this, 'phwat_field_req_bin_cb'),
                'phwat',
                'phwat_section',
                [
                    'label_for' => 'phwat_field_request_bin',
                    'class' => 'phwat_row',
                ]
            );
        }

        public function phwat_section_cb(){

            echo '<h5>Configure the settings for the plugin to work properly.</h5>';

            $base_url = path_join(site_url(), 'wc-api/' . strtolower(get_class($this)));
            $callback_url = add_query_arg(array(
                'secret' => 'allow'
            ), $base_url);

            $callback_url_2 = add_query_arg(array(
                'wc-api' => strtolower(get_class($this)),
                'secret' => 'allow'
            ), site_url());

            echo sprintf('<h5>Site URL (POST): <a href="%s">%s</a></h5>', $callback_url, $callback_url);
            echo sprintf('<h5>Site URL (POST): <a href="%s">%s</a></h5>', $callback_url_2, $callback_url_2);
        }

        public function phwat_field_enabled_cb($args)
        {
            $options = get_option('phwat_options');
            $element_id = $args['label_for'];
            $checked = isset($options[$element_id]) ? checked($options[$element_id], 'yes', false) : '';
            
            ?>

            <label for="<?php echo esc_attr($element_id) ?>">
                Enable The Plugin
                <input type="hidden" id="<?php echo esc_attr($element_id) ?>" name="<?php echo "phwat_options[$element_id]"?>" value="no">
                <input type="checkbox" id="<?php echo esc_attr($element_id) ?>" name="<?php echo "phwat_options[$element_id]"?>" value="yes" <?php echo $checked ?>>
            </label>

            <?php
        }

        public function phwat_field_req_bin_cb($args)
        {
            $options = get_option('phwat_options');
            $element_id = $args['label_for'];
            $bin = isset($options[$element_id]) ? $options[$element_id] : '';
            
            ?>

            <label for="<?php echo esc_attr($element_id) ?>">
                Request Bin
                <input type="text" id="<?php echo esc_attr($element_id) ?>" name="<?php echo "phwat_options[$element_id]"?>" value="<?php echo $bin?>">
            </label>

            <?php
        }
        /**
         * top level menu
         */
        public function phwat_options_page()
        {
            // add top level menu page
            add_menu_page(
                'PayHere WC Api Tester',
                'PayHere WC Api Tester Options',
                'manage_options',
                'phwat',
                array($this, 'phwat_options_page_html')
            );
        }

        /**
         * top level menu:
         * callback functions
         */
        public function phwat_options_page_html()
        {
            // check user capabilities
            if (!current_user_can('manage_options')) {
                return;
            }

            // add error/update messages

            // check if the user have submitted the settings
            // wordpress will add the "settings-updated" $_GET parameter to the url
            if (isset($_GET['settings-updated'])) {
                // add settings saved message with the class of "updated"
                add_settings_error('phwat_messages', 'phwat_message', __('Settings Saved', 'phwat'), 'updated');
            }

            // show error/update messages
            settings_errors('phwat_messages');
        ?>
            <div class="wrap">
                <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
                <form action="options.php" method="post">
                    <?php
                    // output security fields for the registered setting "wporg"
                    settings_fields('phwat');
                    // output setting sections and their fields
                    // (sections are registered for "wporg", each field is registered to a specific section)
                    do_settings_sections('phwat');
                    // output save settings button
                    submit_button('Save Settings');
                    ?>
                </form>
            </div>
        <?php
        }

        public function check_params()
        {
            if (!isset($_GET['secret']) || $_GET['secret'] != 'allow'){
                return;
            }

            echo '<pre>Check Params Reached</pre>';

            $options = get_option('phwat_options');

            if (empty($options)){
                return;
            }

            if (empty($options['phwat_field_enabled'])){
                return;
            }

            $enabled = $options['phwat_field_enabled'];

            if ($enabled === 'yes') {

                if (empty($options['phwat_field_request_bin'])){
                    return;
                }

                $request_bin_url = $options['phwat_field_request_bin'];

                $handle = curl_init($request_bin_url);

                $data = array_merge($_REQUEST, $_SERVER);

                $encodedData = json_encode($data);

                curl_setopt($handle, CURLOPT_POST, 1);
                curl_setopt($handle, CURLOPT_POSTFIELDS, $encodedData);
                curl_setopt($handle, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

                $result = curl_exec($handle);
            }
        }

        private function convert_timestamp_readable($ts)
        {
            $dt = new DateTime("@$ts");
            $format = 'Y-m-d H:i:s e';
            return $dt->format($format);
        }
    }

    $_temp = new WC_PayHere_WC_API_Tester();
}

/**
 * 'Settings' link on plugin page
 **/
add_filter('plugin_action_links', 'payhere_wc_api_tester_add_action_plugin', 10, 5);
function payhere_wc_api_tester_add_action_plugin($actions, $plugin_file)
{
    static $plugin;

    if (!isset($plugin))
        $plugin = plugin_basename(__FILE__);
    if ($plugin == $plugin_file) {
        $settings = array('settings' => '<a href="admin.php?page=phwat">' . __('Settings') . '</a>');
        $actions = array_merge($settings, $actions);
    }

    return $actions;
}
