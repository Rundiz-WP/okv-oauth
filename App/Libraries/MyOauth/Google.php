<?php
/**
 * Google login
 * 
 * @package rundiz-oauth
 */


namespace RundizOauth\App\Libraries\MyOauth;


if (!class_exists('\\RundizOauth\\App\\Libraries\\MyOauth\\Google')) {
    /**
     * Google OAuth class.
     * 
     * @link http://usefulangle.com/post/9/google-login-api-with-php-curl Reference.
     * @link https://developers.google.com/identity/protocols/OpenIDConnect Reference.
     * @link https://developers.google.com/identity/protocols/OAuth2WebServer Reference.
     */
    class Google
    {


        use \RundizOauth\App\AppTrait;


        /**
         * @var resource The cUrl resource.
         */
        protected $ch;


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
         * @link https://developers.google.com/identity/protocols/oauth2/web-server#exchange-authorization-code Reference
         * @param string $code The code got from Google.
         * @param string $redirect_uri Redirect URL.
         * @return mixed Return false on failure.
         */
        protected function getAccessToken($code, $redirect_uri)
        {
            // get all options from setting config file.
            $this->getOptions();

            global $rundizoauth_options;

            if (is_array($rundizoauth_options)) {
                if (
                    array_key_exists('google_login_enable', $rundizoauth_options) &&
                    array_key_exists('google_client_id', $rundizoauth_options) &&
                    array_key_exists('google_client_secret', $rundizoauth_options)
                ) {
                    $this->curlInit();
                    curl_setopt($this->ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
                    curl_setopt($this->ch, CURLOPT_HTTPHEADER, ['Content-type: application/x-www-form-urlencoded']);
                    curl_setopt($this->ch, CURLOPT_POST, true);

                    $postData = 'code=' . rawurlencode($code) .
                        '&client_id=' . rawurlencode($rundizoauth_options['google_client_id']) .
                        '&client_secret=' . rawurlencode($rundizoauth_options['google_client_secret']) .
                        '&redirect_uri=' . rawurlencode($redirect_uri) .
                        '&grant_type=authorization_code';
                    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postData);
                    unset($postData);

                    $result = curl_exec($this->ch);
                    \RundizOauth\App\Libraries\Logger::writeLog('Google OAuth token result:' . PHP_EOL . $result);
                    $result = json_decode($result);

                    curl_close($this->ch);

                    return $result;
                }
            }

            return false;
        }// getAccessToken


        /**
         * Get authenticate URL.
         * 
         * @link https://developers.google.com/identity/protocols/oauth2/web-server#creatingclient Reference
         * @link https://developers.google.com/identity/protocols/oauth2/scopes Available scopes
         * @global array $rundizoauth_options
         * @param string Redirect URL.
         * @return string Return generated URL.
         */
        public function getAuthUrl($redirect_url)
        {
            // get all options from setting config file.
            $this->getOptions();

            global $rundizoauth_options;

            if (is_array($rundizoauth_options)) {
                if (
                    array_key_exists('google_login_enable', $rundizoauth_options) &&
                    array_key_exists('google_client_id', $rundizoauth_options) &&
                    array_key_exists('google_client_secret', $rundizoauth_options)
                ) {
                    $oauth_url = 'https://accounts.google.com/o/oauth2/v2/auth' .
                        '?client_id=' . rawurlencode($rundizoauth_options['google_client_id']) .
                        '&response_type=code' .
                        '&scope=' . rawurlencode('openid https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile') .
                        '&redirect_uri=' . rawurlencode($redirect_url) .
                        '&access_type=online' .
                        '&state=' . rawurlencode(wp_create_nonce('google-login'));
                    if (isset($rundizoauth_options['google_auth_param_prompt']) && !empty($rundizoauth_options['google_auth_param_prompt'])) {
                        $oauth_url .= '&prompt=' . $rundizoauth_options['google_auth_param_prompt'];
                    }

                    if (isset($rundizoauth_options['google_auth_param_other']) && !empty($rundizoauth_options['google_auth_param_other'])) {
                        parse_str(str_replace('&amp;', '&', $rundizoauth_options['google_auth_param_other']), $other_params);
                        if (isset($other_params) && is_array($other_params)) {
                            $skip_param_names = ['client_id', 'response_type', 'scope', 'redirect_uri', 'access_type', 'state', 'prompt'];
                            foreach ($other_params as $name => $value) {
                                if (in_array(strtolower($name), $skip_param_names)) {
                                    unset($other_params[$name]);
                                }
                            }// endforeach;
                            unset($name, $value);

                            if (isset($other_params) && !empty($other_params)) {
                                $oauth_url .= '&' . http_build_query($other_params);
                            }
                        }
                        unset($other_params);
                    }
                    return $oauth_url;
                }
            }

            return ;
        }// getAuthUrl


        /**
         * Get user profile info.
         * 
         * @link https://developers.google.com/oauthplayground/ OAuth API endpoint playground.
         * @param string $access_token The access token got from Google.
         * @return object Return google result object.
         */
        protected function getUserProfileInfo($access_token)
        {
            $this->curlInit();

            curl_setopt($this->ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . rawurlencode($access_token));

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
            if (isset($_REQUEST['code']) && isset($_REQUEST['state'])) {
                // if get code querystring from google, authenticate and get token.
                if (check_admin_referer('google-login', 'state') === 1) {
                    // if verify nonce passed.
                    $result = $this->getAccessToken($_REQUEST['code'], get_edit_user_link() . '?rdoauth=google');
                    if (isset($result->access_token) && isset($result->id_token)) {
                        $access_token = $result->access_token;
                        $id_token = $result->id_token;
                    } elseif (isset($result->error) && is_scalar($result->error)) {
                        // there is errors from Google.
                        unset($access_token, $id_token);
                        return new \WP_Error('rundiz_oauth_invalid_oauth_settings', sprintf(\RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('invalidoauthsettings'), $result->error));
                    } else {
                        // invalid token.
                        unset($access_token, $id_token, $result);
                        return new \WP_Error('rundiz_oauth_invalid_token', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('invalidtoken'));
                    }

                    if (isset($access_token) && isset($id_token)) {
                        $result = $this->validateTokenAndGetAttributes($access_token, $id_token);
                        if (isset($result['result']) && $result['result'] === true) {
                            // if valid token
                            if (
                                isset($result['data']) && 
                                isset($result['data']->email) && 
                                isset($result['data']->email_verified) && 
                                $result['data']->email_verified === 'true' &&
                                isset($result['profileInfo'])
                            ) {
                                // got user profile and email was verified.
                                $user = get_user_by('email', $result['data']->email);
                                if ($user === false && !is_wp_error($user)) {
                                    // not found user by this email.
                                    // success
                                    return $result['data']->email;
                                } else {
                                    // email is already exists.
                                    unset($access_token, $id_token, $result, $user);
                                    return new \WP_Error('rundiz_oauth_email_exists', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('emailalreadyinuse_tryanother'));
                                }
                            } else {
                                unset($access_token, $id_token, $result);
                                return new \WP_Error('rundiz_oauth_email_not_verified', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('emailnotverified'));
                            }
                        } else {
                            unset($access_token, $id_token, $result);
                            return new \WP_Error('rundiz_oauth_invalid_token', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('invalidtoken'));
                        }
                        unset($result);
                    }
                }
            }

            return ;
        }// wpCheckEmailNotExists


        /**
         * Make WordPress login using Google OAuth.
         * 
         * @param null|\WP_User|\WP_Error $user
         * @return null|\WP_User|\WP_Error
         */
        public function wpLoginWithGoogle($user)
        {
            if (isset($_REQUEST['code']) && isset($_REQUEST['state'])) {
                // if get code querystring from google, authenticate and get token.
                if (check_admin_referer('google-login', 'state') === 1) {
                    // if verify nonce passed.
                    $result = $this->getAccessToken($_REQUEST['code'], home_url('rd-oauth?rdoauth=google'));
                    if (isset($result->access_token) && isset($result->id_token)) {
                        $access_token = $result->access_token;
                        $id_token = $result->id_token;
                    } elseif (isset($result->error) && is_scalar($result->error)) {
                        // there is errors from Google.
                        unset($access_token, $id_token);
                        return new \WP_Error('rundiz_oauth_invalid_oauth_settings', sprintf(\RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('invalidoauthsettings'), $result->error));
                    } else {
                        // invalid token.
                        unset($access_token, $id_token, $result);
                        return new \WP_Error('rundiz_oauth_invalid_token', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('invalidtoken'));
                    }
                    unset($result);

                    if (isset($access_token) && isset($id_token)) {
                        $result = $this->validateTokenAndGetAttributes($access_token, $id_token);
                        if (isset($result['result']) && $result['result'] === true) {
                            // if valid token
                            if (
                                isset($result['data']) && 
                                isset($result['data']->email) && 
                                isset($result['data']->email_verified) && 
                                $result['data']->email_verified === 'true' &&
                                isset($result['profileInfo'])
                            ) {
                                // got user profile and email was verified.
                                $user = get_user_by('email', $result['data']->email);
                                if ($user !== false && !is_wp_error($user)) {
                                    // found user by this email.
                                    // keep $user because we will use it as return value.
                                    // set token cookie.
                                    setcookie('rundiz_oauth_google_tokens', json_encode([$access_token, $id_token]), time()+(2 * DAY_IN_SECONDS), '/', defined(COOKIE_DOMAIN) ? COOKIE_DOMAIN : '');
                                    // complete.
                                } else {
                                    // user was not found.
                                    // in case that to create user instead, add the code here.
                                    unset($access_token, $id_token, $result, $user);
                                    return new \WP_Error('rundiz_oauth_user_notfound', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('emailnotfoundinwordpress'));
                                }
                            } else {
                                unset($access_token, $id_token, $result);
                                return new \WP_Error('rundiz_oauth_email_not_verified', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('emailnotverified'));
                            }
                        } else {
                            unset($access_token, $id_token, $result);
                            return new \WP_Error('rundiz_oauth_invalid_token', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('invalidtoken'));
                        }
                        unset($result);
                    }
                }
            }

            return $user;
        }// wpLoginWithGoogle


        /**
         * Remove Google tokens cookie.
         */
        public function wpLogoutWithGoogle()
        {
            setcookie('rundiz_oauth_google_tokens', '', (time()-(365 * DAY_IN_SECONDS)), '/', defined(COOKIE_DOMAIN) ? COOKIE_DOMAIN : '' );
        }// wpLogoutWithGoogle


        /**
         * Make WordPress register user using Google account.
         * 
         * @return null|\WP_Error|array Return error if failed, return array with access_token, id_token, email in keys if success.
         */
        public function wpRegisterWithGoogle()
        {
            if (isset($_REQUEST['code']) && isset($_REQUEST['state'])) {
                // if get code querystring from google, authenticate and get token.
                if (check_admin_referer('google-login', 'state') === 1) {
                    // if verify nonce passed.
                    $result = $this->getAccessToken($_REQUEST['code'], home_url('rd-oauth?rdoauth_subpage=register&rdoauth=google'));
                    if (isset($result->access_token) && isset($result->id_token)) {
                        $access_token = $result->access_token;
                        $id_token = $result->id_token;
                    } elseif (isset($result->error) && is_scalar($result->error)) {
                        unset($access_token, $id_token);
                        return new \WP_Error('rundiz_oauth_invalid_oauth_settings', sprintf(\RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('invalidoauthsettings'), $result->error));
                    } else {
                        unset($access_token, $id_token, $result);
                        return new \WP_Error('rundiz_oauth_invalid_token', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('invalidtoken'));
                    }
                    unset($result);

                    if (isset($access_token) && isset($id_token)) {
                        $result = $this->validateTokenAndGetAttributes($access_token, $id_token);
                        if (isset($result['result']) && $result['result'] === true) {
                            // if validated token
                            if (
                                isset($result['data']) && 
                                isset($result['data']->email) && 
                                isset($result['data']->email_verified) && 
                                $result['data']->email_verified === 'true' &&
                                isset($result['profileInfo'])
                            ) {
                                // got user profile and email was verified.
                                if (email_exists($result['data']->email) === false && username_exists($result['data']->email) === false) {
                                    // if user that is using this email is NOT already exists (yay).
                                    setcookie('rundiz_oauth_google_tokens', json_encode([$access_token, $id_token]), time()+(2 * DAY_IN_SECONDS), '/', defined(COOKIE_DOMAIN) ? COOKIE_DOMAIN : '');
                                    $output['access_token'] = $access_token;
                                    $output['id_token'] = $id_token;
                                    $output['email'] = $result['data']->email;
                                    return $output;
                                } else {
                                    unset($access_token, $id_token, $result);
                                    return new \WP_Error('rundiz_oauth_email_already_inuse', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('emailalreadyinuse'));
                                }
                            } else {
                                unset($access_token, $id_token, $result);
                                return new \WP_Error('rundiz_oauth_email_not_verified', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('emailnotverified'));
                            }
                        } else {
                            unset($access_token, $id_token, $result);
                            return new \WP_Error('rundiz_oauth_invalid_token', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('invalidtoken'));
                        }
                        unset($result);
                    }
                    unset($access_token, $id_token);
                }
            }

            return null;
        }// wpRegisterWithGoogle


        /**
         * Validate token and get attributes.
         * 
         * @link https://developers.google.com/identity/protocols/oauth2/openid-connect#validatinganidtoken Reference
         * @global array $rundizoauth_options
         * @param string $access_token The access token got from Google.
         * @param string $id_token The token ID got from Google.
         * @return array Return result in array.
         */
        protected function validateTokenAndGetAttributes($access_token, $id_token)
        {
            // get all options from setting config file.
            $this->getOptions();

            global $rundizoauth_options;

            $output = [];

            if (is_array($rundizoauth_options)) {
                if (
                    array_key_exists('google_login_enable', $rundizoauth_options) &&
                    array_key_exists('google_client_id', $rundizoauth_options) &&
                    array_key_exists('google_client_secret', $rundizoauth_options)
                ) {
                    $this->curlInit();
                    curl_setopt($this->ch, CURLOPT_URL, 'https://oauth2.googleapis.com/tokeninfo?id_token=' . rawurlencode($id_token));

                    $result = curl_exec($this->ch);
                    \RundizOauth\App\Libraries\Logger::writeLog('Google OAuth validate token: ' . PHP_EOL . $result);
                    $result = json_decode($result);

                    curl_close($this->ch);

                    if (isset($result->error_description)) {
                        $output['result'] = false;
                    } elseif (
                        isset($result->iss) && strpos($result->iss, 'accounts.google.com') !== false &&
                        isset($result->aud) && $result->aud === $rundizoauth_options['google_client_id']
                    ) {
                        $output['result'] = true;
                        $output['data'] = $result;
                        $output['profileInfo'] = $this->getUserProfileInfo($access_token);
                        \RundizOauth\App\Libraries\Logger::writeLog('Google OAuth get attributes: ' . PHP_EOL . print_r($output['profileInfo'], true));
                    } else {
                        $output['result'] = false;
                    }

                    unset($result);
                }
            }

            return $output;
        }// validateTokenAndGetAttributes


    }
}