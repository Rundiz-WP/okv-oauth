<?php
/**
 * Main app class. extend this class if you want to use any method of this class.
 * 
 * @package rundiz-oauth
 */


namespace RundizOauth\App;

use RundizOauth\App\Controllers as Controllers;

if (!class_exists('\\RundizOauth\\App\\App')) {
    class App
    {


        /**
         * @var \RundizOauth\App\Libraries\Loader
         */
        public $Loader;


        /**
         * load text domain. (language files)
         */
        public function loadLanguage()
        {
            load_plugin_textdomain('okv-oauth', false, dirname(plugin_basename(RUNDIZOAUTH_FILE)) . '/languages/');
        }// loadLanguage


        /**
         * run the wp plugin app.
         */
        public function run()
        {
            add_action('plugins_loaded', function() {
                // @link https://codex.wordpress.org/Function_Reference/load_plugin_textdomain Reference.
                // @link https://developer.wordpress.org/reference/functions/load_plugin_textdomain/ Reference.
                // @link https://wordpress.stackexchange.com/questions/245250/override-plugin-text-domain-in-child-theme Override text domain, translation by other themes, plugins.
                // load language of this plugin.
                $this->loadLanguage();
            });

            // Any method that must be called before auto register controllers must be manually write it down here, below this line.
            $StylesAndScripts = new Libraries\StylesAndScripts();
            $StylesAndScripts->manualRegisterHooks();
            unset($StylesAndScripts);

            // Initialize the loader class.
            $this->Loader = new \RundizOauth\App\Libraries\Loader();
            $this->Loader->autoRegisterControllers();

            // The rest of controllers that is not able to register via loader's auto register.
            // They must be manually write it down here, below this line.
            // For example:
            // $SomeController = new \RundizOauth\App\Controllers\SomeController();
            // $SomeController->runItHere();
            // unset($SomeController);// for clean up memory.
            // ------------------------------------------------------------------------------------
        }// run


    }
}