<?php
/**
 * Render contents for loginlinks block.
 * 
 * @package rundiz-oauth
 * @since 1.5
 */


$currentUrl = ( is_ssl() ? 'https://' : 'http://' ) . 
    (isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : '') . 
    (isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '');
$isUserLoggedIn = is_user_logged_in();
$classes = ($isUserLoggedIn ? 'logged-in' : 'logged-out');
$wrapperAttributes = get_block_wrapper_attributes(['class' => $classes]);
unset($classes);
$openLine = '<li>';
$closeLine = '</li>';

$contents = '';
$contents .= '<ul ' . $wrapperAttributes . '>';
unset($wrapperAttributes);
// apply filters after open the wrapper.
// @since 1.5.2
$contents .= apply_filters('rdoauth_loginlinkswidgetblock_afteropenwrapper', '', $openLine, $closeLine);

if (!$isUserLoggedIn && get_option('users_can_register')) {
    // if user is not logged in and has option can register.
    $contents .= $openLine . '<a href="' . esc_url( wp_registration_url() ) . '">' . __( 'Register' ) . '</a>' . $closeLine . PHP_EOL;
} elseif ($isUserLoggedIn) {
    // if user logged in.
    // apply filters for logged in users, before display links.
    // @since 1.5.2
    $contents .= apply_filters('rdoauth_loginlinkswidgetblock_loggedin_beforelinks', '', $openLine, $closeLine);
    if (isset($attributes) && is_array($attributes) && array_key_exists('displayLinkToAdmin', $attributes) && true === $attributes['displayLinkToAdmin']) {
        // if block was set to display link to admin dashboard.
        $contents .= $openLine . '<a href="' . admin_url() . '">' . __( 'Site Admin' ) . '</a>' . $closeLine . PHP_EOL;
    }
    if (isset($attributes) && is_array($attributes) && array_key_exists('displayLinkToEditProfile', $attributes) && true === $attributes['displayLinkToEditProfile']) {
        // if block was set to display link to edit profile.
        $contents .= $openLine . '<a href="' . get_edit_user_link() . '">' . __('Edit Profile') . '</a>' . $closeLine . PHP_EOL;
    }
    // apply filters for logged in users, after  display links.
    // @since 1.5.2
    $contents .= apply_filters('rdoauth_loginlinkswidgetblock_loggedin_afterlinks', '', $openLine, $closeLine);
}
unset($isUserLoggedIn);

$contents .= $openLine . wp_loginout($currentUrl, false) . $closeLine . PHP_EOL;
// apply filters after login/logout.
// @since 1.5.2
$contents .= apply_filters('rdoauth_loginlinkswidgetblock_afterloginout', '', $openLine, $closeLine);
unset($currentUrl);
$contents .= '</ul>';

unset($closeLine, $openLine);

// must use echo, not return.
echo  $contents;
// that's all. end of file.