<?php
/**
 * RundizSettings configuration file.
 * 
 * @package rundiz-oauth
 */


// generate login expiration values.
$login_expiration_values[''] = __('Use default', 'okv-oauth');
for ($i = 1; $i <= 20; $i++) {
    /* translators: %s: number of days. */
    $login_expiration_values[$i] = sprintf(_n('%s day', '%s days', $i, 'okv-oauth'), $i);
}
for ($i = 30; $i <= 200; $i+=10) {
    /* translators: %s: number of days. */
    $login_expiration_values[$i] = sprintf(_n('%s day', '%s days', $i, 'okv-oauth'), $i);
}
unset($i);


// generate google login help message.
$google_project_url = 'https://console.developers.google.com/cloud-resource-manager';
$google_login_help_msg = sprintf(
        /* translators: %1$s: Open link, %2$s Close link. */
        __('Please visit %1$sGoogle Projects%2$s and create or open your project.', 'okv-oauth'), 
        '<a href="' . $google_project_url . '" target="gg_project">', 
        '</a>'
    ) . "\n" .
    '<ul class="rd-settings-ul">' . "\n" .
        '<li>' . __('Go to APIs &amp; services &gt; Credentials.', 'okv-oauth') . '</li>' . "\n" .
        '<li>' . __('Click on Create credentials &gt; OAuth client ID.', 'okv-oauth') . '</li>' . "\n" .
        '<li>' . __('Select Web application', 'okv-oauth') . '</li>' . "\n" .
        '<li>' .
            sprintf(
                /* translators: %s: URL. */
                __('Authorized JavaScript origins: enter %s', 'okv-oauth'), 
                '<strong>' . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . (isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : '') . '</strong>'
            ) .
            '<br>' .
            __('You may insert one more copy by include both http and https.', 'okv-oauth') .
        '</li>' . "\n" .
        '<li>' .
            sprintf(
                /* translators: %s: URLs. */
                __('Authorized redirect URIs: enter %s', 'okv-oauth'), 
                '<strong>' . home_url('rd-oauth?rdoauth=google') . '</strong><br>' .
                '<strong>' . home_url('rd-oauth?rdoauth_subpage=register&rdoauth=google') . '</strong><br>' .
                '<strong>' . admin_url('profile.php') . '?rdoauth=google</strong><br>' .
                '<strong>' . admin_url('user/profile.php') . '?rdoauth=google</strong>'
            ) .
            '<br>' .
            __('You may insert one more copy by include both http and https.', 'okv-oauth') .
        '</li>' . "\n" .
        '<li>' . __('Use Client ID and Client secret generated from there.', 'okv-oauth') . '</li>' . "\n" .
        '<li>' .
            __('Go to APIs &amp; service &gt; OAuth consent screen and enter your website information there.', 'okv-oauth') .
        '</li>' . "\n" .
    '</ul>' . "\n";
unset($google_project_url);


