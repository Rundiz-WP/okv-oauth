<?php
/**
 * Plugin Name: Rundiz OAuth
 * Plugin URI: https://rundiz.com/?p=319
 * Description: Use OAuth such as Google, Facebook to login and register.
 * Version: 1.4.1
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


// include this plugin's autoload.
require __DIR__.'/autoload.php';


// initialize plugin app main class.
$this_plugin_app = new \RundizOauth\App\App();
$this_plugin_app->run();
unset($this_plugin_app);