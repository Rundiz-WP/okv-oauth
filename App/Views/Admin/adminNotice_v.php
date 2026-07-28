<?php
/**
 * Admin notice to display notice message on admin pages.
 * 
 * @package okv-oauth
 * 
 * phpcs:disable Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace
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
<div class="notice <?php if (isset($class)) {echo esc_attr($class);} ?> is-dismissible" >
        <p><?php 
        if (isset($message)) {
            echo wp_kses($message, include $okv_oauth_kses_file);
        } 
        ?></p>
</div>