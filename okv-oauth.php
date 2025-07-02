<?php
/**
 * Plugin Name: Rundiz OAuth
 * Plugin URI: https://rundiz.com/?p=319
 * Description: Use OAuth such as Google, Facebook to login and register.
 * Version: 1.6.3
 * Requires at least: 5.0
 * Requires PHP: 5.4
 * Author: Vee Winch
 * Author URI: http://rundiz.com
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: okv-oauth
 * Domain Path: /languages/
 * 
 * @package rundiz-oauth
 */


// define this plugin main file path.
if (!defined('RUNDIZOAUTH_FILE')) {
    define('RUNDIZOAUTH_FILE', __FILE__);
}


if (!defined('RUNDIZOAUTH_VERSION')) {
    $pluginData = (function_exists('get_file_data') ? get_file_data(__FILE__, ['Version' => 'Version']) : null);
    $pluginVersion = (isset($pluginData['Version']) ? $pluginData['Version'] : date('Ym'));
    unset($pluginData);
    define('RUNDIZOAUTH_VERSION', $pluginVersion);
    unset($pluginVersion);
}


// include this plugin's autoload.
require __DIR__.'/autoload.php';


// initialize plugin app main class.
$this_plugin_app = new \RundizOauth\App\App();
$this_plugin_app->run();
unset($this_plugin_app);
