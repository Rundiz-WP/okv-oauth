<?php
/**
 * Styles (CSS) and scripts (JS).
 * 
 * @package rundiz-oauth
 */


namespace RundizOauth\App\Libraries;


if (!class_exists('\\RundizOauth\\App\\Libraries\\StylesAndScripts')) {
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
            wp_register_style('rd-oauth-login', plugin_dir_url(RUNDIZOAUTH_FILE) . 'assets/css/rd-oauth-login.css', [], '1.4');
            wp_register_style('rd-oauth-font-awesome6', plugin_dir_url(RUNDIZOAUTH_FILE).'assets/vendor/font-awesome/css/all.min.css', [], '4.7.0');
        }// registerStylesAndScripts


    }
}