// generate facebook login help message.
$facebook_apps_url = 'https://developers.facebook.com/apps';
$facebook_login_help_msg = sprintf(
        /* translators: %1$s: Open link, %2$s: Close link */
        __('Please visit %1$sFacebook for developers%2$s and add or open your app.', 'okv-oauth'),
        '<a href="https://developers.facebook.com/apps" target="fb_fordev">',
        '</a>'
    ) . "\n" .
    '<ul class="rd-settings-ul">' . "\n" .
        '<li>' . __('Click +Add a New App, enter the Display Name and click on Create App ID.', 'okv-oauth') . '</li>' .
        '<li>' .
            sprintf(
                /* translators: %1$s: Open strong tag, %2$s: Close strong tag. */
                __('From Add a product section, click Set Up on %1$sFacebook Login%2$s.', 'okv-oauth'), 
                '<strong>', 
                '</strong>'
            ) .
        '</li>' . "\n" .
        '<li>' .
            __('Enable or select Yes for Client OAuth Login, Web OAuth Login, Use Strict Mode for Redirect URIs.', 'okv-oauth') .
        '</li>' . "\n" .
        '<li>' .
            sprintf(
                /* translators: %s: URLs. */
                __('Valid OAuth redirect URIs: enter %s', 'okv-oauth'), 
                '<strong>' . home_url('rd-oauth?rdoauth=facebook') . '</strong><br>' .
                '<strong>' . home_url('rd-oauth?rdoauth_subpage=register&rdoauth=facebook') . '</strong><br>' .
                '<strong>' . admin_url('profile.php') . '?rdoauth=facebook</strong><br>' .
                '<strong>' . admin_url('user/profile.php') . '?rdoauth=facebook</strong>'
            ) .
            '<br>' .
            __('You may insert one more copy by include both http and https.', 'okv-oauth') .
        '</li>' . "\n" .
        '<li>' .
            sprintf(
                /* translators: %s: URL. */
                __('Deauthorize Callback URL: enter %s', 'okv-oauth'), 
                '<strong>' . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . (isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : '') . '</strong>'
            ) .
        '</li>' . "\n" .
        '<li>' . __('Click on Save Changes', 'okv-oauth') . '</li>' . "\n" .
        '<li>' . __('Go to Settings &gt; Basic', 'okv-oauth') . '</li>' . "\n" .
        '<li>' . __('Use App ID and App Secret generated from there.', 'okv-oauth') . '</li>' . "\n" .
        '<li>' . __('You may setup your app info here such as name, icon.', 'okv-oauth') . '</li>' . "\n" .
        '<li>' . __('To make your app ready for public use, please go to App Review menu and change the option there.', 'okv-oauth') . '</li>' . "\n" .
    '</ul>' . "\n" .
    '<p class="rd-settings-notice rd-settings-notice-warning">' . __('Please note that Facebook API is no longer supported by the plugin author.', 'okv-oauth') . '</p>';
unset($facebook_apps_url);


$linenaver_dev_console_url = 'https://developers.line.biz/console/';
$linenaver_login_help_msg = sprintf(
        /* translators: %1$s: Open link, %2$s Close link. */
        __('Please visit %1$sLINE developers console%2$s and create or open your channel.', 'okv-oauth'), 
        '<a href="' . $linenaver_dev_console_url . '" target="line_console">', 
        '</a>'
    ) . "\n" .
    '<ul class="rd-settings-ul">' . "\n" .
        '<li>' . __('Open the LINE Login tab.', 'okv-oauth') . '</li>' . "\n" .
        '<li>' . __('Click Edit on Callback URL.', 'okv-oauth') . '</li>' . "\n" .
        '<li>' .
            sprintf(
                /* translators: %s: URLs. */
                __('Enter %s', 'okv-oauth'), 
                '<strong>' . home_url('rd-oauth?rdoauth=linenaver') . '</strong><br>' .
                '<strong>' . home_url('rd-oauth?rdoauth_subpage=register&rdoauth=linenaver') . '</strong><br>' .
                '<strong>' . admin_url('profile.php') . '?rdoauth=linenaver</strong><br>' .
                '<strong>' . admin_url('user/profile.php') . '?rdoauth=linenaver</strong>'
            ) .
            '<br>' .
            __('You may insert one more copy by include both http and https.', 'okv-oauth') .
        '</li>' . "\n" .
        '<li>' . __('Click Update.', 'okv-oauth') . '</li>' . "\n" .
        '<li>' . __('Open Basic settings tab.', 'okv-oauth') . '</li>' . "\n" .
        '<li>' . __('Use Channel ID and Channel secret from there.', 'okv-oauth') . '</li>' . "\n" .
        '<li>' . sprintf(
            /* translators: %1$s Open link, %2$s Close link. */
            __('For more information, please read more on %1$sthis document%2$s.', 'okv-oauth'),
            '<a href="https://developers.line.biz/en/docs/line-login/integrate-line-login/#create-a-channel" target="linenaver_doc">',
            '</a>'
        ) .
        '</li>' . "\n" .
    '</ul>' . "\n";
