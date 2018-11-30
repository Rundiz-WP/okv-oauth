<?php
/**
 * Errors collection
 * 
 * @package rundiz-oauth
 */


namespace RundizOauth\App\Libraries;


if (!class_exists('\\RundizOauth\\App\\Libraries\\ErrorsCollection')) {
    class ErrorsCollection
    {


        /**
         * Get error message from the error code.
         * 
         * Use inside Rundiz OAuth only.
         * 
         * @param string $errorCode The error code.
         * @return string Return readable message.
         */
        public static function getErrorMessage($errorCode)
        {
            if (!is_scalar($errorCode)) {
                return ;
            }

            switch ($errorCode) {
                case 'donotmanuallychangeemail':
                    return __('Do not manually change email while the settings is using OAuth only. Please try to change the email via OAuth button instead.', 'okv-oauth');
                case 'emailalreadyinuse':
                    return sprintf(__('Your email is already in use or registered, please try to %slogin%s instead.', 'okv-oauth'), '<a href="' . wp_login_url() . '">', '</a>');
                case 'emailalreadyinuse_tryanother':
                    return __('Your email is already in use or registered, please try another OAuth account that register with different email.', 'okv-oauth');
                case 'emailnotfoundinwordpress':
                    return sprintf(__('Not found this email on the system. Please try to %sregister%s your account.', 'okv-oauth'), '<a href="' . wp_login_url() . '?action=register' . '">', '</a>');
                case 'emailnotverified':
                    return __('Your email has not been verified or your user on OAuth provider was not found.', 'okv-oauth');
                case 'invalidoauthsettings':
                    return __('Invalid OAuth settings, please contact administrator to verify OAuth settings. Error code: %s.', 'okv-oauth');
                case 'invalidtoken':
                    return __('The token has been expired or invalid, please try again.', 'okv-oauth');
                case 'missingrequiredscope':
                    return __('No required scope. Please allow us to access an email, public profile to use with WordPress user system.', 'okv-oauth');
                case 'originalforgotpwdisabled':
                    return __('The original forgot password process has been disabled, please use OAuth only.', 'okv-oauth');
                case 'originallogindisabled':
                    return __('The original login process has been disabled, please use OAuth only.', 'okv-oauth');
                case 'originalregisterdisabled':
                    return __('The original registration process has been disabled, please use OAuth only.', 'okv-oauth');
                case 'tryagain':
                    return __('Sorry, please try again.', 'okv-oauth');
                case 'unableregister':
                    return __('Unable to register, please contact the administrator.', 'okv-oauth');
                case 'usernotverified':
                    return __('Your user account on OAuth provider was not verified, please verify your account before continue.', 'okv-oauth');
                default:
                    return ;
            }
        }// getErrorMessage


    }
}