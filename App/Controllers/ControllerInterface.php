<?php
/**
 * The controller interface.<br>
 * This file contain the interface and required method(s) that is needed to use with auto register controller in the loader library.
 * 
 * @package rundiz-oauth
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace RundizOauth\App\Controllers;

if (!interface_exists('\\RundizOauth\\App\\Controllers\\ControllerInterface')) {
    /**
     * The controller interface that have required methods to register hooks, and more.<br>
     * Implement this interface only when you want to have hooks into WP core.
     */
    interface ControllerInterface
    {


        /**
         * Register hooks (actions/filters) that will be hook into WordPress core.
         */
        public function registerHooks();


    }
}