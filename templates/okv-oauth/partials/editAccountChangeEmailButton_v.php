<div class="rd-oauth-form">
    <noscript><div class="error-message rd-oauth-alert rd-oauth-alert-error">'<?php _e('Please enable javascript.', 'okv-oauth'); ?>'</div></noscript>
    <?php
    if (isset($_REQUEST['rdoauth-err'])) {
        echo '<div class="error-message rd-oauth-alert rd-oauth-alert-error">';
        echo \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage(sanitize_text_field(wp_unslash($_REQUEST['rdoauth-err'])));
        echo '</div>';
    }
    ?> 

    <div class="rd-oauth-links-admin">
        <?php
        if (isset($rundizoauth_options['login_method'])) {
            if ('1' === $rundizoauth_options['login_method'] || '2' === $rundizoauth_options['login_method']) {
                // oauth+wp login (1), oauth only (2)
        ?> 
        <p><?php _e('Change an email by click on the OAuth button and login to OAuth provider with the email you want.', 'okv-oauth'); ?></p>
        <?php
            }// endif;
        }// endif; isset($rundizoauth_options['login_method']);
        ?> 
        <?php
        if (isset($rundizoauth_options['google_login_enable']) && '1' === $rundizoauth_options['google_login_enable']) {
            // google login enabled.
            $Google = new RundizOauth\App\Libraries\MyOauth\Google();
            $thisOAuthIcon = apply_filters('rundizoauth_google_iconhtml', '<i class="fa-brands fa-google fa-fw"></i>');
        ?> 
        <div class="rd-oauth-link oauth-google"><a class="rd-oauth-button google" href="<?php echo $Google->getAuthUrl(get_edit_user_link() . '?rdoauth=google'); ?>"><?php echo $thisOAuthIcon; ?> <?php _e('Change email', 'okv-oauth'); ?></a></div>
        <?php
            unset($Google, $thisOAuthIcon);
        }// endif;
        ?> 
        <?php
        if (isset($rundizoauth_options['facebook_login_enable']) && '1' === $rundizoauth_options['facebook_login_enable']) {
            // facebook login enabled.
            $Facebook = new \RundizOauth\App\Libraries\MyOauth\Facebook();
            $thisOAuthIcon = apply_filters('rundizoauth_facebook_iconhtml', '<i class="fa-brands fa-facebook-f fa-fw"></i>');
        ?> 
        <div class="rd-oauth-link oauth-facebook"><a class="rd-oauth-button facebook" href="<?php echo $Facebook->getAuthUrl(get_edit_user_link() . '?rdoauth=facebook'); ?>"><?php echo $thisOAuthIcon; ?> <?php _e('Change email', 'okv-oauth'); ?></a></div>
        <?php
            unset($Facebook, $thisOAuthIcon);
        }// endif;
        ?> 
        <?php
        if (isset($rundizoauth_options['login_method']) && '1' === $rundizoauth_options['login_method']) {
            // use wp login + oauth, display "or".
        ?> 
        <div class="rd-oauth-or-original-wp-login"><?php _e('OR', 'okv-oauth'); ?></div>
        <?php
        }// endif;
        ?> 
    </div><!--.rd-oauth-links-->
</div><!--.rd-oauth-form-->