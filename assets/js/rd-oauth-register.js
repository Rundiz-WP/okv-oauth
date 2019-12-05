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
        $('#registerform').addClass('oauth-only');
        // remove register form
        $('#registerform').find('p:has(label)').remove();
        // remove register message (register confirmation will be ...), button.
        $('#registerform').find('#reg_passmail, .clear, .submit').remove();

        // remove forgot password link.
        $('#nav a').each(function() {
            if (this.href && this.href.indexOf('lostpassword') !== -1) {
                this.remove();
            }
        });
        // trim last character.
        // example: "Login |" will be "Login"
        // https://stackoverflow.com/a/32516190/128761 original source code.
        let navHtml = ($('#nav').html()).trim();
        let trimLastSep = navHtml.replace(/^\|+|\|+$/g, '');
        $('#nav').html(trimLastSep);
    }
});