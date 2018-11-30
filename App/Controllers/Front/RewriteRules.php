<?php
/**
 * Add front-end page rewrite rules.
 * 
 * @package rundiz-oauth
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace RundizOauth\App\Controllers\Front;


if (!class_exists('\\RundizOauth\\App\\Controllers\\Front\\RewriteRules')) {
    class RewriteRules implements \RundizOauth\App\Controllers\ControllerInterface
    {


        /**
         * Add rewrite rules.
         */
        public function addRewriteRules()
        {
            add_rewrite_rule('^rd-oauth', 'index.php?pagename=rundiz-oauth&rdoauth_subpage=index', 'top');
        }// addRewriteRules


        /**
         * Select which page and sub-page will be use.
         * 
         * In this method will call to other controllers depends on sub page.
         */
        public function goToPages()
        {
            if (get_query_var('pagename') == 'rundiz-oauth') {
                switch (get_query_var('rdoauth_subpage')) {
                    case 'register':
                        $Register = new RdOauth\Register();
                        $Register->indexAction();
                        unset($Register);
                        break;
                    case 'login':
                    case 'index':// it's login
                    default:
                        $Index = new RdOauth\Index();
                        $Index->indexAction();
                        unset($Index);
                        break;
                }
                exit;// required.
            }
        }// goToPages


        /**
         * Setup additional query variable.
         * 
         * @param array $vars
         * @return array
         */
        public function queryVars($vars)
        {
            $vars[] = 'rdoauth_subpage';

            return $vars;
        }// queryVars


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            if (!is_admin()) {
                add_filter('query_vars', [$this, 'queryVars']);
                add_action('template_redirect', [$this, 'goToPages']);
            }
            add_action('init', [$this, 'addRewriteRules']);// not condition to front-end or admin to make it work on theme changed.
        }// registerHooks


    }
}