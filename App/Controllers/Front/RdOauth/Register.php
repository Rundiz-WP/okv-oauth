<?php
/**
 * Custom OAuth register page.
 * 
 * @package rundiz-oauth
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace RundizOauth\App\Controllers\Front\RdOauth;


if (!class_exists('\\RundizOauth\\App\\Controllers\\Front\\RdOauth\\Register')) {
    class Register
    {


        /**
         * The index action for /rd-oauth?rdoauth_subpage=register URI.
         * 
         * @global \WP_Query $wp_query
         */
        public function indexAction()
        {
            $output = [];

            $RundizOauth = new \RundizOauth\App\Libraries\RundizOauth();
            $RundizOauth->init();
            if (0 === $RundizOauth->loginMethod) {
                // if login method is using wp only.
                exit;
            }

            if (isset($_REQUEST['rdoauth']) && 'google' === $_REQUEST['rdoauth']) {
                // user choose to register with Google.
                $Google = new \RundizOauth\App\Libraries\MyOauth\Google();
                $result = $Google->wpRegisterWithGoogle();
                unset($Google);
            } elseif (isset($_REQUEST['rdoauth']) && 'facebook' === $_REQUEST['rdoauth']) {
                // user choose to register with Facebook.
                $Facebook = new \RundizOauth\App\Libraries\MyOauth\Facebook();
                $result = $Facebook->wpRegisterWithFacebook();
                unset($Facebook);
            }

            if (isset($result) && is_wp_error($result)) {
                $output['form_error_msg'] = $result->get_error_message();
            } elseif (isset($result) && is_array($result) && array_key_exists('email', $result)) {
                if (is_multisite()) {
                    // if multi-site.
                    $random_password = wp_generate_password(20, true, true);
                    $user_id = wpmu_create_user($result['email'], $random_password, $result['email']);// already sent an email to admin in this function.
                    unset($random_password);
                    if (!is_wp_error($user_id)) {
                        wp_safe_redirect(home_url('rd-oauth?rdoauth_subpage=register&rdoauth_result=success'));
                        exit;
                    } else {
                        $output['form_error_msg'] = \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('unableregister');
                    }
                } else {
                    // if NOT multi-site.
                    $random_password = wp_generate_password(20, true, true);
                    $user_id = wp_create_user($result['email'], $random_password, $result['email']);
                    unset($random_password);
                    if (!is_wp_error($user_id)) {
                        // if register success!
                        wp_new_user_notification($user_id, null, 'admin');// notify admin only.
                        wp_safe_redirect(home_url('rd-oauth?rdoauth_subpage=register&rdoauth_result=success'));
                        exit;
                    } else {
                        $output['form_error_msg'] = \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('unableregister');
                    }
                }// endif multisite.
            }

            unset($result, $RundizOauth);

            $this->registerScripts();

            global $wp_query;
            // prevent return 404 status.
            $wp_query->is_404 = false;
            status_header(200);

            // set title of the page.
            // @link https://developer.wordpress.org/reference/hooks/document_title_parts/ Reference.
            add_filter('document_title_parts', function($title) {
                $title['title'] = __('Rundiz OAuth', 'okv-oauth');
                return $title;
            });

            if (isset($_REQUEST['rdoauth_result']) && 'success' === $_REQUEST['rdoauth_result']) {
                /* translators: %1$s: Open link, %2$s: Close link */
                $output['form_success_msg'] = sprintf(__('Registration completed. You can now %1$slogin%2$s using selected OAuth provider.', 'okv-oauth'), '<a href="' . wp_login_url() . '">', '</a>');
            }

            $Loader = new \RundizOauth\App\Libraries\Loader();
            $Loader->loadTemplate('okv-oauth/register_v', $output);
            unset($Loader, $output);
            exit;
        }// indexAction


        /**
         * Register styles and scripts.
         */
        public function registerScripts()
        {
            if (!wp_script_is('rd-oauth-login', 'registered')) {
                $StylesAndScripts = new \RundizOauth\App\Libraries\StylesAndScripts();
                $StylesAndScripts->registerStylesAndScripts();
                unset($StylesAndScripts);
            }

            wp_enqueue_style('rd-oauth-login');
        }// registerScripts


    }
}