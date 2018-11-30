/**
 * Rundiz Oauth plugin for WordPress.
 */


jQuery(function($) {
    // display error on lost password form.
    $('#lostpasswordform').find('.error-message').insertBefore('#lostpasswordform');

    // if okvoauth_login_method = 2 (use oauth only)
    if (typeof(RdOauthLostPassword.loginMethod) != 'undefined' && RdOauthLostPassword.loginMethod === '2') {
        // hide default instruction message.
        $('.message').remove();

        $('#lostpasswordform').addClass('oauth-only').hide();
        // remove login form.
        $('#lostpasswordform').find('p:has(label)').remove();
        // remove submit btn
        $('#lostpasswordform').find('.submit').remove();
        // remove register btn
        $('#nav a:last-child').remove();
        $('#nav').html($('#nav a')[0].outerHTML);
    }
});