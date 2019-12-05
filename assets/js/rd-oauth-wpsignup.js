/**
 * Rundiz Oauth plugin for WordPress.
 */


jQuery(function($) {
    // move those social login btn to top.
    $('#setupform').find('.rd-oauth-form').insertBefore('#setupform');
    // move error message to below generic message.
    $('#setupform').find('.error-message').insertAfter($('.message'));

    if (RdOauthRegister.loginMethod === '1') {
        // use wp login + oauth.
    } else if (RdOauthRegister.loginMethod === '2') {
        // use oauth only.
        $('#setupform').addClass('oauth-only');

        if (RdOauthRegister.active_signup === 'user') {
            // if allow register for user only.
            // remove register form
            $('#setupform').html('');
        } else if (RdOauthRegister.active_signup === 'all') {
            // if allow register user and blog.
            // remove "Just a username please" option.
            $('#setupform #signupuser').remove();
            $('#setupform label[for="signupuser"]').remove();
            // force select radio to "Gimme a site!"
            $('#setupform #signupblog').prop('checked', 'checked');
            $('#setupform #signupblog').attr('checked', 'checked');
        }
    }
});