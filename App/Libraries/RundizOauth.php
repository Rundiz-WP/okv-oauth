<?php
/**
 * Rundiz OAuth register or login functional.
 * 
 * @package rundiz-oauth
 */


namespace RundizOauth\App\Libraries;


if (!class_exists('\\RundizOauth\\App\\Libraries\\RundizOauth')) {
    class RundizOauth
    {


        use \RundizOauth\App\AppTrait;


        /**
         * @var integer Login method. 0 = wp only, 1 = wp+oauth, 2 = oauth only.
         */
        public $loginMethod = 0;


        /**
         * @var array The oAuth providers that was enabled in the settings will appears here in the array value. Example: array('google', 'facebook');
         */
        public $oauthProviders = [];


        /**
         * @var boolean If settings to use wp login with oauth or oauth only then this value will be true.
         */
        public $useOauth = false;


        /**
         * Call to this class before begins using any properties.
         * 
         * @global array $rundizoauth_options
         */
        public function init()
        {
            if (false !== $this->useOauth) {
                // already initialized.
                return ;
            }

            // get all options from setting config file.
            $this->getOptions();

            global $rundizoauth_options;

            if (is_array($rundizoauth_options)) {
                if (
                    array_key_exists('login_method', $rundizoauth_options) &&
                    (
                        '1' === strval($rundizoauth_options['login_method']) ||
                        '2' === strval($rundizoauth_options['login_method'])
                    )
                ) {
                    // if choose login method as wp login with oauth or oauth only.
                    $this->useOauth = true;
                    $this->loginMethod = intval($rundizoauth_options['login_method']);

                    // check that which oauth providers was enabled.
                    if (
                        array_key_exists('google_login_enable', $rundizoauth_options) && 
                        array_key_exists('google_client_id', $rundizoauth_options) && 
                        array_key_exists('google_client_secret', $rundizoauth_options) && 
                        '1' === strval($rundizoauth_options['google_login_enable']) &&
                        !empty($rundizoauth_options['google_client_id']) &&
                        !empty($rundizoauth_options['google_client_secret'])
                    ) {
                        $this->oauthProviders[] = 'google';
                    }
                    if (
                        array_key_exists('facebook_login_enable', $rundizoauth_options) &&
                        array_key_exists('facebook_app_id', $rundizoauth_options) &&
                        array_key_exists('facebook_app_secret', $rundizoauth_options) &&
                        '1' === strval($rundizoauth_options['facebook_login_enable']) &&
                        !empty($rundizoauth_options['facebook_app_id']) &&
                        !empty($rundizoauth_options['facebook_app_secret'])
                    ) {
                        $this->oauthProviders[] = 'facebook';
                    }

                    // at last, verify that if login method is set to use oauth only and providers was set.
                    if (2 === $this->loginMethod && empty($this->oauthProviders)) {
                        // if login method is using oauth only and providers was not set (maybe not enabled or missed config values).
                        // change login method to 1
                        $this->loginMethod = 1;
                    }
                }
            }
        }// init


        /**
         * Redirect the logged in user and exit the program.
         * 
         * These code copy from wp-login.php file.
         * 
         * @param object $user user object from wp.
         */
        public function loggedinRedirect($user)
        {
            if (is_wp_error($user) || (!is_wp_error($user) && !is_object($user))) {
                return false;
            }

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $secure_cookie = '';
            
            if (isset($_SESSION['okv-oauth_redirect_to'])) {
                $redirect_to = $_SESSION['okv-oauth_redirect_to'];
                // Redirect to https if user wants ssl
                if ($secure_cookie && false !== strpos($redirect_to, 'wp-admin'))
                    $redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
            } else {
                $redirect_to = admin_url();
            }

            $requested_redirect_to = (isset($_SESSION['okv-oauth_redirect_to']) ? $_SESSION['okv-oauth_redirect_to'] : '');
            $redirect_to = apply_filters('login_redirect', $redirect_to, $requested_redirect_to, $user);
            unset($requested_redirect_to);

            if ((empty($redirect_to) || 'wp-admin/' === $redirect_to || admin_url() === $redirect_to)) {
                // If the user doesn't belong to a blog, send them to user admin. If the user can't edit posts, send them to their profile.
                if (is_multisite() && !get_active_blog_for_user($user->ID) && !is_super_admin($user->ID)) {
                    $redirect_to = user_admin_url();
                } elseif (is_multisite() && !$user->has_cap('read')) {
                    $redirect_to = get_dashboard_url($user->ID);
                } elseif (!$user->has_cap('edit_posts')) {
                    $redirect_to = admin_url('profile.php');
                }
            }

            session_write_close();
            wp_safe_redirect($redirect_to);
            
            exit;
        }// loggedinRedirect


    }
}