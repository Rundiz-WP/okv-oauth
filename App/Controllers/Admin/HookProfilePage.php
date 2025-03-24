<?php
/**
 * Hooks into profile page (logged in user edit their profile).
 * 
 * @since 1.5.6
 * @package rundiz-oauth
 */


namespace RundizOauth\App\Controllers\Admin;


if (!class_exists('\\RundizOauth\\App\\Controllers\\Admin\\HookProfilePage')) {
    class HookProfilePage extends \RundizOauth\App\Libraries\RundizOauth implements \RundizOauth\App\Controllers\ControllerInterface
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
         * @param bool $show
         * @param \WP_User $profile
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


    }
}// endif;