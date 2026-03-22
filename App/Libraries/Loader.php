<?php
/**
 * Loader class. This class will load anything for example: views, template, configuration file.
 * 
 * @package okv-oauth
 */


namespace OKVOauth\App\Libraries;


if (!class_exists('\\OKVOauth\\App\\Libraries\\Loader')) {
    /**
     * Loader class for load template, view file, config file, etc.
     */
    class Loader
    {


        /**
         * Automatic look into those controllers and register to the main App class to make it works.<br>
         * The controllers that will be register must implement OKVOauth\App\Controllers\ControllerInterface to have registerHooks() method in it, otherwise it will be skipped.
         */
        public function autoRegisterControllers()
        {
            $this_plugin_dir = dirname(OKVOAUTH_FILE);
            $di = new \RecursiveDirectoryIterator($this_plugin_dir . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Controllers', \RecursiveDirectoryIterator::SKIP_DOTS);
            $it = new \RecursiveIteratorIterator($di);
            unset($di);

            foreach ($it as $file) {
                $this_file_classname = '\\OKVOauth' . str_replace([$this_plugin_dir, '.php', '/'], ['', '', '\\'], $file);
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
         * Load config file and return its values.
         * 
         * @param string $config_file_name The configuration file name only without extension.
         * @param bool $require_once Mark as `true` to use `require_once`, otherwise use `require`.
         * @return mixed Return config file content if success. Return `false` if failed.
         */
        public function loadConfig($config_file_name = 'config', $require_once = false)
        {
            $config_dir = dirname(__DIR__) . '/config/';

            if (file_exists($config_dir) && is_file($config_dir . $config_file_name . '.php')) {
                if (true === $require_once) {
                    $config_values = require_once $config_dir . $config_file_name . '.php';
                } else {
                    $config_values = require $config_dir . $config_file_name . '.php';
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
         * @throws \Exception Throws the error if template file is really not found from anywhere listed above.
         */
        public function loadTemplate($view_name, array $data = [])
        {
            global $wp_query;

            $pluginFolderName = dirname(plugin_basename(OKVOAUTH_FILE));
            $templatePath = locate_template($pluginFolderName . '/templates/' . $view_name . '.php');

            if ('' !== $templatePath) {
                // if template found in the theme location.
            } else {
                // if template was not found in theme location.
                $templatePath = plugin_dir_path(OKVOAUTH_FILE) . 'templates/' . $view_name . '.php';
                if (!is_file($templatePath)) {
                    // if not found the template file in plugin itself.
                    // remove the variable.
                    unset($pluginFolderName);
                    // throw the error to notice the developers.
                    /* translators: %s: Template path. */
                    throw new \Exception(esc_html(sprintf(__('The template file was not found. (%s)', 'okv-oauth'), $template_path)));
                }
            }

            if (isset($templatePath)) {
                if (!empty($data)) {
                    $wp_query->query_vars = array_merge($wp_query->query_vars, $data);
                    $data = [];
                }
                load_template($templatePath);
            }

            unset($pluginFolderName, $templatePath);
        }// loadTemplate


        /**
         * Load views.
         *
         * @param string $view_name View file name, refer from app/Views folder.
         * @param array $data For send data variable to view.
         * @param bool $require_once Set to `true` to use `include_once`, `false` to use `include`. Default is `false`.
         * @return bool Return `true` if success loading.
         * @throws \Exception Throws the error if views file was not found.
         */
        public function loadView($view_name, array $data = [], $require_once = false)
        {
            $view_dir = dirname(__DIR__) . '/Views/';
            $templateFile = $view_dir . $view_name . '.php';
            unset($view_dir);

            if ('' !== $view_name && file_exists($templateFile) && is_file($templateFile)) {
                // if views file was found.
                if (is_array($data)) {
                    extract($data, EXTR_PREFIX_SAME, 'dupvar_');// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
                }

                if (true === $require_once) {
                    include_once $templateFile;
                } else {
                    include $templateFile;
                }

                unset($templateFile);
                return true;
            } else {
                // if views file was not found.
                // throw the exception to notice the developers.
                throw new \Exception(
                    sprintf(
                        // translators: %s: Template path.
                        esc_html(__('The views file was not found (%s).', 'okv-oauth')), 
                        str_replace(['\\', '/'], '/', $templateFile)// phpcs:ignore WordPress.Security.EscapeOutput
                    )
                );
            }
        }// loadView


    }// Loader
}
