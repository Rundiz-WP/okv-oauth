<?php
/**
 * Hooks into profile page (logged in user edit their profile).
 * 
 * @since 1.5.6
 * @package okv-oauth
 */


namespace OKVOauth\App\Controllers\Admin;


if (!class_exists('\\OKVOauth\\App\\Controllers\\Admin\\HookProfilePage')) {
    /**
     * Hook profile page class. (User profile.)
     */
    class HookProfilePage extends \OKVOauth\App\Libraries\RundizOauth implements \OKVOauth\App\Controllers\ControllerInterface
    {


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            add_filter('show_password_fields', [$this, 'showPasswordFields'], 10, 2);
        }// registerHooks


        /**
         * Show or hide password fields.
         * 
         * @link https://developer.wordpress.org/reference/hooks/show_password_fields/ Reference.
         * @param bool $show Whether to show the password fields. Default `true`.
         * @param \WP_User $profile User object for the current user to edit.
         * @return bool
         */
        public function showPasswordFields($show, \WP_User $profile)
        {
            $this->init();

            if (2 === $this->loginMethod) {
                $show = false;
            }

            return $show;
        }// showPasswordFields


    }// HookProfilePage
}// endif;
