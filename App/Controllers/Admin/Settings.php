<?php
/**
 * Add settings sub menu and page into the Settings menu.
 * 
 * Original source last update: 2026-04-11
 * 
 * @package okv-oauth
 */


namespace OKVOauth\App\Controllers\Admin;


if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('\\OKVOauth\\App\\Controllers\\Admin\\Settings')) {
    /**
     * Admin settings page.
     */
    class Settings implements \OKVOauth\App\Controllers\ControllerInterface
    {


        use \OKVOauth\App\AppTrait;


        /**
         * @var string Settings menu slug. This constant must be public.
         */
        const MENU_SLUG = 'okv-oauth-settings';


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
            $hook_suffix = add_options_page(__('Rundiz OAuth settings', 'okv-oauth'), __('Rundiz OAuth', 'okv-oauth'), 'manage_options', static::MENU_SLUG, [$this, 'pluginSettingsPage']);
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
            $config_values = $this->getLoader()->loadConfig();
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

            $this->getLoader()->loadView('Admin/settings_v', $output);
            unset($output);
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

            $config_values = $this->getLoader()->loadConfig();
            if (is_array($config_values) && array_key_exists('rundiz_settings_config_file', $config_values)) {
                $settings_config_file = $config_values['rundiz_settings_config_file'];
                $RundizSettings = new \OKVOauth\App\Libraries\RundizSettings();
                $RundizSettings->settings_config_file = $settings_config_file;
                $hasEditorField = $RundizSettings->hasEditor();
                $hasMediaField = $RundizSettings->hasMedia();
                unset($RundizSettings, $settings_config_file);
            }
            unset($config_values);

            if (isset($hasEditorField) && true === $hasEditorField) {
                // if there is editor field (TinyMCE).
                // the function call `wp_enqueue_editor()` is required to make tabs 'visual/code' works.
                // the media assets will be enqueue automatically.
                wp_enqueue_editor();
            }
            unset($hasEditorField);
            if (isset($hasMediaField) && true === $hasMediaField) {
                // if there is media field. 
                // the function call `wp_enqueue_media()` is required 
                // in case there is no function call to `wp_enqueue_editor()` 
                // to make sure that JS `wp.media()` will work.
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
