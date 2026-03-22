<?php
/**
 * Register form.
 * 
 * @package okv-oauth
 * 
 * phpcs:disable Generic.WhiteSpace.ScopeIndent.Incorrect, Generic.WhiteSpace.ScopeIndent.IncorrectExact, WordPress.Security.NonceVerification.Recommended
 */


if (!defined('ABSPATH')) {
    exit();
}

$okv_oauth_kses_file = dirname(OKVOAUTH_FILE) . '/App/config/kses_data.php';
if (!is_file($okv_oauth_kses_file)) {
    // if not found custom kses data. This use custom kses data to make sure it is up to date with modern HTML elements and attributes that will work.
    // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
    error_log(esc_html('The file ' . str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $okv_oauth_kses_file) . ' could not be found.'));
}
?>
<div class="rd-oauth-form">
    <noscript><div class="error-message rd-oauth-alert rd-oauth-alert-error">'<?php esc_html_e('Please enable javascript.', 'okv-oauth'); ?>'</div></noscript>
    <?php
    if (isset($_REQUEST['rdoauth-err'])) {
        echo '<div class="error-message rd-oauth-alert rd-oauth-alert-error">';
        echo wp_kses(
            \OKVOauth\App\Libraries\ErrorsCollection::getErrorMessage(sanitize_text_field(wp_unslash($_REQUEST['rdoauth-err']))),
            include $okv_oauth_kses_file
        );
        echo '</div>';
    }
    ?> 

    <div class="rd-oauth-links">
        <?php
        $okv_oauth_OauthProviders = new \OKVOauth\App\Libraries\OAuthProviders();
        $okv_oauth_classes = $okv_oauth_OauthProviders->getClasses($okv_oauth_options);
        unset($okv_oauth_OauthProviders);
        if (is_iterable($okv_oauth_classes)) {
            foreach ($okv_oauth_classes as $okv_oauth_providerKey => $okv_oauth_OAuthClass) {
        ?> 
        <div class="rd-oauth-link oauth-<?= esc_attr($okv_oauth_providerKey); ?>">
            <a class="rd-oauth-button <?= esc_attr($okv_oauth_providerKey); ?>" href="<?php echo esc_url($okv_oauth_OAuthClass->getAuthUrl(home_url('rd-oauth?rdoauth_subpage=register&rdoauth=' . $okv_oauth_providerKey))); ?>">
                <i class="<?php echo esc_attr($okv_oauth_OAuthClass->getIconClasses()); ?>"></i> <?php esc_html_e('Register', 'okv-oauth'); ?>
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