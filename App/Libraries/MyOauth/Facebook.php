<?php
/**
 * Facebook login
 * 
 * @package rundiz-oauth
 */


namespace RundizOauth\App\Libraries\MyOauth;

if (!class_exists('\\RundizOauth\\App\\Libraries\\MyOauth\\Facebook')) {
    /**
     * Facebook OAuth class.
     * 
     * @link https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow Reference.
     * @link https://developers.facebook.com/docs/graph-api/securing-requests Reference.
     * @link https://developers.facebook.com/docs/facebook-login/security Reference.
     * @link https://developers.facebook.com/docs/facebook-login/web Reference.
     * @link https://developers.facebook.com/docs/facebook-login/handling-declined-permissions Reference.
     * @link https://developers.facebook.com/docs/facebook-login/reauthentication Reference.
     * @link https://developers.facebook.com/tools/explorer/145634995501895/ Tools.
     */
    class Facebook
    {


        use \RundizOauth\App\AppTrait;


        /**
         * @var resource The cUrl resource.
         */
        protected $ch;


        /**
         * @link https://developers.facebook.com/docs/facebook-login/permissions/ Reference for scopes.
         * @var array The required scopes.
         */
        protected $requiredScopes = ['public_profile', 'email'];


        /**
         * Initialize cURL and set common option.
         */
        protected function curlInit()
        {
            $this->ch = curl_init();

            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        }// curlInit


        /**
         * Verify code and get access token.
         * 
         * @global array $rundizoauth_options
         * @param string $code The code got from Facebook.
         * @param string $redirect_uri Redirect URI.
         * @return mixed Return false on failure, return object on success.
         */
        protected function getAccessToken($code, $redirect_uri)
        {
            // get all options from setting config file.
            $this->getOptions();

            global $rundizoauth_options;

            if (is_array($rundizoauth_options)) {
                if (
                    array_key_exists('facebook_login_enable', $rundizoauth_options) &&
                    array_key_exists('facebook_app_id', $rundizoauth_options) &&
                    array_key_exists('facebook_app_secret', $rundizoauth_options)
                ) {
                    $this->curlInit();
                    $oauth_url = 'https://graph.facebook.com/v3.1/oauth/access_token' .
                        '?client_id=' . urlencode($rundizoauth_options['facebook_app_id']) .
                        '&redirect_uri=' . urlencode($redirect_uri) .
                        '&client_secret=' . urlencode($rundizoauth_options['facebook_app_secret']) .
                        '&code=' . urlencode($code);
                    curl_setopt($this->ch, CURLOPT_URL, $oauth_url);
                    unset($oauth_url);

                    $result = curl_exec($this->ch);
                    $result = json_decode($result);

                    curl_close($this->ch);

                    if (is_object($result) && isset($result->access_token)) {
                        $result->permissions = $this->getPermissions($result->access_token, $rundizoauth_options['facebook_app_secret']);
                        if (isset($result->permissions->data) && is_array($result->permissions->data)) {
                            $verifyScopes = false;
                            foreach ($result->permissions->data as $key => $item) {
                                if (
                                    isset($item->permission) && 
                                    isset($item->status) && 
                                    is_scalar($item->permission) &&
                                    in_array($item->permission, $this->requiredScopes) &&
                                    strtolower($item->status) === 'granted'
                                ) {
                                    $verifyScopes = true;
                                } else {
                                    $verifyScopes = false;
                                    break;
                                }
                            }// endforeach;
                            unset($item, $key);

                            if ($verifyScopes !== true) {
                                $result->access_token = null;
                                $result->rdoauth_error = 'missingrequiredscope';
                            }
                        }
                    } else {
                        if (isset($result->error)) {
                            $result->rdoauth_error = 'invalidoauthsettings';
                        }
                    }

                    return $result;
                }
            }

            return false;
        }// getAccessToken


        /**
         * Get authenticate URL.
         * 
         * @global array $rundizoauth_options
         * @param string $redirect_uri Redirect URL.
         * @return string Return generated URL.
         */
        public function getAuthUrl($redirect_uri)
        {
            // get all options from setting config file.
            $this->getOptions();

            global $rundizoauth_options;

            if (is_array($rundizoauth_options)) {
                if (
                    array_key_exists('facebook_login_enable', $rundizoauth_options) &&
                    array_key_exists('facebook_app_id', $rundizoauth_options) &&
                    array_key_exists('facebook_app_secret', $rundizoauth_options)
                ) {
                    $oauth_url = 'https://www.facebook.com/v3.1/dialog/oauth' .
                        '?client_id=' . urlencode($rundizoauth_options['facebook_app_id']) .
                        '&auth_type=rerequest' .
                        '&response_type=code' .
                        '&scope=email' .
                        '&redirect_uri=' . urlencode($redirect_uri) .
                        '&state=' . urlencode(wp_create_nonce('facebook-login'));
                    return $oauth_url;
                }
            }

            return ;
        }// getAuthUrl


        /**
         * Get permissions
         * 
         * This method was called from `getAccessToken()`.
         * 
         * @param string $access_token
         * @param string $app_secret
         * @return object
         */
        private function getPermissions($access_token, $app_secret)
        {
            $this->curlInit();

            $oauth_url = 'https://graph.facebook.com/me/permissions' .
                '?access_token=' . urlencode($access_token) .
                '&appsecret_proof=' . urlencode(hash_hmac('sha256', $access_token, $app_secret));
            curl_setopt($this->ch, CURLOPT_URL, $oauth_url);
            unset($oauth_url);

            $result = curl_exec($this->ch);
            $result = json_decode($result);

            curl_close($this->ch);

            return $result;
        }// getPermissions


        /**
         * Get user profile info.
         * 
         * @global array $rundizoauth_options
         * @param string $access_token The access token got from Facebook.
         * @return object Return Facebook object result.
         */
        protected function getUserProfileInfo($access_token)
        {
            // get all options from setting config file.
            $this->getOptions();

            global $rundizoauth_options;

            $this->curlInit();

            $oauth_url = 'https://graph.facebook.com/v3.1/me' .
                '?fields=id,name,email,picture.width(2000).height(2000)' .
                '&access_token=' . urlencode($access_token) .
                '&appsecret_proof=' . urlencode(hash_hmac('sha256', $access_token, (isset($rundizoauth_options['facebook_app_secret']) ? $rundizoauth_options['facebook_app_secret'] : '')));
            // fields that were removed due to deprecated: verified.
            curl_setopt($this->ch, CURLOPT_URL, $oauth_url);
            unset($oauth_url);

            $result = curl_exec($this->ch);
            $result = json_decode($result);

            curl_close($this->ch);

            return $result;
        }// getUserProfileInfo


        /**
         * Check that authorised OAuth provider's email is not exists in the WordPress system.
         * 
         * @return \WP_Error|string|null Return error message on failed to validate, return email string if validate passed.
         */
        public function wpCheckEmailNotExists()
        {
            if (isset($_REQUEST['code'])) {
                // if get code querystring from facebook, authenticate and get token.
                if (check_admin_referer('facebook-login', 'state') === 1) {
                    // if verify nonce passed.
                    $result = $this->getAccessToken($_REQUEST['code'], get_edit_user_link() . '?rdoauth=facebook');
                    if (isset($result->access_token)) {
                        $access_token = $result->access_token;
                    } elseif (isset($result->error) && is_scalar($result->error)) {
                        // there is errors from Facebook.
                        unset($access_token);
                        return new \WP_Error('rundiz_oauth_invalid_oauth_settings', sprintf(\RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('invalidoauthsettings'), $result->error));
                    } else {
                        // invalid token.
                        unset($access_token, $result);
                        return new \WP_Error('rundiz_oauth_invalid_token', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('invalidtoken'));
                    }

                    if (isset($access_token)) {
                        $result = $this->getUserProfileInfo($access_token);
                        if (isset($result->id) && isset($result->email)) {
                            // got user profile and verified.
                            // verified property (field) were removed in v3.1
                            $user = get_user_by('email', $result->email);
                            if ($user === false && !is_wp_error($user)) {
                                // not found user by this email.
                                // success
                                return $result->email;
                            } else {
                                // email is already exists.
                                unset($access_token, $result, $user);
                                    return new \WP_Error('rundiz_oauth_email_exists', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('emailalreadyinuse_tryanother'));
                            }
                        } else {
                            unset($access_token, $result);
                            return new \WP_Error('rundiz_oauth_user_notverified', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('usernotverified'));
                        }
                        unset($result);
                    }
                }
            }

            return ;
        }// wpCheckEmailNotExists


        /**
         * Make WordPress login using Facebook OAuth.
         * 
         * @param null|WP_User|WP_Error $user
         * @return null|WP_User|WP_Error
         */
        public function wpLoginWithFacebook($user)
        {
            if (isset($_REQUEST['error'])) {
                return new \WP_Error('rundiz_oauth_tryagain', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('tryagain'));
            }

            if (isset($_REQUEST['code'])) {
                // if get code querystring from fb, authenticate and get token.
                if (check_admin_referer('facebook-login', 'state') === 1) {
                    // if verify nonce passed.
                    $result = $this->getAccessToken($_REQUEST['code'], home_url('rd-oauth?rdoauth=facebook'));
                    if (isset($result->access_token)) {
                        $access_token = $result->access_token;
                    } elseif (isset($result->rdoauth_error)) {
                        return new \WP_Error('rundiz_oauth_autherror', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage($result->rdoauth_error));
                    } else {
                        unset($access_token, $result);
                        return new \WP_Error('rundiz_oauth_invalid_token', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('invalidtoken'));
                    }
                    unset($result);

                    if (isset($access_token)) {
                        $result = $this->getUserProfileInfo($access_token);
                        if (isset($result->id) && isset($result->email)) {
                            // got user profile and verified.
                            // verified property (field) were removed in v3.1
                            $user = get_user_by('email', $result->email);
                            if ($user !== false && !is_wp_error($user)) {
                                // found user by this email.
                                // keep $user because we will use it as return value.
                                // set token cookie.
                                setcookie('rundiz_oauth_facebook_tokens', $access_token, time()+(2 * DAY_IN_SECONDS), '/', defined(COOKIE_DOMAIN) ? COOKIE_DOMAIN : '');
                                // complete.
                            } else {
                                // user was not found.
                                // in case that to create user instead, add the code here.
                                unset($access_token, $result, $user);
                                return new \WP_Error('rundiz_oauth_user_notfound', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('emailnotfoundinwordpress'));
                            }
                        } else {
                            unset($access_token, $result);
                            return new \WP_Error('rundiz_oauth_user_notverified', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('usernotverified'));
                        }
                        unset($result);
                    }
                    unset($access_token);
                }
            }

            return $user;
        }// wpLoginWithFacebook


        /**
         * Remove Facebook tokens cookie.
         */
        public function wpLogoutWithFacebook()
        {
            setcookie('rundiz_oauth_facebook_tokens', '', (time()-(365 * DAY_IN_SECONDS)), '/', defined(COOKIE_DOMAIN) ? COOKIE_DOMAIN : '' );
        }// wpLogoutWithFacebook


        /**
         * Make WordPress register user using Facebook account.
         * 
         * @return null|\WP_Error|array Return error if failed, return array with access_token, email in keys if success.
         */
        public function wpRegisterWithFacebook()
        {
            if (isset($_REQUEST['error'])) {
                return new \WP_Error('rundiz_oauth_invalid_token', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('tryagain'));
            }

            if (isset($_REQUEST['code'])) {
                // if get code querystring from fb, authenticate and get token.
                if (check_admin_referer('facebook-login', 'state') === 1) {
                    // if verify nonce passed.
                    $result = $this->getAccessToken($_REQUEST['code'], home_url('rd-oauth?rdoauth_subpage=register&rdoauth=facebook'));
                    if (isset($result->access_token)) {
                        $access_token = $result->access_token;
                    } elseif (isset($result->rdoauth_error)) {
                        unset($access_token);
                        return new \WP_Error('rundiz_oauth_err', $result->rdoauth_error);
                    } else {
                        unset($access_token, $result);
                        return new \WP_Error('rundiz_oauth_invalid_token', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('invalidtoken'));
                    }
                    unset($result);

                    if (isset($access_token)) {
                        $result = $this->getUserProfileInfo($access_token);
                        if (isset($result->id) && isset($result->email)) {
                            // got user profile and verified.
                            // verified property (field) were removed in v3.1
                            if (email_exists($result->email) === false && username_exists($result->email) === false) {
                                // if user that is using this email is NOT already exists (yay).
                                setcookie('rundiz_oauth_facebook_tokens', $access_token, time()+(2 * DAY_IN_SECONDS), '/', defined(COOKIE_DOMAIN) ? COOKIE_DOMAIN : '');
                                $output['access_token'] = $access_token;
                                $output['email'] = $result->email;
                                return $output;
                            } else {
                                unset($access_token, $result);
                                return new \WP_Error('rundiz_oauth_email_already_inuse', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('emailalreadyinuse'));
                            }
                        } else {
                            unset($access_token, $result);
                            return new \WP_Error('rundiz_oauth_invalid_token', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('usernotverified'));
                        }
                        unset($result);
                    }
                    unset($access_token);
                }
            }
        }// wpRegisterWithFacebook


    }
}