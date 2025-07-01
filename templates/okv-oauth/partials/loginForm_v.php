<?php
$oauth_enabled = false;
?>
<div class="rd-oauth-form">
    <noscript><div class="error-message rd-oauth-alert rd-oauth-alert-error">'<?php _e('Please enable javascript.', 'okv-oauth'); ?>'</div></noscript>

    <div class="rd-oauth-links">
        <?php
        $OauthProviders = new \RundizOauth\App\Libraries\OAuthProviders();
        $classes = $OauthProviders->getClasses($rundizoauth_options);
        unset($OauthProviders);
        if (is_iterable($classes)) {
            foreach ($classes as $providerKey => $OAuthClass) {
        ?> 
        <div class="rd-oauth-link oauth-<?= $providerKey; ?>">
            <a class="rd-oauth-button <?= $providerKey; ?>" href="<?php echo $OAuthClass->getAuthUrl(home_url('rd-oauth?rdoauth=' . $providerKey)); ?>">
                <i class="<?php echo $OAuthClass->getIconClasses(); ?>"></i> <?php _e('Login', 'okv-oauth'); ?>
            </a>
        </div>
        <?php
            }// endforeach;
            unset($providerKey, $OAuthClass);
        }// endif; is_iterable
        unset($classes);
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