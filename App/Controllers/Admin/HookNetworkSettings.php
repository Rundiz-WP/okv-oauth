<?php
/**
 * @package rundiz-oauth
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace RundizOauth\App\Controllers\Admin;


if (!class_exists('\\RundizOauth\\App\\Controllers\\Admin\\HookNetworkSettings')) {
    class HookNetworkSettings extends \RundizOauth\App\Libraries\RundizOauth implements \RundizOauth\App\Controllers\ControllerInterface
    {


        /**
         * Enqueue admin scripts.
         * 
         * @param string $hook
         */
        public function adminEnqueueScripts($hook)
        {
            if (is_admin() && is_multisite() && 'settings.php' === $hook) {
                $this->init();

                wp_enqueue_script('rd-oauth-adminnetworksettings', plugin_dir_url(RUNDIZOAUTH_FILE) . 'assets/js/rd-oauth-adminnetworksettings.js', ['jquery'], false, true);
                wp_localize_script(
                    'rd-oauth-adminnetworksettings',
                    'RdOauthAdminNetworkSettings',
                    [
                        'loginMethod' => $this->loginMethod,
                    ]
                );
            }
        }// adminEnqueueScripts


        /**
         * Filter update network settings for site registration.
         * 
         * @param mixed $value New value of the network option.
         * @param mixed $old_value Old value of the network option.
         * @param string $option Option name.
         * @param int $network_id ID of the network.
         */
        public function FilterUpdateSiteRegistration($value, $old_value, $option, $network_id)
        {
            $this->init();

            if (2 === $this->loginMethod) {
                // if settings was set to use oauth only.
                // cannot allow register form to register user and blog 
                // because user can register any email that is not oauth user 
                // but cannot login with [oauth only login form].
                $value = 'blog';
            }

            return $value;
        }// FilterUpdateSiteRegistration


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
            add_filter('pre_update_site_option_registration', [$this, 'FilterUpdateSiteRegistration'], 10, 4);
        }// registerHooks


    }
}
