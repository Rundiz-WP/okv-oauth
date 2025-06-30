<?php
/**
 * @package rundiz-oauth
 * @since 1.5.7
 */


namespace RundizOauth\App\Libraries\MyOauth\Interfaces;


if (!interface_exists('\\RundizOauth\\App\\Libraries\\MyOauth\\Interfaces\\MyOAuthInterface')) {
    /**
     * My OAuth interface.
     * 
     * @since 1.5.7
     */
    interface MyOAuthInterface
    {


        /**
         * Get authenticate URL.
         * 
         * @param string $redirect_url Redirect URL.
         * @return string Return generated URL.
         */
        public function getAuthUrl($redirect_url);


        /**
         * Get icon CSS class names for selected OAuth provider.
         * 
         * @return string Return CSS class names of selected provider.
         */
        public function getIconClasses();


        /**
         * Get selected OAuth provider name.
         * 
         * @return string Return the name of selected provider.
         */
        public function getProviderName();


        /**
         * Check that authorized OAuth provider's email is not exists in the WordPress system.  
         * This usually will be use to check before change the email in edit profile page.
         * 
         * @return \WP_Error|string|null Return error message on failed to validate, return email string if validate passed.
         */
        public function wpCheckEmailNotExists();


        /**
         * Set login with OAuth.  
         * This will be verify token from service provider, check email exists in the database, and then set token cookie.
         * 
         * It will not doing anything login such as set auth cookie.
         * 
         * @return null|\WP_User|\WP_Error Return `null` if there is no `code` and `state` from service provider.
         *              Return `\WP_Error` if there is something errors.  
         *              Return `\WP_User` object if found selected user on the database. And login cookie will be set from here.
         */
        public function wpLoginUseOAuth();


        /**
         * Logout by delete token cookie.
         */
        public function wpLogoutUseOAuth();


        /**
         * Verify before register user data into WordPress.  
         * This will be verify token from service provider, check email is already verified on service provider, email must not exists in the database here, and then set token cookie, return user data required for register.
         * 
         * It will no doing anything register progress such as create user.
         * 
         * @return null|\WP_Error|array Return `null` if there is no `code` and `state` from service provider.  
         *              Return `\WP_Error` if there is something errors.  
         *              Return array with `access_token`, `email` in keys if success. There may have another keys depend on each service provider but these 2 keys are required.
         */
        public function wpRegisterUseOAuth();


    }// MyOAuthInterface
}// endif;
