<?php
$oauth_enabled = false;
?>
<div class="rd-oauth-form">
    <noscript><div class="error-message rd-oauth-alert rd-oauth-alert-error">'<?php _e('Please enable javascript.', 'okv-oauth'); ?>'</div></noscript>

    <div class="rd-oauth-links">
        <?php
        if (isset($rundizoauth_options['google_login_enable']) && $rundizoauth_options['google_login_enable'] === '1') {
            // google login enabled.
            $Google = new RundizOauth\App\Libraries\MyOauth\Google();
            $thisOAuthIcon = apply_filters('rundizoauth_google_iconhtml', '<i class="fa fa-google fa-fw"></i>');
        ?> 
        <div class="rd-oauth-link oauth-google"><a class="rd-oauth-button google" href="<?php echo $Google->getAuthUrl(home_url('rd-oauth?rdoauth=google')); ?>"><?php echo $thisOAuthIcon; ?> <?php _e('Login', 'okv-oauth'); ?></a></div>
        <?php
            unset($Google, $thisOAuthIcon);
            $oauth_enabled = true;
        }// endif;
        ?> 
        <?php
        if (isset($rundizoauth_options['facebook_login_enable']) && $rundizoauth_options['facebook_login_enable'] === '1') {
            // facebook login enabled.
            $Facebook = new RundizOauth\App\Libraries\MyOauth\Facebook();
            $thisOAuthIcon = apply_filters('rundizoauth_facebook_iconhtml', '<i class="fa fa-facebook fa-fw"></i>');
        ?> 
        <div class="rd-oauth-link oauth-facebook"><a class="rd-oauth-button facebook" href="<?php echo $Facebook->getAuthUrl(home_url('rd-oauth?rdoauth=facebook')) ?>"><?php echo $thisOAuthIcon; ?> <?php _e('Login', 'okv-oauth'); ?></a></div>
        <?php
            unset($Facebook, $thisOAuthIcon);
            $oauth_enabled = true;
        }// endif;
        ?> 
        <?php
        if (isset($rundizoauth_options['login_method']) && $rundizoauth_options['login_method'] === '1' && $oauth_enabled === true) {
            // use wp login + oauth, display "or".
        ?> 
        <div class="rd-oauth-or-original-wp-login"><?php _e('OR', 'okv-oauth'); ?></div>
        <?php
        }// endif;
        ?> 
    </div><!--.rd-oauth-links-->
</div><!--.rd-oauth-form-->