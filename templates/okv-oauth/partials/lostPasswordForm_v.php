<?php
/**
 * Lost password form.
 * 
 * @package okv-oauth
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
<div class="error-message" id="login_error"><p><?php 
echo wp_kses(
    \OKVOauth\App\Libraries\ErrorsCollection::getErrorMessage('originalforgotpwdisabled'),
    include $okv_oauth_kses_file// phpcs:ignore PEAR.Files.IncludingFile.UseRequire
); 
?></p></div>