<?php
/**
 * Styles (CSS) and scripts (JS).
 * 
 * @package okv-oauth
 */


namespace OKVOauth\App\Libraries;


if (!class_exists('\\OKVOauth\\App\\Libraries\\StylesAndScripts')) {
    /**
     * Styles and scripts class.
     */
    class StylesAndScripts
    {


        /**
         * Manually register hooks.
         */
        public function manualRegisterHooks()
        {
            // register stylesheets and scripts
            add_action('admin_enqueue_scripts', [$this, 'registerStylesAndScripts']);
            add_action('wp_enqueue_scripts', [$this, 'registerStylesAndScripts']);
        }// manualRegisterHooks


        /**
         * Register common CSS and JS.
         */
        public function registerStylesAndScripts()
        {
            wp_register_style('rd-oauth-login', plugin_dir_url(OKVOAUTH_FILE) . 'assets/css/rd-oauth-login.css', [], OKVOAUTH_VERSION);
            wp_register_style('rd-oauth-font-awesome6', plugin_dir_url(OKVOAUTH_FILE) . 'assets/vendor/fontawesome/css/all.min.css', [], '6.7.2');
        }// registerStylesAndScripts


    }// StylesAndScripts
}
