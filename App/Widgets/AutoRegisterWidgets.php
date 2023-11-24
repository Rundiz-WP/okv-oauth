<?php
/**
 * Auto register all available widgets in this theme. To make it works, you have to call <code>registerAll()</code> method.
 * 
 * @package rundiz-oauth
 * @link https://wordpress.stackexchange.com/questions/396479/how-migrate-a-legacy-widget-to-block No need to convert/migrate widget to block.
 * @link https://developer.wordpress.org/block-editor/how-to-guides/widgets/legacy-widget-block/ In case it is needed, this is migration guide.
 */


namespace RundizOauth\App\Widgets;


if (!class_exists('\\RundizOauth\\App\\Widgets\\AutoRegisterWidgets')) {
    class AutoRegisterWidgets
    {


        /**
         * Register all widgets that come with this theme.
         */
        public function registerAll()
        {
            $widgets_folder = __DIR__;
            $DirectoryIterator = new \DirectoryIterator($widgets_folder);

            foreach ($DirectoryIterator as $fileinfo) {
                if (!$fileinfo->isDot() && $fileinfo->isFile() && strtolower($fileinfo->getExtension()) === 'php') {
                    $file_name_only = $fileinfo->getBasename('.php');
                    $class_name = __NAMESPACE__ . '\\' . $file_name_only;

                    if (__CLASS__ !== $class_name && class_exists($class_name)) {
                        add_action('widgets_init', function() use ($class_name) {
                            return register_widget($class_name);
                        }, 11);
                    }

                    unset($class_name, $file_name_only);
                }
            }// endforeach;

            unset($DirectoryIterator, $fileinfo, $widgets_folder);
        }// registerAll


    }
}