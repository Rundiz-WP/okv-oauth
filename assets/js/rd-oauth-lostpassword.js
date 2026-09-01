/**
 * Rundiz Oauth plugin for WordPress.
 */


document.addEventListener('DOMContentLoaded', () => {
    const lostPasswordForm = document.getElementById('lostpasswordform');

    if (lostPasswordForm) {
        lostPasswordForm.querySelectorAll('.error-message').forEach((message) => {
            lostPasswordForm.before(message);
        });
    }

    if (typeof(RdOauthLostPassword.loginMethod) != 'undefined' && RdOauthLostPassword.loginMethod === '2') {
        // if okvoauth_login_method = 2 (use oauth only)
        // hide default instruction message.
        document.querySelectorAll('.message')?.forEach((eachMsg) => {
            eachMsg.remove();
        });

        if (lostPasswordForm) {
            // hide login (lost password) form.
            lostPasswordForm.classList.add('oauth-only');
            lostPasswordForm.style.display = 'none';

            // remove login form field.
            lostPasswordForm.querySelectorAll('p:has(label)')?.forEach((item) => {
                item.remove();
            });
            // remove submit btn
            lostPasswordForm.querySelectorAll('.submit')?.forEach((item) => {
                item.remove();
            });
        }
    }// endif; login method oauth only.
});
