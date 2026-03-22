<?php
/**
 * Render contents for loginlinks block.
 * 
 * @package okv-oauth
 * @since 1.5
 */


if (!defined('ABSPATH')) {
    exit();
}

$okv_oauth_currentUrl = ( is_ssl() ? 'https://' : 'http://' ) . 
    (isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : '') . 
    (isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '');
$okv_oauth_isUserLoggedIn = is_user_logged_in();
$okv_oauth_classes = ($okv_oauth_isUserLoggedIn ? 'logged-in' : 'logged-out');
$okv_oauth_wrapperAttributes = get_block_wrapper_attributes(['class' => $okv_oauth_classes]);
unset($okv_oauth_classes);
$okv_oauth_openLine = '<li>';
$okv_oauth_closeLine = '</li>';

$okv_oauth_contents = '';
$okv_oauth_contents .= '<ul ' . $okv_oauth_wrapperAttributes . '>';
unset($okv_oauth_wrapperAttributes);
// apply filters after open the wrapper.
// @since 1.5.2
// @since 1.7.0 Renamed from `rdoauth_loginlinkswidgetblock_afteropenwrapper`.
$okv_oauth_contents .= apply_filters('okv_oauth_loginlinkswidgetblock_afteropenwrapper', '', $okv_oauth_openLine, $okv_oauth_closeLine);

if (!$okv_oauth_isUserLoggedIn && get_option('users_can_register')) {
    // if user is not logged in and has option can register.
    $okv_oauth_contents .= $okv_oauth_openLine . '<a href="' . esc_url( wp_registration_url() ) . '">' . esc_html__('Register', 'okv-oauth') . '</a>' . $okv_oauth_closeLine . PHP_EOL;
} elseif ($okv_oauth_isUserLoggedIn) {
    // if user logged in.
    // apply filters for logged in users, before display links.
    // @since 1.5.2
    // @since 1.7.0 Renamed from `rdoauth_loginlinkswidgetblock_loggedin_beforelinks`.
    $okv_oauth_contents .= apply_filters('okv_oauth_loginlinkswidgetblock_loggedin_beforelinks', '', $okv_oauth_openLine, $okv_oauth_closeLine);
    if (isset($attributes) && is_array($attributes) && array_key_exists('displayLinkToAdmin', $attributes) && true === $attributes['displayLinkToAdmin']) {
        // if block was set to display link to admin dashboard.
        $okv_oauth_contents .= $okv_oauth_openLine . '<a href="' . admin_url() . '">' . esc_html__('Site Admin', 'okv-oauth') . '</a>' . $okv_oauth_closeLine . PHP_EOL;
    }
    if (isset($attributes) && is_array($attributes) && array_key_exists('displayLinkToEditProfile', $attributes) && true === $attributes['displayLinkToEditProfile']) {
        // if block was set to display link to edit profile.
        $okv_oauth_contents .= $okv_oauth_openLine . '<a href="' . get_edit_user_link() . '">' . esc_html__('Edit Profile', 'okv-oauth') . '</a>' . $okv_oauth_closeLine . PHP_EOL;
    }
    // apply filters for logged in users, after  display links.
    // @since 1.5.2
    // @since 1.7.0 Renamed from `rdoauth_loginlinkswidgetblock_loggedin_afterlinks`.
    $okv_oauth_contents .= apply_filters('okv_oauth_loginlinkswidgetblock_loggedin_afterlinks', '', $okv_oauth_openLine, $okv_oauth_closeLine);
}
unset($okv_oauth_isUserLoggedIn);

$okv_oauth_contents .= $okv_oauth_openLine . wp_loginout($okv_oauth_currentUrl, false) . $okv_oauth_closeLine . PHP_EOL;
// apply filters after login/logout.
// @since 1.5.2
// @since 1.7.0 Renamed from `rdoauth_loginlinkswidgetblock_afterloginout`.
$okv_oauth_contents .= apply_filters('okv_oauth_loginlinkswidgetblock_afterloginout', '', $okv_oauth_openLine, $okv_oauth_closeLine);
unset($okv_oauth_currentUrl);
$okv_oauth_contents .= '</ul>';

unset($okv_oauth_closeLine, $okv_oauth_openLine);

$okv_oauth_kses_file = dirname(OKVOAUTH_FILE) . '/App/config/kses_data.php';
if (!is_file($okv_oauth_kses_file)) {
    // if not found custom kses data. This use custom kses data to make sure it is up to date with modern HTML elements and attributes that will work.
    // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
    error_log(esc_html('The file ' . str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $okv_oauth_kses_file) . ' could not be found.'));
}
// must use echo, not return.
// not use `require` because if kses data file is not exists, the page must still be able to show.
// phpcs:ignore PEAR.Files.IncludingFile.UseRequire
echo wp_kses($okv_oauth_contents, include $okv_oauth_kses_file);
unset($okv_oauth_kses_file);
// that's all. end of file.
