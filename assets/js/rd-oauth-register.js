/**
 * Rundiz Oauth plugin for WordPress.
 */


jQuery(function($) {
    // move those social login btn to top.
    $('#registerform').find('.rd-oauth-form').prependTo('#registerform');
    // move error message to below generic message.
    $('#registerform').find('.error-message').insertAfter($('.message'));

    if (RdOauthRegister.loginMethod === '1') {
        // use wp login + oauth.
    } else if (RdOauthRegister.loginMethod === '2') {
        // use oauth only.
        // remove register form
        $('#registerform').find('p:has(label)').remove();
        // remove register message (register confirmation will be ...), button.
        $('#registerform').find('#reg_passmail, .clear, .submit').remove();
        // remove forgot password link.
        $('#nav a:last-child').remove();
        $('#nav').html($('#nav a')[0].outerHTML);

        $('#registerform').addClass('oauth-only');
    }
});