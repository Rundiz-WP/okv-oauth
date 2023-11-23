<?php
/**
 * Login links block.
 * 
 * @package rundiz-oauth
 */


namespace RundizOauth\App\Controllers\Blocks;


if (!class_exists('RundizOauth\App\Controllers\Blocks\\LoginLinks')) {
    class LoginLinks implements \RundizOauth\App\Controllers\ControllerInterface
    {


        /**
         * Register block.
         */
        public function registerBlock()
        {
            register_block_type(
                dirname(RUNDIZOAUTH_FILE) . DIRECTORY_SEPARATOR . 'blocks' . DIRECTORY_SEPARATOR . 'loginlinks'
            );

            if (function_exists('wp_set_script_translations')) {
                wp_set_script_translations('rd-oauth-loginlinks-block-script', 'okv-oauth', str_replace([DIRECTORY_SEPARATOR], '/', plugin_dir_path(RUNDIZOAUTH_FILE)) . 'languages');
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

