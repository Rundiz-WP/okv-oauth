<?php
/**
 * Add front-end page rewrite rules.
 * 
 * @package okv-oauth
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace OKVOauth\App\Controllers\Front;


if (!class_exists('\\OKVOauth\\App\\Controllers\\Front\\RewriteRules')) {
    /**
     * RewriteRules class.
     */
    class RewriteRules implements \OKVOauth\App\Controllers\ControllerInterface
    {


        /**
         * Add rewrite rules.
         */
        public function addRewriteRules()
        {
            add_rewrite_rule('^rd-oauth', 'index.php?pagename=okv-oauth&rdoauth_subpage=index', 'top');
        }// addRewriteRules


        /**
         * Select which page and sub-page will be use.
         * 
         * In this method will call to other controllers depends on sub page.
         */
        public function goToPages()
        {
            if (get_query_var('pagename') === 'okv-oauth') {
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
         * @link https://developer.wordpress.org/reference/hooks/query_vars/ Reference.
         * @param array $vars The array of allowed query variable names.
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


    }// RewriteRules
}
