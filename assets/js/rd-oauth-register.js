/**
 * Rundiz Oauth plugin for WordPress.
 */


document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('registerform');

    if (registerForm) {
        // move those social login btn to top.
        registerForm.querySelectorAll('.rd-oauth-form').forEach((form) => {
            registerForm.prepend(form);
        });
        // move error message to below generic message.
        const registerMessage = document.querySelector('.message');
        if (registerMessage) {
            registerForm.querySelectorAll('.error-message').forEach((error) => {
                registerMessage.after(error);
            });
        }
        const navElement = document.getElementById('nav');

        if (RdOauthRegister.loginMethod === '1') {
            // use wp login + oauth.
        } else if (RdOauthRegister.loginMethod === '2') {
            // use oauth only.
            if (registerForm) {
                // use oauth only.
                registerForm.classList.add('oauth-only');
                // remove register form
                registerForm.querySelectorAll('p:has(label)')?.forEach((item) => {
                    item.remove();
                });
                // remove register message (register confirmation will be ...), button.
                registerForm.querySelectorAll('#reg_passmail, .clear, .submit').forEach((item) => {
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
                const navHtmlString = navElement.innerHTML.trim();
                const trimLastSep = navHtmlString.replace(/^\|+|\|+$/g, '');
                navElement.innerHTML = trimLastSep;
            }
        }// endif; oauth only.
    }// endif; registerform
});
