<?php
/**
 * Login links block.
 * 
 * @package okv-oauth
 */


namespace OKVOauth\App\Controllers\Blocks;


if (!class_exists('\\OKVOauth\\App\\Controllers\\Blocks\\LoginLinks')) {
    class LoginLinks implements \OKVOauth\App\Controllers\ControllerInterface
    {


        /**
         * Register block.
         */
        public function registerBlock()
        {
            register_block_type(
                dirname(OKVOAUTH_FILE) . DIRECTORY_SEPARATOR . 'blocks' . DIRECTORY_SEPARATOR . 'loginlinks'
            );

            if (function_exists('wp_set_script_translations')) {
                wp_set_script_translations('rd-oauth-loginlinks-block-script', 'okv-oauth', str_replace([DIRECTORY_SEPARATOR], '/', plugin_dir_path(OKVOAUTH_FILE)) . 'languages');
            }
        }// registerBlock


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            add_action('init', [$this, 'registerBlock']);
        }// registerHooks


    }
}

