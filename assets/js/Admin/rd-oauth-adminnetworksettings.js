/**
 * Rundiz Oauth plugin for WordPress.
 * 
 * Working on admin network settings page (multi-site).
 */


jQuery(function($) {
    console.debug('here', RdOauthAdminNetworkSettings);
    if (RdOauthAdminNetworkSettings.loginMethod === '2') {
        // if settings is use oauth only.
        // remove option that is "Both sites and user accounts can be registered".
        $('label:has(#registration4)').remove();
    }
});