unset($linenaver_dev_console_url);


$wpActiveSignup = get_site_option('registration', 'none');// see `signup_user()` function.
$wpsignup_field = [];
if (is_multisite() && 'all' === $wpActiveSignup) {
    $wpsignup_field = [
        'content' => sprintf(
                /* translators: %s: Use OAuth only option text. */
                __('This is multi-site WordPress and the registration page may not work perfectly with method %s.', 'okv-oauth'),
                '<strong>' . __('Use OAuth only', 'okv-oauth') . '</strong>'
            ) .
            '<br>' .
            sprintf(
                /* translators: %1$s: Allow new registrations text, %2$s: User accounts may be registered text. */
                __('It is recommended that you change the network settings for %1$s to %2$s.', 'okv-oauth'),
                '<strong>' . __('Allow new registrations') . '</strong>',
                '<strong>' . __('User accounts may be registered') . '</strong>'
            ),
        'title' => __('Multi-site', 'okv-oauth'),
        'type' => 'html',
    ];
}
unset($wpActiveSignup);


$design_pages_realpath = realpath(plugin_dir_path(RUNDIZOAUTH_FILE) . 'templates');
$design_pages_content = '<p>' . sprintf(
    /* translators: %1$s: The design guide text file name, %2$s: The design guide file location. */
    __('To design your login and register result page, please read %1$s file inside the %2$s folder.', 'okv-oauth'), 
    '<strong>design-guide.txt</strong>', 
    '<strong>' . $design_pages_realpath . '</strong>'
) . '</p>' . PHP_EOL;
if (is_file($design_pages_realpath . DIRECTORY_SEPARATOR . 'design-guide.txt')) {
    $design_pages_textcontent = file_get_contents($design_pages_realpath . DIRECTORY_SEPARATOR . 'design-guide.txt');
} else {
    $design_pages_textcontent = __('File was not found!');
}
unset($design_pages_realpath);
$design_pages_content .= '<p>' . __('Here is the content on text file.', 'okv-oauth') . '</p>' . PHP_EOL;
$design_pages_content .= '<blockquote>' . nl2br(esc_html($design_pages_textcontent)) . '</blockquote>' . PHP_EOL;
unset($design_pages_textcontent);


$GoogleOAuth = new RundizOauth\App\Libraries\MyOauth\Google();
$FacebookOAuth = new RundizOauth\App\Libraries\MyOauth\Facebook();
$LINEOauth = new \RundizOauth\App\Libraries\MyOauth\Line();

