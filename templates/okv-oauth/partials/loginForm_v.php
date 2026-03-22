<?php
/**
 * Login form template. Use in WordPress core login page.
 * 
 * @package okv-oauth
 * 
 * phpcs:disable Generic.WhiteSpace.ScopeIndent.Incorrect, Generic.WhiteSpace.ScopeIndent.IncorrectExact
 */


if (!defined('ABSPATH')) {
    exit();
}

?>
<div class="rd-oauth-form">
    <noscript><div class="error-message rd-oauth-alert rd-oauth-alert-error">'<?php esc_html_e('Please enable javascript.', 'okv-oauth'); ?>'</div></noscript>

    <div class="rd-oauth-links">
        <?php
        $okv_oauth_OauthProviders = new \OKVOauth\App\Libraries\OAuthProviders();
        $okv_oauth_classes = $okv_oauth_OauthProviders->getClasses($okv_oauth_options);
        unset($okv_oauth_OauthProviders);
        if (is_iterable($okv_oauth_classes)) {
            foreach ($okv_oauth_classes as $okv_oauth_providerKey => $okv_oauth_OAuthClass) {
        ?> 
        <div class="rd-oauth-link oauth-<?= esc_attr($okv_oauth_providerKey); ?>">
            <a class="rd-oauth-button <?= esc_attr($okv_oauth_providerKey); ?>" href="<?php echo esc_url($okv_oauth_OAuthClass->getAuthUrl(home_url('rd-oauth?rdoauth=' . $okv_oauth_providerKey))); ?>">
                <i class="<?php echo esc_attr($okv_oauth_OAuthClass->getIconClasses()); ?>"></i> <?php esc_html_e('Login', 'okv-oauth'); ?>
            </a>
        </div>
        <?php
            }// endforeach;
            unset($okv_oauth_providerKey, $okv_oauth_OAuthClass);
        }// endif; is_iterable
        unset($okv_oauth_classes);
        ?> 
        <?php
        if (isset($okv_oauth_options['login_method']) && '1' === $okv_oauth_options['login_method']) {
            // use wp login + oauth, display "or".
        ?> 
        <div class="rd-oauth-or-original-wp-login"><?php esc_html_e('OR', 'okv-oauth'); ?></div>
        <?php
        }// endif;
        ?> 
    </div><!--.rd-oauth-links-->
</div><!--.rd-oauth-form-->