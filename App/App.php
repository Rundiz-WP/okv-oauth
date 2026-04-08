<?php
/**
 * Main app class. extend this class if you want to use any method of this class.
 * 
 * @package okv-oauth
 */


namespace OKVOauth\App;


if (!class_exists('\\OKVOauth\\App\\App')) {
    /**
     * Plugin application main entry class.
     */
    class App
    {


        /**
         * Run the WP plugin app.
         */
        public function run()
        {
            // Any method that must be called before auto register controllers must be manually write it down here, below this line.
            $StylesAndScripts = new Libraries\StylesAndScripts();
            $StylesAndScripts->manualRegisterHooks();
            unset($StylesAndScripts);

            // Initialize the loader class.
            $Loader = new \OKVOauth\App\Libraries\Loader();
            $Loader->autoRegisterControllers();
            unset($Loader);

            // Register all widgets.
            $WidgetAutoRegister = new Widgets\AutoRegisterWidgets();
            $WidgetAutoRegister->registerAll();
            unset($WidgetAutoRegister);
        }// run


    }// App
}
