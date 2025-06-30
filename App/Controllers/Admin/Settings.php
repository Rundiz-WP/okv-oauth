<?php
/**
 * Settings class is for add settings menu.
 * 
 * @package rundiz-oauth
 */


namespace RundizOauth\App\Controllers\Admin;

if (!class_exists('\\RundizOauth\\App\\Controllers\\Settings')) {
    class Settings implements \RundizOauth\App\Controllers\ControllerInterface
    {


        use \RundizOauth\App\AppTrait;


        /**
         * setup settings menu to go to settings page.
         */
        public function pluginSettingsMenu()
        {
            $hook_suffix = add_options_page(__('Rundiz OAuth settings', 'okv-oauth'), __('Rundiz OAuth', 'okv-oauth'), 'manage_options', 'rd-oauth-settings', [$this, 'pluginSettingsPage']);
            add_action('load-' . $hook_suffix, [$this, 'registerScripts']);
            unset($hook_suffix);
        }// pluginSettingsMenu


        /**
         * display plugin settings page.
         */
        public function pluginSettingsPage()
        {
            // check permission.
            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have permission to access this page.'));
            }

            // load config values to get settings config file.
            $loader = new \RundizOauth\App\Libraries\Loader();
            $config_values = $loader->loadConfig();
            if (is_array($config_values) && array_key_exists('rundiz_settings_config_file', $config_values)) {
                $settings_config_file = $config_values['rundiz_settings_config_file'];
            } else {
                echo 'Settings configuration file was not set.';
                die('Settings configuration file was not set.');
            }
            unset($config_values, $loader);

            $RundizSettings = new \RundizOauth\App\Libraries\RundizSettings();
            $RundizSettings->settings_config_file = $settings_config_file;

            $options_values = $this->getOptions();

            // if form submitted
            if (isset($_POST) && !empty($_POST)) {
                if (!wp_verify_nonce((isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : ''))) {
                    wp_nonce_ays('-1');
                }

                // populate form field values.
                $options_values = $RundizSettings->getSubmittedData();

                // you may validate form here first.
                // then save data.
                $result = $this->saveOptions($options_values);

                if (true === $result) {
                    $output['form_result_class'] = 'notice-success';
                    $output['form_result_msg'] = __('Settings saved.');
                } else {
                    $output['form_result_class'] = 'notice-success';
                    $output['form_result_msg'] = __('Settings saved.');
                }
            }// endif $_POST

            $output['settings_page'] = $RundizSettings->getSettingsPage($options_values);
            unset($RundizSettings, $options_values);

            $Loader = new \RundizOauth\App\Libraries\Loader();
            $Loader->loadView('admin/settings_v', $output);
            unset($Loader, $output);
        }// pluginSettingsPage


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            if (is_admin()) {
                add_action('admin_menu', [&$this, 'pluginSettingsMenu']);
            }
        }// registerHooks


        /**
         * enqueue scripts and styles here.
         */
        public function registerScripts()
        {
            if (!wp_script_is('rd-oauth-font-awesome6', 'registered')) {
                $StylesAndScripts = new \RundizOauth\App\Libraries\StylesAndScripts();
                $StylesAndScripts->registerStylesAndScripts();
                unset($StylesAndScripts);
            }

            // to name fontawesome handle as `plugin-name-prefix-font-awesome6` is to prevent conflict with other plugins that maybe use older version but same handle that cause some newer icons in this plugin disappears.
            wp_enqueue_style('rd-oauth-font-awesome6');
            wp_enqueue_style('rd-oauth-rd-settings-tabs-css', plugin_dir_url(RUNDIZOAUTH_FILE).'assets/css/rd-settings-tabs.css');
            wp_enqueue_script('rd-oauth-rd-settings-tabs-js', plugin_dir_url(RUNDIZOAUTH_FILE).'assets/js/rd-settings-tabs.js', ['jquery'], false, true);

            wp_enqueue_style('rd-oauth-rd-settings-customstyle-css', plugin_dir_url(RUNDIZOAUTH_FILE) . 'assets/css/rd-settings-customstyle.css');
        }// registerScripts


    }
}