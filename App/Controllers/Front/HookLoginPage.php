<?php
/**
 * Hooks into login page.
 * 
 * @package rundiz-oauth
 */


namespace RundizOauth\App\Controllers\Front;


if (!class_exists('\\RundizOauth\\App\\Controllers\\Front\\HookLoginPage')) {
    class HookLoginPage extends \RundizOauth\App\Libraries\RundizOauth implements \RundizOauth\App\Controllers\ControllerInterface
    {


        /**
         * Enqueue styles and scripts for edit profile page.
         * 
         * @param string $hook
         */
        public function adminEnqueueScripts($hook)
        {
            if (is_admin() && $hook === 'profile.php') {
                wp_enqueue_style('rd-oauth-login', plugin_dir_url(RUNDIZOAUTH_FILE) . 'assets/css/rd-oauth-login.css' );
                wp_enqueue_style('font-awesome-4', plugin_dir_url(RUNDIZOAUTH_FILE) . 'assets/css/font-awesome.min.css', [], '4.7.0');
            }
        }// adminEnqueueScripts


        /**
         * Disallow password reset if setting is using OAuth only.
         * 
         * @link https://developer.wordpress.org/reference/hooks/allow_password_reset/ Reference.
         * @param boolean $allow
         * @param integer $user_id
         * @return boolean
         */
        public function allowPasswordReset($allow, $user_id)
        {
            $this->init();

            if ($this->loginMethod === 2) {
                $allow = false;
            }

            return $allow;
        }// allowPasswordReset


        /**
         * Add notice, error (if available).
         * 
         * This is useful in edit profile page.
         * 
         * @link https://stackoverflow.com/questions/1242328/wordpress-displaying-an-error-message-hook-admin-notices-fails-on-wp-insert-p Reference.
         */
        public function adminNotice()
        {
            if($output = get_transient('rundiz-oauth-error')) {
                delete_transient('rundiz-oauth-error');
                $output = maybe_unserialize(stripslashes_deep($output));

                if (is_array($output) && array_key_exists('class', $output) && array_key_exists('message', $output)) {
                    $Loader = new \RundizOauth\App\Libraries\Loader();
                    $Loader->loadView('admin/adminNotice_v', $output);
                    unset($Loader, $output);
                }
            }
        }// adminNotice


        /**
         * Set cookie expiration.
         * 
         * @link https://developer.wordpress.org/reference/hooks/auth_cookie_expiration/ Reference.
         * @global array $rundizoauth_options
         * @param integer $length
         * @param integer $user_id
         * @param boolean $remember
         */
        public function authCookieExpiration($length, $user_id, $remember = false)
        {
            if (!is_numeric($user_id)) {
                $user_id = 0;
            }

            if (!is_bool($remember)) {
                $remember = false;
            }

            $this->init();

            global $rundizoauth_options;

            
            if (is_array($rundizoauth_options)) {
                if (array_key_exists('login_expiration', $rundizoauth_options) &&
                    !empty($rundizoauth_options['login_expiration']) &&
                    intval($rundizoauth_options['login_expiration']) > 0
                ) {
                    // if login expiration was set, get its length as new length for next checking.
                    $newLength = intval($rundizoauth_options['login_expiration']);
                }

                if (isset($newLength) && ($this->loginMethod === 2 || $remember === true)) {
                    // if rundiz oauth settings is using oauth only OR normal login form and user tick on remember me.
                    // remove filter hook to prevent call hook loop into itself (here, again).
                    remove_filter('auth_cookie_expiration', [$this, 'authCookieExpiration']);
                    // call to the same filter hook to allow other plugins to override login expiration.
                    $length = apply_filters('auth_cookie_expiration', $newLength * DAY_IN_SECONDS, $user_id, $remember);
                }

                unset($newLength);
            }// endif;

            return $length;
        }// authCookieExpiration


        /**
         * Check that plugin setting is using OAuth only and if there is form submitted then show the error message.
         * 
         * This hook was called every time you visit /wp-login.php page.
         * 
         * @link https://developer.wordpress.org/reference/hooks/authenticate/ Reference.
         * @link https://ben.lobaugh.net/blog/7175/wordpress-replace-built-in-user-authentication Reference.
         * @param null|\WP_User|\WP_Error $user
         * @param string $username
         * @param string $password
         * @return null|\WP_User|\WP_Error
         */
        public function authenticate($user, $username, $password)
        {
            if (is_a($user, 'WP_User')) {
                // if already logged in (maybe by previous hook or plugin), do nothing.
                return $user;
            }

            $this->init();

            if ($this->loginMethod === 2 && ($username != null || $password != null)) {
                // if using oauth only but there is form data submitted.
                // show the error message.
                return new \WP_Error('rundiz_oauth_login_error', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('originallogindisabled'));
            }

            return $user;
        }// authenticate


        /**
         * Add buttons into edit account page.
         * 
         * Useful for WooCommerce.
         */
        public function editAccountChangeEmailButton()
        {
            $this->init();

            if ($this->useOauth === true) {
                global $rundizoauth_options;

                $Loader = new \RundizOauth\App\Libraries\Loader();
                $Loader->loadTemplate('okv-oauth/partials/editAccountChangeEmailButton_v', ['rundizoauth_options' => $rundizoauth_options]);
                unset($Loader);
            }
        }// editAccountChangeEmailButton


        /**
         * Add buttons into edit own profile before name section.
         * 
         * @param \WP_User $user
         */
        public function editOwnProfilePersonalOptions($user)
        {
            $this->init();

            if ($this->useOauth === true) {
                global $rundizoauth_options;

                $Loader = new \RundizOauth\App\Libraries\Loader();
                $Loader->loadView('admin/editOwnProfilePersonalOptions_v', ['rundizoauth_options' => $rundizoauth_options]);
                unset($Loader);
            } else {
                return false;
            }
        }// editOwnProfilePersonalOptions


        /**
         * Perform change an email with OAuth.
         */
        public function loadProfile()
        {
            $this->init();

            if ($this->loginMethod === 1 || $this->loginMethod === 2) {
                // if rundiz oauth settings is using wp+oauth (1) or oauth only (2).
                if (isset($_REQUEST['rdoauth']) && $_REQUEST['rdoauth'] === 'google') {
                    // user choose to login with google.
                    $Google = new \RundizOauth\App\Libraries\MyOauth\Google();
                    $email = $Google->wpCheckEmailNotExists();
                    unset($Google);
                } elseif (isset($_REQUEST['rdoauth']) && $_REQUEST['rdoauth'] === 'facebook') {
                    // user choose to login with facebook.
                    $Facebook = new \RundizOauth\App\Libraries\MyOauth\Facebook();
                    $email = $Facebook->wpCheckEmailNotExists();
                    unset($Facebook);
                }

                if (isset($email)) {
                    if (!is_wp_error($email) && is_scalar($email)) {
                        $user_id = get_current_user_id();
                        $user = get_user_by('ID', $user_id);
                        wp_update_user(['ID' => $user->ID, 'user_email' => $email]);
                        do_action('rundiz_oauth_changeemail_success', $user->ID, $email);
                        unset($email, $user, $user_id);

                        set_transient('rundiz-oauth-error', maybe_serialize(['class' => 'notice-success', 'message' => __('Your email has been changed.', 'okv-oauth')]));
                    } elseif (is_wp_error($email)) {
                        set_transient('rundiz-oauth-error', maybe_serialize(['class' => 'notice-error', 'message' => $email->get_error_message()]));
                    }

                    wp_safe_redirect(get_edit_user_link());
                    exit;
                }
            }
        }// loadProfile


        /**
         * Add CSS to login/register page.
         */
        public function loginEnqueueScripts()
        {
            $this->init();

            if ($this->useOauth === true) {
                // if choose login method as wp login with oauth or oauth only.
                $action = (isset($_REQUEST['action']) ? $_REQUEST['action'] : '');
                if (!in_array($action, ['postpass', 'logout', 'lostpassword', 'retrievepassword', 'resetpass', 'rp', 'register', 'login'], true) && false === has_filter('login_form_' . $action )) {
                    $action = 'login';
                }

                switch ($action) {
                    case 'postpass':
                    case 'logout':
                    case 'lostpassword':
                        if ($this->loginMethod === 2) {
                            wp_enqueue_script('rd-oauth-lostpassword', plugin_dir_url(RUNDIZOAUTH_FILE) . 'assets/js/rd-oauth-lostpassword.js', ['jquery'], false, true);
                            wp_localize_script(
                                'rd-oauth-lostpassword', 
                                'RdOauthLostPassword', 
                                [
                                    'loginMethod' => $this->loginMethod,
                                ]
                            );
                        }
                        break;
                    case 'retrievepassword':
                    case 'resetpass':
                    case 'rp':
                        break;
                    case 'register':
                    case 'login':
                    default:
                        wp_enqueue_style('rd-oauth-login', plugin_dir_url(RUNDIZOAUTH_FILE) . 'assets/css/rd-oauth-login.css' );
                        wp_enqueue_style('font-awesome-4', plugin_dir_url(RUNDIZOAUTH_FILE) . 'assets/css/font-awesome.min.css', [], '4.7.0');
                }

                if (isset($action) && $action === 'register') {
                    // if in register page.
                    wp_enqueue_script('rd-oauth-register', plugin_dir_url(RUNDIZOAUTH_FILE) . 'assets/js/rd-oauth-register.js', ['jquery'], false, true);
                    wp_localize_script(
                        'rd-oauth-register', 
                        'RdOauthRegister', 
                        [
                            'loginMethod' => $this->loginMethod,
                        ]
                    );
                } elseif (isset($action) && $action === 'login') {
                    // if in login page.
                    wp_enqueue_script('rd-oauth-login', plugin_dir_url(RUNDIZOAUTH_FILE) . 'assets/js/rd-oauth-login.js', ['jquery'], false, true);
                    wp_localize_script(
                        'rd-oauth-login', 
                        'RdOauthLogin', 
                        [
                            'loginMethod' => $this->loginMethod,
                        ]
                    );
                }
            }
        }// loginEnqueueScripts


        /**
         * Add buttons into login form.
         * 
         * @link https://developer.wordpress.org/reference/hooks/login_form/ Reference.
         * @global array $rundizoauth_options
         */
        public function loginForm()
        {
            $this->init();

            if ($this->useOauth === true) {
                global $rundizoauth_options;

                $Loader = new \RundizOauth\App\Libraries\Loader();
                $Loader->loadTemplate('okv-oauth/partials/loginForm_v', ['rundizoauth_options' => $rundizoauth_options]);
                unset($Loader);
            } else {
                return false;
            }
        }// loginForm


        /**
         * Change URL of login logo.
         * 
         * @link https://codex.wordpress.org/Plugin_API/Filter_Reference/login_headerurl Reference.
         * @return string
         */
        public function loginHeaderUrl()
        {
            return home_url();
        }// loginHeaderUrl


        /**
         * Display lost password form disabled if settings is using OAuth only.
         * 
         * @link https://developer.wordpress.org/reference/hooks/lostpassword_form/ Reference.
         */
        public function lostPasswordForm()
        {
            $this->init();

            if ($this->loginMethod === 2) {
                // if using oauth only.
                // not allow to lost password because user have to login using oauth provider such as Google.
                $Loader = new \RundizOauth\App\Libraries\Loader();
                $Loader->loadTemplate('okv-oauth/partials/lostPasswordForm_v');
                unset($Loader);
            }
        }// lostPasswordForm


        /**
         * Disable lost password function if settings is using OAuth only.
         * 
         * @link https://developer.wordpress.org/reference/hooks/lostpassword_post/ Reference.
         * @link https://developer.wordpress.org/reference/hooks/retrieve_password/ Reference.
         */
        public function lostPasswordPost()
        {
            $this->init();

            if ($this->loginMethod === 2) {
                exit;
            }
        }// lostPasswordPost


        /**
         * Add buttons to register page.
         * 
         * @link https://developer.wordpress.org/reference/hooks/register_form/ Reference.
         * @global array $rundizoauth_options
         */
        public function registerForm()
        {
            $this->init();

            if ($this->useOauth === true) {
                global $rundizoauth_options;
                $active_signup = get_site_option('registration', 'none');// 'all', 'none', 'blog', or 'user'

                $Loader = new \RundizOauth\App\Libraries\Loader();
                $Loader->loadTemplate(
                    'okv-oauth/partials/registerForm_v', 
                    [
                        'rundizoauth_options' => $rundizoauth_options, 
                        'oauthProviders' => $this->oauthProviders,
                        'active_signup' => $active_signup,
                    ]
                );
                unset($Loader);
            } else {
                return false;
            }
        }// registerForm


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            // remember redirect_to querystring and set to session for use in this plugin page.
            add_action('login_init', [$this, 'rememberRedirectTo']);

            // add css to login page.
            add_action('login_enqueue_scripts', [$this, 'loginEnqueueScripts']);
            // change login url on the logo.
            add_filter('login_headerurl', [$this, 'loginHeaderUrl']);

            // register page. -------------------------------------------------------------------
            // add buttons to register page.
            add_action('register_form', [$this, 'registerForm']);
            // add buttons to wp-signup.php page.
            add_action('signup_extra_fields', [$this, 'registerForm']);
            add_action('before_signup_header', [$this, 'wpSignupEnqueueScripts']);
                // the same as above but only for woocommerce. (needs style and script manually).
                add_action('woocommerce_register_form_start', [$this, 'registerForm']);
                // the same as above but only for woocommerce. (needs style and script manually).
                add_action('woocommerce_after_checkout_registration_form', [$this, 'registerForm']);
            // perform register action with OAuth. using /rd-oauth?rdoauth_subpage=register custom front-end page instead.
            // after register form submitted but before add. for create validation.
            add_filter('registration_errors', [$this, 'registrationErrors'], 10, 3);
                // the same as above but only for woocommerce. (needs style and script manually).
                add_filter('woocommerce_registration_errors', [$this, 'registrationErrors'], 10, 3);
            // check that if plugin setting using OAuth only and registration option is 'user' then set this value to none.
            add_filter('wpmu_active_signup', [$this, 'wpSignupActiveValue']);

            // login page. ----------------------------------------------------------------------
            // add buttons into login page.
            add_action('login_form', [$this, 'loginForm']);
                // the same as above but only for woocommerce. (needs style and script manually).
                add_action('woocommerce_login_form_start', [$this, 'loginForm']);
            // set authenticate cookie expiration.
            add_filter('auth_cookie_expiration', [$this, 'authCookieExpiration'], 10, 3);
            // check that settings is using OAuth only and if there is form submitted then show the error.
            add_filter('authenticate', [$this, 'authenticate'], 30, 3);

            // lost password page. ------------------------------------------------------------
            // display error at lost password form if using OAuth only.
            add_action('lostpassword_form', [$this, 'lostPasswordForm']);
            // hooks into last password after form submitted but before process.
            add_action('lostpassword_post', [$this, 'lostPasswordPost']);
            add_action('retreive_password', [$this, 'lostPasswordPost']);
            // disallow password to be reset if using OAuth only.
            add_filter('allow_password_reset', [$this, 'allowPasswordReset'], 10, 2);

            // edit profile page. (admin section) ---------------------------------------------
            // display error message (if available).
            add_action('admin_notices', [$this, 'adminNotice']);
                // same as above but for user only.
                add_action('user_admin_notices', [$this, 'adminNotice']);
            // prevent user manually change their email if settings is using OAuth only.
            add_action('personal_options_update', [$this, 'updateOwnProfile'], 10, 1);
                // same as above but for woocommerce edit account.
                add_filter('woocommerce_save_account_details_errors', [$this, 'wcPreventEmailChange'], 10, 2);
            // add css to admin edit profile page.
            add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
            // add buttons into edit own profile page.
            add_action('profile_personal_options', [$this, 'editOwnProfilePersonalOptions']);
            // add change email buttons for woocommerce edit profile page.
            add_action('woocommerce_edit_account_form_start', [$this, 'editAccountChangeEmailButton']);
            // perform change email with OAuth.
            add_action('load-profile.php', [$this, 'loadProfile']);
                // same as above but for woocommerce only by change the billing email.
                add_action('rundiz_oauth_changeemail_success', [$this, 'wcChangeBillingEmailUsingOAuth'], 10, 2);

            // on logout, remove any cookies that used while register/login
            add_action('wp_logout', [$this, 'wpLogout']);
        }// registerHooks


        /**
         * Add validations
         * 
         * @link https://developer.wordpress.org/reference/hooks/registration_errors/ Reference.
         * @param \WP_Error $errors
         * @param string $sanitized_user_login
         * @param string $user_email
         * @return object
         */
        public function registrationErrors(\WP_Error $errors, $sanitized_user_login, $user_email)
        {
            $this->init();

            if (
                $this->loginMethod === 2 &&
                (
                    strtoupper($_SERVER['REQUEST_METHOD']) === 'POST' ||
                    isset($_REQUEST['user_email'])
                )
            ) {
                // if use oauth only but have form submitted.
                $errors->add('register-form-disabled', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('originalregisterdisabled'));
            }

            return $errors;
        }// registrationErrors


        /**
         * Remember redirect to querystring and set to session for use later.
         */
        public function rememberRedirectTo()
        {
            $this->init();

            if ($this->useOauth) {
                if (isset($_REQUEST['redirect_to'])) {
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }

                    $_SESSION['okv-oauth_redirect_to'] = $_REQUEST['redirect_to'];
                }
            }
        }// rememberRedirectTo


        /**
         * Validate and detect email changes while using OAuth only is not allowed.
         * 
         * @global array $rundizoauth_options
         * @param integer $user_id
         */
        public function updateOwnProfile($user_id)
        {
            $this->init();

            global $rundizoauth_options;

            if ($this->useOauth === true && $this->loginMethod === 2 && is_numeric($user_id)) {
                // if using OAuth only.
                $current_user = get_user_by('id', $user_id);

                if (isset($_POST['email']) && isset($current_user->user_email) && $_POST['email'] !== $current_user->user_email) {
                    // if detecting email changes while it is using OAuth only. No, do not allow. restore the post email value.
                    $_POST['email'] = $current_user->user_email;
                    // trigger no change email error.
                    add_action('user_profile_update_errors', function($errors, $update, $user) {
                        $errors->add('donot-manually-change-email', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('donotmanuallychangeemail'));
                    }, 10, 3);
                }

                unset($current_user);
            }
        }// updateOwnProfile


        public function wcChangeBillingEmailUsingOAuth($user_id, $new_email = null)
        {
            if (is_numeric($user_id) && !empty($new_email) && is_email($new_email) && class_exists('\\WooCommerce')) {
                // the code below this line copy from class-wc-from-handler.php in WooCommerce.
                // it is in WC_Form_Handler class save_account_details() method.
                $customer = new \WC_Customer($user_id);
                $customer->set_billing_email($new_email);
                $customer->save();
            }
        }// wcChangeBillingEmailUsingOAuth


        /**
         * Prevent user manually change email in WooCommerce.
         * 
         * @global array $rundizoauth_options
         * @param \WP_Error $errors
         * @param object $user
         */
        public function wcPreventEmailChange(&$errors = null, &$user = null)
        {
            if (!class_exists('\\WooCommerce')) {
                return ;
            }

            $this->init();

            global $rundizoauth_options;

            if ($this->useOauth === true && $this->loginMethod === 2) {
                if ('POST' === strtoupper($_SERVER['REQUEST_METHOD'])) {
                    $current_user = get_user_by('id', get_current_user_id());
                    $current_email = $current_user->user_email;
                    $account_email = !empty($_POST['account_email']) ? wc_clean($_POST['account_email']) : '';
                    unset($current_user);

                    if (strtolower($account_email) !== strtolower($current_email) && is_object($errors)) {
                        $errors->add('rundizoauth_dontchange_email', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('donotmanuallychangeemail'));
                    }
                }
            }
        }// wcPreventEmailChange


        /**
         * Authenticate with oAuth providers on wp register.
         * 
         * @link https://developer.wordpress.org/reference/hooks/init/ Reference.
         * @global type $rundizoauth_options
         * @deprecated since 1.2 Use /rd-oauth?rdoauth_subpage=register front-end custom page to register user via OAuth instead.
         */
        public function wpInit()
        {
            $this->init();

            global $rundizoauth_options;

            if ($this->useOauth === true) {
                // if choose login method as wp login with oauth or oauth only.
                if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'register') {
                    // if on register page.
                    if (isset($_REQUEST['rdoauth']) && $_REQUEST['rdoauth'] === 'google') {
                        // user choose to register with Google.
                        $Google = new \RundizOauth\App\Libraries\MyOauth\Google();
                        $Google->wpRegisterWithGoogle();
                        unset($Google);
                    } elseif (isset($_REQUEST['rdoauth']) && $_REQUEST['rdoauth'] === 'facebook') {
                        // user choose to register with Facebook.
                        $Facebook = new \RundizOauth\App\Libraries\MyOauth\Facebook();
                        $Facebook->wpRegisterWithFacebook();
                        unset($Facebook);
                    }
                }
            }
        }// wpInit


        /**
         * Logout and remove any cookies that use while register or login.
         * 
         * @link https://codex.wordpress.org/Plugin_API/Action_Reference/wp_logout Reference.
         */
        public function wpLogout()
        {
            $Google = new \RundizOauth\App\Libraries\MyOauth\Google();
            $Google->wpLogoutWithGoogle();
            unset($Google);

            $Facebook = new \RundizOauth\App\Libraries\MyOauth\Facebook();
            $Facebook->wpLogoutWithFacebook();
            unset($Facebook);
        }// wpLogout


        /**
         * Check that plugin setting is using OAuth only and registration option is 'user' then set this value to none.
         * 
         * @param string $active_signup String that returns registration type. 
         *                                              The value can be 'all', 'none', 'blog', or 'user'.
         * @return string
         */
        public function wpSignupActiveValue($active_signup)
        {
            $this->init();

            $active_signup_option = get_site_option('registration', 'none');// 'all', 'none', 'blog', or 'user'

            if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
                if ($active_signup_option === 'user' && $this->useOauth === true && $this->loginMethod === 2) {
                    $active_signup = 'none';
                }
            }

            return $active_signup;
        }// wpSignupActiveValue


        /**
         * Enqueue scripts and styles for wp-signup.php page.
         */
        public function wpSignupEnqueueScripts()
        {
            $this->init();

            wp_enqueue_style('rd-oauth-login', plugin_dir_url(RUNDIZOAUTH_FILE) . 'assets/css/rd-oauth-login.css');
            wp_enqueue_style('font-awesome-4', plugin_dir_url(RUNDIZOAUTH_FILE) . 'assets/css/font-awesome.min.css', [], '4.7.0');
            wp_enqueue_script('rd-oauth-wpsignup', plugin_dir_url(RUNDIZOAUTH_FILE) . 'assets/js/rd-oauth-wpsignup.js', ['jquery'], false, true);
            $active_signup = get_site_option('registration', 'none');// 'all', 'none', 'blog', or 'user'
            wp_localize_script(
                'rd-oauth-wpsignup', 
                'RdOauthRegister', 
                [
                    'active_signup' => $active_signup,
                    'loginMethod' => $this->loginMethod,
                ]
            );
        }// wpSignupEnqueueScripts


    }
}