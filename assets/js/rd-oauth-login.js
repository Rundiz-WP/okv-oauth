/**
 * Rundiz Oauth plugin for WordPress.
 */


function rdOauthGetParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}// rdOauthGetParameterByName


jQuery(function($) {
    if (RdOauthLogin.loginMethod === '1') {
        // use wp login + oauth.
        // move those social login btn to top.
        $('#loginform').find('.rd-oauth-form').prependTo('#loginform');
    } else if (RdOauthLogin.loginMethod === '2') {
        // use oauth only.
        if (rdOauthGetParameterByName('checkemail') === 'registered') {
            // registration completed page.
            $('#loginform').remove();
        } else {
            $('#loginform').addClass('oauth-only');
            // remove login form.
            $('#loginform').find('p:has(label)').remove();
            // remove password form.
            $('#loginform .user-pass-wrap').remove();
            // remove remember me form and submit btn
            $('#loginform').find('.forgetmenot, .submit').remove();
        }

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