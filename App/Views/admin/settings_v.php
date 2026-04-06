<?php
/**
 * Setting's view file.
 * 
 * @package okv-oauth
 */


if (!defined('ABSPATH')) {
    exit();
}

?>
<div class="wrap rd-settings-page">
    <h1><?php esc_html_e('Rundiz OAuth settings', 'okv-oauth'); ?></h1>

    <?php 
    if (isset($form_result_class) && isset($form_result_msg)) {
        // phpcs:ignore Squiz.PHP.CommentedOutCode.Found
        /*
        $args = [
            'dismissible' => true,
            'type' => (stripos($form_result_class, 'success') !== false ? 'success' : 'warning'),
        ];
        wp_admin_notice($form_result_msg, $args);
        unset($args);
        */
        // The function `wp_admin_notice()` requires WordPress 6.4.
        // Use normal HTML below is no need `.notice-dismiss` button because it will be append automatically by WordPress's JS.
        // phpcs:ignore Generic.WhiteSpace.ScopeIndent.IncorrectExact
    ?> 
    <div class="notice is-dismissible <?php echo esc_attr($form_result_class); ?>">
        <p><?php echo esc_html($form_result_msg); ?></p>
    </div>
    <?php // phpcs:ignore Generic.WhiteSpace.ScopeIndent.Incorrect
    }
    ?> 

    <form method="post">
        <?php 
        wp_nonce_field(); 
        echo PHP_EOL;

        if (isset($settings_page)) {
            $okv_oauth_kses_data_file = dirname(OKVOAUTH_FILE) . '/App/config/kses_data.php';
            // Use custom kses data to make sure it is up to date with modern HTML elements and attributes that will work.
            if (!is_file($okv_oauth_kses_data_file)) {
                // If not found custom kses data.
                // Throw the exception to notice the developers. Without translation.
                // Because If this happens to a user from an unknown language, assistance may not be possible.
                throw new \Exception(
                    esc_html(
                        sprintf(
                            'The file %1$s could not be found.',
                            str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $okv_oauth_kses_data_file)
                        )
                    )
                );
            }
            echo wp_kses($settings_page, include $okv_oauth_kses_data_file);
        } 

        echo PHP_EOL;
        submit_button(); 
        ?> 
    </form>
</div><!--.wrap .rd-settings-page-->