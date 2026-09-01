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


document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginform');

    if (RdOauthLogin.loginMethod === '1') {
        // if use wp login + oauth.
        // move those social login btn to top.
        loginForm.querySelectorAll('.rd-oauth-form').forEach((form) => {
            loginForm.prepend(form);
        });
    } else if (RdOauthLogin.loginMethod === '2') {
        // if use oauth only.
        const navElement = document.getElementById('nav');

        if (rdOauthGetParameterByName('checkemail') === 'registered') {
            // if in registration completed page.
            loginForm.remove();
        } else {
            loginForm.classList.add('oauth-only');
            // remove login form.
            loginForm.querySelectorAll('p:has(label)')?.forEach((item) => {
                item.remove();
            });
            // remove password form.
            loginForm.querySelectorAll('.user-pass-wrap')?.forEach((item) => {
                item.remove();
            });
            // remove remember me form and submit btn
            loginForm.querySelectorAll('.forgetmenot, .submit')?.forEach((item) => {
                item.remove();
            });

            // remove forgot password link.
            document.querySelectorAll('.wp-login-lost-password')?.forEach((item) => {
                item.remove();
            });
            // remove forgot password link same as above but for fallback on older WP version.
            navElement?.querySelectorAll('a')?.forEach((item) => {
                if (item.href && item.href.indexOf('lostpassword') !== -1) {
                    item.remove();
                }
            });

            // after remove forgot password link, there may have the last character as `|`. 
            // example: `Login | Forgot password` becomes => `Login |`.
            // remove the last character.
            // @link https://stackoverflow.com/a/32516190/128761 Original source code.
            const navHtmlString = navElement.innerHTML.trim();
            const trimLastSep = navHtmlString.replace(/^\|+|\|+$/g, '');
            navElement.innerHTML = trimLastSep;
        }
    }// endif; login method.
});
