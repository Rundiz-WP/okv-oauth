<?php
/**
 * Deactivate the plugin action.
 * 
 * @package okv-oauth
 * @since 1.7.2
 */


namespace OKVOauth\App\Controllers\Admin\Plugins;


if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('\\OKVOauth\App\Controllers\Admin\Plugins\\Deactivation')) {
    /**
     * Plugin deactivation hook class.
     * 
     * @since 1.7.2
     */
    class Deactivation implements \OKVOauth\App\Controllers\ControllerInterface
    {


        /**
         * Deactivate the plugin.
         * 
         * @since 1.7.0 Renamed from `deactivation`.
         * @since 1.7.2 Moved from `Activation->deactivate()`.
         */
        public function deactivate()
        {
            // Do something that will be happens on deactivate plugin.
            // remove all added rewrite rules.
            flush_rewrite_rules();
        }// deactivate


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            // register deactivate hook
            register_deactivation_hook(OKVOAUTH_FILE, [$this, 'deactivate']);
        }// registerHooks


    }// Deactivation
}
