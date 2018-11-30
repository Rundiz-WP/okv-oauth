<?php
/**
 * Loader class. This class will load anything for example: views, template, configuration file.
 * 
 * @package rundiz-oauth
 */


namespace RundizOauth\App\Libraries;

if (!class_exists('\\RundizOauth\\App\\Libraries\\Loader')) {
    class Loader
    {


        /**
         * Automatic look into those controllers and register to the main App class to make it works.<br>
         * The controllers that will be register must implement RundizOauth\App\Controllers\ControllerInterface to have registerHooks() method in it, otherwise it will be skipped.
         */
        public function autoRegisterControllers()
        {
            $this_plugin_dir = dirname(RUNDIZOAUTH_FILE);
            $di = new \RecursiveDirectoryIterator($this_plugin_dir . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Controllers', \RecursiveDirectoryIterator::SKIP_DOTS);
            $it = new \RecursiveIteratorIterator($di);
            unset($di);

            foreach ($it as $file) {
                $this_file_classname = '\\RundizOauth' . str_replace([$this_plugin_dir, '.php', '/'], ['', '', '\\'], $file);
                if (class_exists($this_file_classname)) {
                    $ControllerClass = new $this_file_classname();
                    if (method_exists($ControllerClass, 'registerHooks')) {
                        $ControllerClass->registerHooks();
                    }
                    unset($ControllerClass);
                }
                unset($this_file_classname);
            }// endforeach;

            unset($file, $it, $this_plugin_dir);
        }// autoRegisterControllers


        /**
         * load config file and return its values.
         * 
         * @param string $config_file_name
         * @param boolean $require_once
         * @return mixed return config file content if success. return false if failed.
         */
        public function loadConfig($config_file_name = 'config', $require_once = false)
        {
            $config_dir = dirname(__DIR__).'/config/';

            if ($config_dir != null && file_exists($config_dir) && is_file($config_dir.$config_file_name.'.php')) {
                if ($require_once === true) {
                    $config_values = require_once $config_dir.$config_file_name.'.php';
                } else {
                    $config_values = require $config_dir.$config_file_name.'.php';
                }
            }

            unset($config_dir);
            if (isset($config_values)) {
                return $config_values;
            }
            return false;
        }// loadConfig


        /**
         * Load the template by looking at the theme first, if not found then load it from the plugin itself.
         * 
         * Example: If the <code>$view_name</code> is <code>mydir/mypage</code>.<br>
         * It will look up in <code>wp-content/themes/%your theme%/okv-oauth/templates/mydir/mypage.php</code> first.<br>
         * If not found then it will look up in <code>wp-content/plugins/okv-oauth/templates/mydir/mypage.php</code>.<br>
         * If it is still not found then the error will be thrown.
         * 
         * @link https://codex.wordpress.org/Function_Reference/locate_template Reference.
         * @link https://codex.wordpress.org/Function_Reference/load_template Reference.
         * @global \WP_Query $wp_query
         * @param string $view_name The template file name.
         * @param array $data The data to send to template file. The array key will becomes variable in template file.
         */
        public function loadTemplate($view_name, array $data = [])
        {
            global $wp_query;

            if ($template_path = locate_template('okv-oauth/templates/' . $view_name . '.php')) {
                // if template found in the theme location.
            } else {
                // if template was not found in theme location.
                $template_path = plugin_dir_path(RUNDIZOAUTH_FILE) . 'templates/' . $view_name . '.php';
                if (!is_file($template_path)) {
                    // if not found the template file in plugin itself.
                    // throw the error to notice the developers.
                    throw new \Exception(sprintf(__('The template file was not found. (%s)', 'okv-oauth'), $template_path));
                    // remove the variable.
                    unset($template_path);
                }
            }

            if (isset($template_path)) {
                if (!empty($data)) {
                    $wp_query->query_vars = $data;
                    $data = [];
                }
                load_template($template_path);
            }

            unset($template_path);
        }// loadTemplate


        /**
         * load views.
         * 
         * @param string $view_name view file name refer from app/Views folder.
         * @param array $data for send data variable to view.
         * @param boolean $require_once use include or include_once? if true, use include_once.
         * @return boolean return true if success loading, or return false if failed to load.
         */
        public function loadView($view_name, array $data = [], $require_once = false)
        {
            $view_dir = dirname(__DIR__).'/Views/';

            if ($view_name != null && file_exists($view_dir.$view_name.'.php') && is_file($view_dir.$view_name.'.php')) {
                if (is_array($data)) {
                    extract($data, EXTR_PREFIX_SAME, 'dupvar_');
                }

                if ($require_once === true) {
                    include_once $view_dir.$view_name.'.php';
                } else {
                    include $view_dir.$view_name.'.php';
                }

                unset($view_dir);
                return true;
            }

            unset($view_dir);
            return false;
        }// loadView


    }
}