<?php
/**
 * LINE (Naver)
 * 
 * @package rundiz-oauth
 * @since 1.5.7
 */


namespace RundizOauth\App\Libraries\MyOauth;


if (!class_exists('\\RundizOauth\\App\\Libraries\\MyOauth\\Line')) {
    /**
     * LINE OAuth class.
     * 
     * @since 1.5.7
     */
    class Line implements Interfaces\MyOAuthInterface
    {


        use \RundizOauth\App\AppTrait;


        /**
         * Verify code and get access token.
         * 
         * @link https://developers.line.biz/en/docs/line-login/integrate-line-login/#get-access-token Reference.
         * @param string $code The code got from LINE.
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
                    array_key_exists('linenaver_login_enable', $rundizoauth_options) &&
                    array_key_exists('linenaver_channel_id', $rundizoauth_options) &&
                    array_key_exists('linenaver_channel_secret', $rundizoauth_options)
                ) {
                    $postData = 'code=' . rawurlencode($code) .
                        '&grant_type=authorization_code' .
                        '&redirect_uri=' . rawurlencode($redirect_uri) .
                        '&client_id=' . rawurlencode($rundizoauth_options['linenaver_channel_id']) .
                        '&client_secret=' . rawurlencode($rundizoauth_options['linenaver_channel_secret']);

                    $remoteArgs = [
                        'headers' => 'Content-type: application/x-www-form-urlencoded',
                        'body' => $postData,
                    ];
                    unset($postData);
                    $response = wp_remote_post('https://api.line.me/oauth2/v2.1/token', $remoteArgs);
                    unset($remoteArgs);
                    $result = wp_remote_retrieve_body($response);
                    unset($response);
                    \RundizOauth\App\Libraries\Logger::writeLog('LINE OAuth token result:' . PHP_EOL . $result);
                    $result = json_decode($result);

                    return $result;
                }
            }

            return false;
        }// getAccessToken


        /** phpcs:ignore Squiz.Commenting.FunctionComment.MissingParamTag
         * {@inheritDoc}
         */
        public function getAuthUrl($redirect_url)
        {
            // get all options from setting config file.
            $this->getOptions();

            global $rundizoauth_options;

            if (is_array($rundizoauth_options)) {
                if (
                    array_key_exists('linenaver_login_enable', $rundizoauth_options) &&
                    array_key_exists('linenaver_channel_id', $rundizoauth_options)
                ) {
                    $oauth_url = 'https://access.line.me/oauth2/v2.1/authorize' .
                        '?response_type=code' .
                        '&client_id=' . rawurlencode($rundizoauth_options['linenaver_channel_id']) .
                        '&redirect_uri=' . rawurlencode($redirect_url) .
                        '&state=' . rawurlencode(wp_create_nonce('linenaver-login')) .
                        '&scope=' . rawurlencode('profile openid email');
                    if (isset($rundizoauth_options['linenaver_auth_param_prompt']) && !empty($rundizoauth_options['linenaver_auth_param_prompt'])) {
                        $oauth_url .= '&prompt=' . $rundizoauth_options['linenaver_auth_param_prompt'];
                    }

                    if (isset($rundizoauth_options['linenaver_auth_param_other']) && !empty($rundizoauth_options['linenaver_auth_param_other'])) {
                        parse_str(str_replace('&amp;', '&', $rundizoauth_options['linenaver_auth_param_other']), $other_params);
                        if (isset($other_params) && is_array($other_params)) {
                            $skip_param_names = ['client_id', 'response_type', 'scope', 'redirect_uri', 'state', 'prompt'];
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

            return '';
        }// getAuthUrl


        /**
         * {@inheritDoc}
         */
        public function getIconClasses()
        {
            return 'fa-brands fa-line fa-fw';
        }// getIconClasses


        /**
         * {@inheritDoc}
         */
        public function getProviderName()
        {
            return __('LINE', 'okv-oauth');
        }// getProviderName


        /**
         * Get user profile info.
         * 
         * @link https://developers.line.biz/en/docs/line-login/verify-id-token/#get-profile-info-from-id-token Reference.
         * @param string $id_token The ID token got from LINE.
         * @return object Return LINE result object.
         */
        protected function getUserProfileInfo($id_token)
        {
            // get all options from setting config file.
            $this->getOptions();

            global $rundizoauth_options;

            $output = [];

            if (is_array($rundizoauth_options)) {
                $postData = 'id_token=' . $id_token .
                    '&client_id=' . rawurlencode($rundizoauth_options['linenaver_channel_id']);

                $remoteArgs = [
                    'headers' => 'Content-type: application/x-www-form-urlencoded',
                    'body' => $postData,
                ];
                unset($postData);
                $response = wp_remote_post('https://api.line.me/oauth2/v2.1/verify', $remoteArgs);
                unset($remoteArgs);
                $result = wp_remote_retrieve_body($response);
                unset($response);
                $result = json_decode($result);

                if (!is_object($result)) {
                    return new \stdClass();
                }

                return $result;
            }

            return $output;
        }// getUserProfileInfo


        /**
         * {@inheritDoc}
         */
        public function wpCheckEmailNotExists()
        {
            if (isset($_REQUEST['code']) && isset($_REQUEST['state'])) {
                // if get code querystring from line, authenticate and get token.
                if (check_admin_referer('linenaver-login', 'state') === 1) {
                    // if verify nonce passed.
                    $result = $this->getAccessToken(sanitize_text_field(wp_unslash($_REQUEST['code'])), get_edit_user_link() . '?rdoauth=linenaver');
                    if (isset($result->access_token) && isset($result->id_token)) {
                        $access_token = $result->access_token;
                        $id_token = $result->id_token;
                    } elseif (isset($result->error) && is_scalar($result->error)) {
                        // there is errors from LINE.
                        unset($access_token, $id_token);
                        return new \WP_Error('rundiz_oauth_invalid_oauth_settings', sprintf(\RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('invalidoauthsettings'), $result->error));
                    } else {
                        // invalid token.
                        unset($access_token, $id_token, $result);
                        return new \WP_Error('rundiz_oauth_invalid_token', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('invalidtoken'));
                    }

                    if (isset($id_token)) {
                        $result = $this->getUserProfileInfo($id_token);
                        if (isset($result->email)) {
                            // got user profile and email was verified.
                            $user = get_user_by('email', $result->email);
                            if (false === $user && !is_wp_error($user)) {
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
                }// endif; there is no state from service provider. (or verify nonce failed.)
            }// endif; there is no code and state from service provider.

            return ;
        }// wpCheckEmailNotExists


        /**
         * {@inheritDoc}
         */
        public function wpLoginUseOAuth()
        {
            $user = null;

            if (isset($_REQUEST['code']) && isset($_REQUEST['state'])) {
                // if get code querystring from LINE, authenticate and get token.
                if (check_admin_referer('linenaver-login', 'state') === 1) {
                    // if verify nonce passed.
                    $result = $this->getAccessToken(sanitize_text_field(wp_unslash($_REQUEST['code'])), home_url('rd-oauth?rdoauth=linenaver'));
                    if (isset($result->access_token) && isset($result->id_token)) {
                        $access_token = $result->access_token;
                        $id_token = $result->id_token;
                    } elseif (isset($result->error) && is_scalar($result->error)) {
                        // there is errors from LINE.
                        unset($access_token, $id_token);
                        return new \WP_Error('rundiz_oauth_invalid_oauth_settings', sprintf(\RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('invalidoauthsettings'), $result->error));
                    } else {
                        // invalid token.
                        unset($access_token, $id_token, $result);
                        return new \WP_Error('rundiz_oauth_invalid_token', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('invalidtoken'));
                    }
                    unset($result);

                    if (isset($access_token) && isset($id_token)) {
                        $result = $this->getUserProfileInfo($id_token);
                        if (isset($result->email)) {
                            // got user profile and verified.
                            $user = get_user_by('email', $result->email);
                            if (false !== $user && !is_wp_error($user)) {
                                // found user by this email.
                                // keep $user because we will use it as return value.
                                // set token cookie.
                                setcookie('rundiz_oauth_linenaver_tokens', $access_token, time()+(2 * DAY_IN_SECONDS), '/', defined(COOKIE_DOMAIN) ? COOKIE_DOMAIN : '');
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
                }// endif; there is no state from service provider. (or verify nonce failed.)
            } elseif (isset($_REQUEST['error'])) {
                // if there is an error returned from OAuth provider.
                switch ($_REQUEST['error']) {
                    case 'INVALID_REQUEST':
                        return new \WP_Error('rundiz_oauth_invalid_oauth_settings', sprintf(\RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('invalidoauthsettings'), $result->error));
                    case 'ACCESS_DENIED':
                        return new \WP_Error('rundiz_oauth_linenaver_accessdenied', __('You have cenceled.', 'okv-oauth'));
                    case 'INVALID_SCOPE':
                        return new \WP_Error('rundiz_oauth_linenavar_returnerror', __('Invalid scope. Please notify the plugin developer.', 'okv-oauth'));
                    case 'SERVER_ERROR':
                        return new \WP_Error('rundiz_oauth_linenavar_returnerror', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('tryagain'));
                    case 'LOGIN_REQUIRED':
                    case 'INTERACTION_REQUIRED':
                        return new \WP_Error('rundiz_oauth_linenaver_returnerror', __('Auto login could not work.', 'okv-oauth'));
                    default:
                        if (isset($_REQUEST['error_description'])) {
                            return new \WP_Error('rundiz_oauth_linenaver_unknown_error', wp_strip_all_tags(sanitize_text_field(wp_unslash($_REQUEST['error_description']))));
                        } else {
                            return new \WP_Error('rundiz_oauth_linenaver_unknown_error', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('tryagain'));
                        }
                }
            }// endif; there is no code and state from service provider.

            return $user;
        }// wpLoginUseOAuth


        /**
         * {@inheritDoc}
         */
        public function wpLogoutUseOAuth()
        {
            setcookie('rundiz_oauth_linenaver_tokens', '', (time()-(365 * DAY_IN_SECONDS)), '/', defined(COOKIE_DOMAIN) ? COOKIE_DOMAIN : '' );
        }// wpLogoutUseOAuth


        /**
         * {@inheritDoc}
         */
        public function wpRegisterUseOAuth()
        {
            if (isset($_REQUEST['error'])) {
                return new \WP_Error('rundiz_oauth_invalid_token', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('tryagain'));
            }

            if (isset($_REQUEST['code']) && isset($_REQUEST['state'])) {
                // if get code querystring from LINE, authenticate and get token.
                if (check_admin_referer('linenaver-login', 'state') === 1) {
                    // if verify nonce passed.
                    $result = $this->getAccessToken(sanitize_text_field(wp_unslash($_REQUEST['code'])), home_url('rd-oauth?rdoauth_subpage=register&rdoauth=linenaver'));
                    if (isset($result->access_token) && isset($result->id_token)) {
                        $access_token = $result->access_token;
                        $id_token = $result->id_token;
                    } elseif (isset($result->error) && is_scalar($result->error)) {
                        unset($access_token, $id_token);
                        return new \WP_Error('rundiz_oauth_invalid_token', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('tryagain'));
                    } else {
                        unset($access_token, $id_token, $result);
                        return new \WP_Error('rundiz_oauth_invalid_token', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('invalidtoken'));
                    }
                    unset($result);

                    if (isset($access_token) && isset($id_token)) {
                        $result = $this->getUserProfileInfo($id_token);
                        if (isset($result->email)) {
                            // got user profile and verified.
                            if (email_exists($result->email) === false && username_exists($result->email) === false) {
                                // if user that is using this email is NOT already exists (yay).
                                setcookie('rundiz_oauth_linenaver_tokens', $access_token, time()+(2 * DAY_IN_SECONDS), '/', defined(COOKIE_DOMAIN) ? COOKIE_DOMAIN : '');
                                $output['access_token'] = $access_token;
                                $output['email'] = $result->email;
                                return $output;
                            } else {
                                unset($access_token, $id_token, $result);
                                return new \WP_Error('rundiz_oauth_email_already_inuse', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('emailalreadyinuse'));
                            }
                        } else {
                            unset($access_token);
                            if (isset($result->error)) {
                                return new \WP_Error('rundiz_oauth_linenaver_unknown_error', \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('tryagain'));
                            } else {
                                return new \WP_Error('rundiz_oauth_linenaver_no_email', __('Your LINE account have no email available. Please register and confirm your email with LINE before continue.', 'okv-oauth'));
                            }
                        }
                        unset($result);
                    }
                    unset($access_token, $id_token);
                }// endif; there is no state from service provider. (or verify nonce failed.)
            }// endif; there is code and state from service provider.

            return null;
        }// wpRegisterUseOAuth


    }// Line
}// endif;
