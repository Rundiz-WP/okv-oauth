<?php
/**
 * Hooks into Plugins page.
 * 
 * @package okv-oauth
 * @since 1.7.2
 */


namespace OKVOauth\App\Controllers\Admin;


if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('\\OKVOauth\App\Controllers\Admin\\Plugins')) {
    /**
     * Plugin class that will work on admin list plugins page.
     * 
     * @since 1.7.2
     */
    class Plugins implements \OKVOauth\App\Controllers\ControllerInterface
    {


        use \OKVOauth\App\AppTrait;


        /**
         * Add links to plugin actions area. For example: xxxbefore | Activate | Edit | Delete | xxxafter
         * 
         * @link https://developer.wordpress.org/reference/hooks/plugin_action_links/ Reference.
         * @staticvar string $plugin The plugin file name.
         * @param array $actions An array of plugin action links.
         * @param string $plugin_file Path to the plugin file relative to the plugins directory.
         * @param array $plugin_data An array of plugin data. See `get_plugin_data()` and the `'plugin_row_meta'` filter for the list of possible values.
         * @param string $context The plugin context. By default this can include `'all'`, `'active'`, `'inactive'`, `'recently_activated'`, `'upgrade'`, `'mustuse'`, `'dropins'`, and `'search'`.
         * @return array Return modified links
         */
        public function actionLinks(array $actions, $plugin_file, array $plugin_data, $context = 'all')
        {
            static $plugin;

            if (!isset($plugin)) {
                $plugin = plugin_basename(OKVOAUTH_FILE);
            }

            if ($plugin === $plugin_file) {
                $link = [];
                $link['settings'] = '<a href="' . esc_url(get_admin_url(null, 'options-general.php?page=' . rawurlencode(Settings::MENU_SLUG))) . '">' . __('Settings', 'okv-oauth') . '</a>';
                $actions = array_merge($link, $actions);
                unset($link);
                //$actions['after_actions'] = '<a href="#" onclick="return false;">' . __('After Actions', 'okv-oauth') . '</a>';
            }

            return $actions;
        }// actionLinks


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            // add filter action links. this will be displayed in actions area of plugin page. for example: xxxbefore | Activate | Edit | Delete | xxxafter
            add_filter('plugin_action_links', [$this, 'actionLinks'], 10, 4);
            // add filter to row meta. (in plugin page below description). for example: By xxx | Visit plugin site | xxxafter
            add_filter('plugin_row_meta', [$this, 'rowMeta'], 10, 4);
        }// registerHooks


        /**
         * Add links to row meta that is in Plugins page under plugin description. For example: xxxbefore | By xxx | Visit plugin site | xxxafter
         * 
         * @link https://developer.wordpress.org/reference/hooks/plugin_row_meta/ Reference.
         * @staticvar string $plugin The plugin file name.
         * @param array $plugin_meta An array of the plugin’s metadata, including the version, author, author URI, and plugin URI.
         * @param string $plugin_file Path to the plugin file relative to the plugins directory.
         * @param array $plugin_data An array of plugin data.
         * @param string $status Status filter currently applied to the plugin list. Possible values are: `'all'`, `'active'`, `'inactive'`, `'recently_activated'`, `'upgrade'`, `'mustuse'`, `'dropins'`, `'search'`, `'paused'`, `'auto-update-enabled'`, `'auto-update-disabled'`.
         * @return array Return modified links.
         */
        public function rowMeta(array $plugin_meta, $plugin_file, array $plugin_data, $status = 'all')
        {
            static $plugin;

            if (!isset($plugin)) {
                $plugin = plugin_basename(OKVOAUTH_FILE);
            }

            if ($plugin === $plugin_file) {
                $after_link = [];

                $configValues = $this->getOptions();
                if (
                    is_array($configValues) && 
                    array_key_exists('db_settings_version', $configValues) && 
                    is_scalar($configValues['db_settings_version']) && 
                    !empty($configValues['db_settings_version'])
                ) {
                    /* translators: %s The DB settings version of this plugin. */
                    $after_link[] = sprintf(__('DB settings version %s', 'okv-oauth'), $configValues['db_settings_version']);
                }
                unset($configValues);

                $after_link[] = '<a href="https://rundiz.com/en/donate/" target="donate">' . esc_html__('Donate', 'okv-oauth') . '</a>';
                //$after_link[] = '<a href="#" onclick="return false;">Document</a>';
                $plugin_meta = array_merge($plugin_meta, $after_link);
                unset($after_link);
            }

            return $plugin_meta;
        }// rowMeta


    }// Plugins
}
