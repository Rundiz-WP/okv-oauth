<?php
/**
 * Common styles (CSS) and scripts (JS).
 *
 * @package okv-oauth
 */


namespace OKVOauth\App\Libraries;


if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('\\OKVOauth\\App\\Libraries\\StylesAndScripts')) {
    /**
     * Common use styles (CSS) and scripts (JS) class.
     */
    class StylesAndScripts
    {


        /**
         * Manually register hooks.
         */
        public function manualRegisterHooks()
        {
            // register stylesheets and scripts
            add_action('init', [$this, 'registerStylesAndScripts']);
            add_action('init', [$this, 'registerAdminStylesAndScripts']);
        }// manualRegisterHooks


        /**
         * Register admin styles and scripts.
         * 
         * Use for register only, do not enqueue here.
         *
         * The asset handle name will be added more specific name related to this plugin only to prevent the situation as the example below.  
         * Example:  
         * User has plugin AAA installed with this plugin.  
         * The plugin AAA and this plugin use the same CSS (or JS) with same handle name but the plugin AAA's asset version is older.  
         * The plugin AAA doesn't has CSS class that this plugin have such as `.sattellite-dish-icon`.  
         * This plugin is using `.sattellite-dish-icon` class but this plugin was loaded after plugin AAA, that means the CSS asset from this plugin will not be loaded.  
         * The asset class that this plugin is using will never work.  
         * To prevent this situation, the asset handle name **must** be more specific to the plugin.
         *
         * @since 1.7.2
         * @link https://developer.wordpress.org/reference/functions/wp_register_style/ Function reference.
         * @link https://github.com/WordPress/WordPress/blob/master/wp-includes/functions.wp-scripts.php The register style function `wp_add_inline_script` make a called to `_wp_scripts_maybe_doing_it_wrong()`.  
         *              The maybe doing it wrong function check that if `init` hook did called then it's work.
         */
        public function registerAdminStylesAndScripts()
        {
            if (is_admin()) {
                // rundiz settings custom styles
                wp_register_style('okv-oauth-rd-settings-customstyle-css', plugin_dir_url(OKVOAUTH_FILE) . 'assets/css/Admin/rd-settings-customstyle.css', [], OKVOAUTH_VERSION);

                // rundiz settings tabs
                wp_register_style('okv-oauth-rd-settings-tabs-css', plugin_dir_url(OKVOAUTH_FILE) . 'assets/css/Admin/rd-settings-tabs.css', [], OKVOAUTH_VERSION);
                wp_register_script('okv-oauth-rd-settings-tabs-js', plugin_dir_url(OKVOAUTH_FILE) . 'assets/js/Admin/rd-settings-tabs.js', [], OKVOAUTH_VERSION, true);
            }
        }// registerAdminStylesAndScripts


        /**
         * Register front-end & admin stylesheets and scripts for common use later.
         * 
         * Use for register only, do not enqueue here.
         *
         * Use more specific asset handle name. To see more description please read on `registerAdminStylesAndScripts()` method.
         *
         * @see \OKVOauth\App\Libraries\StylesAndScripts::registerAdminStylesAndScripts() For more details.
         */
        public function registerStylesAndScripts()
        {
            wp_register_style('okv-oauth-login-css', plugin_dir_url(OKVOAUTH_FILE) . 'assets/css/rd-oauth-login.css', [], OKVOAUTH_VERSION);
            wp_register_style('okv-oauth-font-awesome6', plugin_dir_url(OKVOAUTH_FILE) . 'assets/vendor/fontawesome/css/all.min.css', [], '6.7.2');
        }// registerStylesAndScripts


    }// StylesAndScripts
}
