<?php
/**
 * Render contents for loginlinks block.
 * 
 * @package rundiz-oauth
 */


$currentUrl = ( is_ssl() ? 'https://' : 'http://' ) . 
    (isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : '') . 
    (isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '');
$isUserLoggedIn = is_user_logged_in();
$classes = ($isUserLoggedIn ? 'logged-in' : 'logged-out');
$contents = '';

if (!$isUserLoggedIn && get_option('users_can_register')) {
    $contents .= '<li><a href="' . esc_url(wp_registration_url()) . '">' . __('Register') . '</a></li>' . PHP_EOL;
} elseif ($isUserLoggedIn && isset($attributes) && is_array($attributes) && array_key_exists('displayLinkToAdmin', $attributes) && true === $attributes['displayLinkToAdmin']) {
    $contents .= '<li><a href="' . admin_url() . '">' . __('Site Admin') . '</a></li>' . PHP_EOL;
}
unset($isUserLoggedIn);

$contents .= '<li>' . wp_loginout($currentUrl, false) . '</li>' . PHP_EOL;
unset($currentUrl);

$wrapperAttributes = get_block_wrapper_attributes(['class' => $classes]);

unset($classes);

// must use echo, not return.
echo '<ul ' . $wrapperAttributes . '>' . $contents . '</ul>';
// that's all. end of file.