/**
 * Rundiz Oauth plugin for WordPress.
 */


/**
 * Get the value of a query string parameter by name from the current page URL.
 *
 * @since 1.0.0
 * @param {string} name The query parameter name to look for.
 * @returns {string|null} The decoded parameter value, an empty string if the name is present without a value, or null if not found.
 */
function rdOauthGetParameterByName(name) {
    const params = new URLSearchParams(window.location.search);
    if (!params.has(name)) {
        return null;
    }
    return params.get(name);
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