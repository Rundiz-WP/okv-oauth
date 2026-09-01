/**
 * Rundiz Oauth plugin for WordPress.
 * 
 * Working on admin network settings page (multi-site).
 */


document.addEventListener('DOMContentLoaded', () => {
    if (RdOauthAdminNetworkSettings.loginMethod === '2') {
        // if settings is use oauth only.
        // remove option that is "Both sites and user accounts can be registered".
        document.querySelectorAll('label:has(#registration4)')?.forEach((item) => {
            item.remove();
        });
    }
});
