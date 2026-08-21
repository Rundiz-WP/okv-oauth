<?php
/**
 * Main app class. Extend this class if you want to use any method of this class.
 * 
 * @package okv-oauth
 */


namespace OKVOauth\App;


if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('\\OKVOauth\\App\\App')) {
    /**
     * Plugin application main entry class.
     */
    class App
    {


        use AppTrait;


        /**
         * Load text domain. (Language files)
         * 
         * @since 1.7.7
         * @link https://make.wordpress.org/core/2025/03/12/i18n-improvements-6-8/ The load text domain function is not need if requires WP 6.8+
         * @link https://core.trac.wordpress.org/ticket/64249 Follow-up bug fix that auto load translation file not working on multi-site enabled.
         */
        public function loadLanguage()
        {
            load_plugin_textdomain('okv-oauth', false, dirname(plugin_basename(OKVOAUTH_FILE)) . '/App/languages/');
        }// loadLanguage


        /**
         * Run the WP plugin app.
         */
        public function run()
        {
            add_action('init', function () {
                // @link https://codex.wordpress.org/Function_Reference/load_plugin_textdomain Reference.
                // load language of this plugin.
                $this->loadLanguage();
            });

            // Any method that must be called before auto register controllers must be manually write it down here, below this line.
            $StylesAndScripts = new Libraries\StylesAndScripts();
            $StylesAndScripts->manualRegisterHooks();
            unset($StylesAndScripts);

            // Initialize the loader class.
            $this->Loader = new \OKVOauth\App\Libraries\Loader();
            $this->Loader->autoRegisterControllers();

            // Register all widgets.
            $WidgetAutoRegister = new Widgets\AutoRegisterWidgets();
            $WidgetAutoRegister->registerAll();
            unset($WidgetAutoRegister);
        }// run


    }// App
}
