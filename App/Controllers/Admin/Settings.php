<?php
/**
 * Add settings sub menu and page into the Settings menu.
 *
 * Last update: 2026-03-27
 * 
 * @package okv-oauth
 */


namespace OKVOauth\App\Controllers\Admin;


if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('\\OKVOauth\\App\\Controllers\\Settings')) {
    /**
     * Settings class.
     */
    class Settings implements \OKVOauth\App\Controllers\ControllerInterface
    {


        use \OKVOauth\App\AppTrait;


        /**
         * @var string Setting menu slug. This constant must be public.
         */
        const SETTING_MENU_SLUG = 'okv-oauth-settings';


        /**
         * @var string The current admin page.
         */
        private $hookSuffix = '';


        /**
         * Allow code/WordPress to call hook `admin_enqueue_scripts` 
         * then `wp_register_script()`, `wp_localize_script()`, `wp_enqueue_script()` functions will be working fine later.
         * 
         * @link https://wordpress.stackexchange.com/a/76420/41315 Original source code.
         * @since 2025-10-14
         */
        public function callEnqueueHook()
        {
            add_action('admin_enqueue_scripts', [$this, 'registerScripts']);
        }// callEnqueueHook


        /**
         * The plugin settings sub menu to go to settings page.
         */
        public function pluginSettingsMenu()
        {
            $hook_suffix = add_options_page(__('Rundiz OAuth settings', 'okv-oauth'), __('Rundiz OAuth', 'okv-oauth'), 'manage_options', static::SETTING_MENU_SLUG, [$this, 'pluginSettingsPage']);
            if (is_string($hook_suffix)) {
                $this->hookSuffix = $hook_suffix;
                add_action('load-' . $hook_suffix, [$this, 'callEnqueueHook']);
            }
            unset($hook_suffix);
        }// pluginSettingsMenu


        /**
         * Display plugin settings page.
         */
        public function pluginSettingsPage()
        {
            // check permission.
            if (!current_user_can('manage_options')) {
                wp_die(esc_html__('You do not have permission to access this page.', 'okv-oauth'));
            }

            // load config values to get settings config file.
            $Loader = new \OKVOauth\App\Libraries\Loader();
            $config_values = $Loader->loadConfig();
            if (is_array($config_values) && array_key_exists('rundiz_settings_config_file', $config_values)) {
                $settings_config_file = $config_values['rundiz_settings_config_file'];
            } else {
                wp_die(esc_html__('Settings configuration file was not set.', 'okv-oauth'));
                exit(1);
            }
            unset($config_values);

            $RundizSettings = new \OKVOauth\App\Libraries\RundizSettings();
            $RundizSettings->settings_config_file = $settings_config_file;

            $options_values = $this->getOptions();
            $output = [];

            // if form submitted
            if (isset($_POST) && !empty($_POST)) {
                $wpnonce = '';
                if (isset($_POST['_wpnonce'])) {
                    $wpnonce = sanitize_text_field(wp_unslash($_POST['_wpnonce']));
                }

                if (!wp_verify_nonce($wpnonce)) {
                    wp_nonce_ays('-1');
                }
                unset($wpnonce);

                // populate form field values.
                $options_values = $RundizSettings->getSubmittedData();

                // you may validate form here first.
                // then save data.
                $output['save_result'] = $this->saveOptions($options_values);

                $output['form_result_class'] = 'notice-success';
                $output['form_result_msg'] = __('Settings saved.', 'okv-oauth');
            }// endif $_POST

            $output['settings_page'] = $RundizSettings->getSettingsPage($options_values);
            unset($RundizSettings, $options_values);

            $Loader->loadView('admin/settings_v', $output);
            unset($Loader, $output);
        }// pluginSettingsPage


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            add_action('admin_menu', [$this, 'pluginSettingsMenu']);
        }// registerHooks


        /**
         * Enqueue scripts and styles here.
         * 
         * @param string $hook_suffix The current admin page.
         */
        public function registerScripts($hook_suffix = '')
        {
            if ($hook_suffix !== $this->hookSuffix) {
                return;
            }

            $Loader = new \OKVOauth\App\Libraries\Loader();
            $config_values = $Loader->loadConfig();
            if (is_array($config_values) && array_key_exists('rundiz_settings_config_file', $config_values)) {
                $settings_config_file = $config_values['rundiz_settings_config_file'];
                $RundizSettings = new \OKVOauth\App\Libraries\RundizSettings();
                $RundizSettings->settings_config_file = $settings_config_file;
                $hasEditorField = $RundizSettings->hasEditor();
                $hasMediaField = $RundizSettings->hasMedia();
                unset($RundizSettings, $settings_config_file);
            }
            unset($config_values, $Loader);

            if (isset($hasEditorField) && true === $hasEditorField) {
                wp_enqueue_editor();
                wp_enqueue_media();
            }
            unset($hasEditorField);
            if (isset($hasMediaField) && true === $hasMediaField) {
                wp_enqueue_script('jquery');
                wp_enqueue_media();
            }
            unset($hasMediaField);

            wp_enqueue_style('okv-oauth-font-awesome6');

            wp_enqueue_style('okv-oauth-rd-settings-customstyle-css');

            wp_enqueue_style('okv-oauth-rd-settings-tabs-css');
            wp_enqueue_script('okv-oauth-rd-settings-tabs-js');
        }// registerScripts


    }// Settings
}
