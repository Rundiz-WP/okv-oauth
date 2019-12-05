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

        // remove register link
        /*$('#nav a').each(function() {
            if (this.href && this.href.indexOf('register') !== -1) {
                this.remove();
            }
        });
        // trim last character.
        // example: "Login |" will be "Login"
        // https://stackoverflow.com/a/32516190/128761 original source code.
        let navHtml = ($('#nav').html()).trim();
        let trimLastSep = navHtml.replace(/^\|+|\|+$/g, '');
        $('#nav').html(trimLastSep);*/// register link will be remove only from WP admin settings.
    }
});