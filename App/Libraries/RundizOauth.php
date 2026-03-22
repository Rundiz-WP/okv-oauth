<?php
/**
 * Rundiz OAuth register or login functional.
 * 
 * @package okv-oauth
 */


namespace OKVOauth\App\Libraries;


if (!class_exists('\\OKVOauth\\App\\Libraries\\RundizOauth')) {
    /**
     * RundizOauth class.
     */
    class RundizOauth
    {


        use \OKVOauth\App\AppTrait;


        /**
         * @var int Login method. 0 = wp only, 1 = wp+oauth, 2 = oauth only.
         */
        public $loginMethod = 0;


        /**
         * @var array The oAuth providers that was enabled in the settings will appears here in the array value. Example: array('google', 'facebook');
         */
        public $oauthProviders = [];


        /**
         * @var bool If settings to use wp login with oauth or oauth only then this value will be true.
         */
        public $useOauth = false;


        /**
         * Call to this class before begins using any properties.
         * 
         * @global array $okv_oauth_options
         */
        public function init()
        {
            if (false !== $this->useOauth) {
                // already initialized.
                return;
            }

            // get all options from setting config file.
            $this->getOptions();

            global $okv_oauth_options;

            if (is_array($okv_oauth_options)) {
                if (
                    array_key_exists('login_method', $okv_oauth_options) &&
                    (
                        '1' === strval($okv_oauth_options['login_method']) ||
                        '2' === strval($okv_oauth_options['login_method'])
                    )
                ) {
                    // if choose login method as wp login with oauth or oauth only.
                    $this->useOauth = true;
                    $this->loginMethod = intval($okv_oauth_options['login_method']);

                    // check that which oauth providers was enabled.
                    if (
                        array_key_exists('google_login_enable', $okv_oauth_options) && 
                        array_key_exists('google_client_id', $okv_oauth_options) && 
                        array_key_exists('google_client_secret', $okv_oauth_options) && 
                        '1' === strval($okv_oauth_options['google_login_enable']) &&
                        !empty($okv_oauth_options['google_client_id']) &&
                        !empty($okv_oauth_options['google_client_secret'])
                    ) {
                        $this->oauthProviders[] = 'google';
                    }
                    if (
                        array_key_exists('facebook_login_enable', $okv_oauth_options) &&
                        array_key_exists('facebook_app_id', $okv_oauth_options) &&
                        array_key_exists('facebook_app_secret', $okv_oauth_options) &&
                        '1' === strval($okv_oauth_options['facebook_login_enable']) &&
                        !empty($okv_oauth_options['facebook_app_id']) &&
                        !empty($okv_oauth_options['facebook_app_secret'])
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
         * @param \WP_User $user user object.
         */
        public function loggedinRedirect(\WP_User $user)
        {
            if (is_wp_error($user) || (!is_wp_error($user) && !is_object($user))) {
                return false;
            }

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (isset($_SESSION['okv-oauth_redirect_to'])) {
                $redirect_to = sanitize_url($_SESSION['okv-oauth_redirect_to']);
            } else {
                $redirect_to = admin_url();
            }

            $requested_redirect_to = (isset($_SESSION['okv-oauth_redirect_to']) ? sanitize_url($_SESSION['okv-oauth_redirect_to']) : '');
            // use (apply) WP core filter.
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
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


    }// RundizOauth
}
