<div class="rd-oauth-form">
    <h2><?php _e('Rundiz OAuth', 'okv-oauth'); ?></h2>
    <noscript><div class="error-message rd-oauth-alert rd-oauth-alert-error">'<?php _e('Please enable javascript.', 'okv-oauth'); ?>'</div></noscript>
    <?php
    if (isset($_REQUEST['rdoauth-err'])) {
        echo '<div class="error-message rd-oauth-alert rd-oauth-alert-error">';
        echo \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage($_REQUEST['rdoauth-err']);
        echo '</div>';
    }
    ?> 

    <div class="rd-oauth-links-admin">
        <?php
        if (isset($rundizoauth_options['google_login_enable']) && $rundizoauth_options['google_login_enable'] === '1') {
            // google login enabled.
            $Google = new RundizOauth\App\Libraries\MyOauth\Google();
        ?> 
        <div class="rd-oauth-link oauth-google"><a class="rd-oauth-button google" href="<?php echo $Google->getAuthUrl(get_edit_user_link() . '?rdoauth=google'); ?>"><i class="fa fa-google fa-fw"></i> <?php _e('Change email', 'okv-oauth'); ?></a></div>
        <?php
            unset($Google);
        }// endif;
        ?> 
        <?php
        if (isset($rundizoauth_options['facebook_login_enable']) && $rundizoauth_options['facebook_login_enable'] === '1') {
            // facebook login enabled.
            $Facebook = new \RundizOauth\App\Libraries\MyOauth\Facebook();
        ?> 
        <div class="rd-oauth-link oauth-facebook"><a class="rd-oauth-button facebook" href="<?php echo $Facebook->getAuthUrl(get_edit_user_link() . '?rdoauth=facebook'); ?>"><i class="fa fa-facebook fa-fw"></i> <?php _e('Change email', 'okv-oauth'); ?></a></div>
        <?php
            unset($Facebook);
        }// endif;
        ?> 
        <?php
        if (isset($rundizoauth_options['login_method']) && $rundizoauth_options['login_method'] === '1') {
            // use wp login + oauth, display "or".
        ?> 
        <div class="rd-oauth-or-original-wp-login"><?php _e('OR', 'okv-oauth'); ?></div>
        <?php
        }// endif;
        ?> 
    </div><!--.rd-oauth-links-->
</div><!--.rd-oauth-form-->