return [
    'tab_style' => 'vertical', // vertical or horizontal
    'setting_tabs' => [
        [
            'icon' => 'fa-solid fa-right-to-bracket fa-fw',
            'title' => __('Plugin settings', 'okv-oauth'),
            'fields' => [
                [
                    'default' => '0',
                    'description' => __('If you choose to use OAuth only, please make sure that you have at least one of them enabled and correct setting values otherwise you will be unable to login.', 'okv-oauth'),
                    'id' => 'login_method',
                    'options' => [
                        '0' => __('Do not use (use WordPress login)', 'okv-oauth'),
                        '1' => __('Use WordPress login with OAuth', 'okv-oauth'),
                        '2' => __('Use OAuth only', 'okv-oauth'),
                    ],
                    'title' => __('Login/Register method', 'okv-oauth'),
                    'type' => 'select',
                ],
                $wpsignup_field,
                [
                    'default' => '',
                    /* translators: %s: auth_cookie_expiration hook. */
                    'description' => sprintf(__('This setting can be override by any plugins that hook into %s.', 'okv-oauth'), '<code>auth_cookie_expiration</code>').' '.__('This setting will be use when user remember login or use OAuth only for login method.', 'okv-oauth'),
                    'id' => 'login_expiration',
                    'options' => $login_expiration_values,
                    'title' => __('Login expiration', 'okv-oauth'),
                    'type' => 'select',
                ],
            ],
        ], // end login settings tab.
        [
            'icon' => $GoogleOAuth->getIconClasses(),
            'title' => $GoogleOAuth->getProviderName(),
            'fields' => [
                [
                    'options' => [
                        [
                            'default' => '',
                            'id' => 'google_login_enable',
                            'title' => sprintf(
                                /* translators: %1$s OAuth provider name. */
                                __('Enable login with %1$s', 'okv-oauth'),
                                $GoogleOAuth->getProviderName()
                            ),
                            'value' => '1',
                        ],
                    ],
                    'title' => __('Enable', 'okv-oauth'),
                    'type' => 'checkbox',
                ],
                [
                    'default' => '',
                    'id' => 'google_client_id',
                    'input_attributes' => ['autocomplete' => 'off'],
                    'title' => __('Client ID', 'okv-oauth'),
                    'type' => 'text',
                ],
                [
                    'default' => '',
                    'id' => 'google_client_secret',
                    'input_attributes' => ['autocomplete' => 'off'],
                    'title' => __('Client secret', 'okv-oauth'),
                    'type' => 'text',
                ],
                [
                    'content' => '<h3>' . __('Auth parameters', 'okv-oauth') . ' <small>(<a href="https://developers.google.com/identity/protocols/OpenIDConnect#authenticationuriparameters" target="google_ref">' . __('Reference', 'okv-oauth') . '</a>)</small></h3>',
                    'type' => 'html_full',
                ],
                [
                    'default' => 'select_account+consent',
                    'id' => 'google_auth_param_prompt',
                    'options' => [
                        '' => __('Not set', 'okv-oauth'),
                        'consent' => 'consent',
                        'select_account' => 'select_account',
                        'select_account+consent' => 'select_account+consent (' . __('Default', 'okv-oauth') . ')',
                    ],
                    'title' => __('Prompt', 'okv-oauth'),
                    'type' => 'select',
                ],
                [
                    'default' => '',
                    'description' => sprintf(
                        /* translators: %1$s &amp; text, %2$s: example parameters &amp;a=true&amp;b=false */
                        __('Alway start with %1$s For example: %2$s.', 'okv-oauth'), 
                        '<code>&amp;</code>', 
                        '<code>&amp;include_granted_scopes=true&amp;hd=mydomain.com</code>'
                    ) . '<br>' .
                    sprintf(
                        /* translators: %s: The parameters that will be skipped. */
                        __('These parameters will be skipped: %s', 'okv-oauth'),
                        '<code>client_id</code>, <code>response_type</code>, <code>scope</code>, <code>redirect_uri</code>, <code>access_type</code>, <code>state</code>, <code>prompt</code>'
                    ),
                    'id' => 'google_auth_param_other',
                    'input_attributes' => ['autocomplete' => 'off'],
                    'title' => __('Other parameters', 'okv-oauth'),
                    'type' => 'text',
                ],
                [
                    'content' => $google_login_help_msg,
                    'type' => 'html_full',
                ],
            ],
        ], // end google login settings tab.
        [
            'icon' => $FacebookOAuth->getIconClasses(),
            'title' => $FacebookOAuth->getProviderName(),
            'fields' => [
                [
                    'options' => [
                        [
                            'default' => '',
                            'id' => 'facebook_login_enable',
                            'title' => sprintf(
                                /* translators: %1$s OAuth provider name. */
                                __('Enable login with %1$s', 'okv-oauth'),
                                $FacebookOAuth->getProviderName()
                            ),
                            'value' => '1',
                        ],
                    ],
                    'title' => __('Enable', 'okv-oauth'),
                    'type' => 'checkbox',
                ],
                [
                    'default' => '',
                    'id' => 'facebook_app_id',
                    'input_attributes' => ['autocomplete' => 'off'],
                    'title' => __('App ID', 'okv-oauth'),
                    'type' => 'text',
                ],
                [
                    'default' => '',
                    'id' => 'facebook_app_secret',
                    'input_attributes' => ['autocomplete' => 'off'],
                    'title' => __('App secret', 'okv-oauth'),
                    'type' => 'text',
                ],
                [
                    'content' => $facebook_login_help_msg,
                    'type' => 'html_full',
                ],
            ],
        ], // end facebook login settings tab.
        [
            'icon' => $LINEOauth->getIconClasses(),
            'title' => $LINEOauth->getProviderName(),
            'fields' => [
                [
                    'options' => [
                        [
                            'default' => '',
                            'id' => 'linenaver_login_enable',
                            'title' => sprintf(
                                /* translators: %1$s OAuth provider name. */
                                __('Enable login with %1$s', 'okv-oauth'),
                                $LINEOauth->getProviderName()
                            ),
                            'value' => '1',
                        ],
                    ],
                    'title' => __('Enable', 'okv-oauth'),
                    'type' => 'checkbox',
                ],
                [
                    'default' => '',
                    'id' => 'linenaver_channel_id',
                    'input_attributes' => ['autocomplete' => 'off'],
                    'title' => __('Channel ID', 'okv-oauth'),
                    'type' => 'text',
                ],
                [
                    'default' => '',
                    'id' => 'linenaver_channel_secret',
                    'input_attributes' => ['autocomplete' => 'off'],
                    'title' => __('Channel secret', 'okv-oauth'),
                    'type' => 'text',
                ],
                [
                    'content' => '<h3>' . __('Auth parameters', 'okv-oauth') . ' <small>(<a href="https://developers.line.biz/en/docs/line-login/integrate-line-login/#making-an-authorization-request" target="linenaver_ref">' . __('Reference', 'okv-oauth') . '</a>)</small></h3>',
                    'type' => 'html_full',
                ],
                [
                    'default' => 'none',
                    'id' => 'linenaver_auth_param_prompt',
                    'options' => [
                        '' => __('Not set', 'okv-oauth'),
                        'consent' => 'consent',
                        'none' => 'none',
                        'login' => 'login',
                    ],
                    'title' => __('Prompt', 'okv-oauth'),
                    'type' => 'select',
                ],
                [
                    'default' => '',
                    'description' => sprintf(
                        /* translators: %1$s &amp; text, %2$s: example parameters &amp;a=true&amp;b=false */
                        __('Alway start with %1$s For example: %2$s.', 'okv-oauth'), 
                        '<code>&amp;</code>', 
                        '<code>&amp;a=true&amp;b=false</code>'
                    ) . '<br>' .
                    sprintf(
                        /* translators: %s: The parameters that will be skipped. */
                        __('These parameters will be skipped: %s', 'okv-oauth'),
                        '<code>client_id</code>, <code>response_type</code>, <code>scope</code>, <code>redirect_uri</code>, <code>state</code>, <code>prompt</code>'
                    ),
                    'id' => 'linenaver_auth_param_other',
                    'input_attributes' => ['autocomplete' => 'off'],
                    'title' => __('Other parameters', 'okv-oauth'),
                    'type' => 'text',
                ],
                [
                    'content' => $linenaver_login_help_msg,
                    'type' => 'html_full',
                ],
            ],
        ], // end LINE login settings tab.
        [
            'icon' => 'fa-solid fa-paintbrush fa-fw',
            'title' => __('Design pages', 'okv-oauth'),
            'fields' => [
                [
                    /* translators: %1$s: Text file to read, %2$s: Path to folder. */
                    'content' => $design_pages_content,
                    'type' => 'html_full',
                ],
            ],
        ], // end design help tab.
    ],
];