<?php
/**
 * Plugin Name: Rundiz OAuth
 * Plugin URI: https://rundiz.com/?p=319
 * Description: Use OAuth such as Google, LINE to login and register.
 * Version: 1.7.1
 * Requires at least: 5.0
 * Requires PHP: 5.4
 * Author: Vee Winch
 * Author URI: https://rundiz.com
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: okv-oauth
 * Domain Path: /languages/
 * 
 * @package okv-oauth
 */


if (!defined('ABSPATH')) {
    exit();
}


// define this plugin main file path.
if (!defined('OKVOAUTH_FILE')) {
    define('OKVOAUTH_FILE', __FILE__);
}


if (!defined('OKVOAUTH_VERSION')) {
    $okv_oauth_pluginData = (function_exists('get_file_data') ? get_file_data(__FILE__, ['Version' => 'Version']) : null);
    $okv_oauth_pluginVersion = (isset($okv_oauth_pluginData['Version']) ? $okv_oauth_pluginData['Version'] : date('Ym'));// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
    unset($okv_oauth_pluginData);
    define('OKVOAUTH_VERSION', $okv_oauth_pluginVersion);
    unset($okv_oauth_pluginVersion);
}


// include this plugin's autoload.
require __DIR__ . '/autoload.php';


// initialize plugin app main class.
$okv_oauth_App = new \OKVOauth\App\App();
$okv_oauth_App->run();
unset($okv_oauth_App);
