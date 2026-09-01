/**
 * Rundiz Oauth plugin for WordPress.
 */


document.addEventListener('DOMContentLoaded', () => {
    const setupForm = document.getElementById('setupform');
    if (setupForm) {
        // move those social login btn to top.
        setupForm.querySelectorAll('.rd-oauth-form').forEach(oauthForm => {
            setupForm.before(oauthForm);
        });

        // move error message to below generic message.
        const messageEl = document.querySelector('.message');
        setupForm.querySelectorAll('.error-message').forEach(eachMessage => {
            messageEl.after(eachMessage)
        });
    }// endif;

    if (RdOauthRegister.loginMethod === '1') {
        // use wp login + oauth.
    } else if (RdOauthRegister.loginMethod === '2') {
        // use oauth only.
        if (setupForm) {
            setupForm.classList.add('oauth-only');

            if (RdOauthRegister.active_signup === 'user') {
                // if allow register for user only.
                // remove register form
                setupForm.innerHTML = '';
            } else if (RdOauthRegister.active_signup === 'all') {
                // if allow register user and blog.
                // remove "Just a username please" option.
                setupForm.querySelector('#signupuser')?.remove();
                setupForm.querySelector('label[for="signupuser"]')?.remove();
                // force select radio to "Gimme a site!"
                setupForm.querySelector('#signupblog').checked = true;
            }
        }
    }// endif;
});
