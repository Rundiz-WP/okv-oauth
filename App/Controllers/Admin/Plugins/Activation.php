<?php
/**
 * Activate the plugin action.
 *
 * @package okv-oauth
 * @since 1.7.2 Moved from App/Controllers/Admin/Activation.php
 */


namespace OKVOauth\App\Controllers\Admin\Plugins;


if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('\\OKVOauth\\App\\Controllers\\Admin\\Plugins\\Activation')) {
    /**
     * Plugin activation and new site activation hooks class.
     */
    class Activation implements \OKVOauth\App\Controllers\ControllerInterface
    {


        use \OKVOauth\App\AppTrait;


        /**
         * @var bool Mark that is it already setup all options.
         */
        private $alreadySetupAllOptions = false;


        /**
         * Activate the plugin by admin on WP plugin page.
         *
         * @link https://developer.wordpress.org/reference/functions/register_activation_hook/ The function `register_activation_hook()` reference.
         * @link https://developer.wordpress.org/reference/hooks/activate_plugin/ The reference about what will be pass to callback of function `register_activation_hook()`.
         * @since 1.7.0 Renamed from `activation`.
         * @global \wpdb $wpdb WordPress DB class.
         * @param bool $network_wide Whether to enable the plugin for all sites in the network or just the current site. Multisite only. Default false.
         * @throws \Exception Throw the exception if failed to detect current version of PHP.
         */
        public function activate($network_wide)
        {
            // Do something that will happens on activate plugin.
            $wordpress_required_version = '4.6.0';
            $phpversion_required = '5.4';
            if (function_exists('phpversion')) {
                $phpversion = phpversion();
            }
            if (!isset($phpversion) || (isset($phpversion) && false === $phpversion)) {
                if (defined('PHP_VERSION')) {
                    $phpversion = PHP_VERSION;
                } else {
                    // if there is no defined constant `PHP_VERSION`.
                    // @link https://www.php.net/ChangeLog-4.php Reference.
                    throw new \Exception('You are using ancient version of PHP. The constant `PHP_VERSION` is available since PHP 4.0.');
                }
            }
            if (version_compare($phpversion, $phpversion_required, '<')) {
                wp_die(
                    esc_html(
                        sprintf(
                            /* translators: %1$s current PHP version. */
                            __('You are using PHP %1$s which does not meet minimum requirement. Please consider upgrade PHP version or contact plugin author for this help.', 'okv-oauth'),
                            $phpversion
                        )
                    )
                    . '<br><br>'
                    . esc_html(
                        sprintf(
                            /* translators: %1$s minimum PHP version required. */
                            __('Minimum PHP requirement: %1$s.', 'okv-oauth'),
                            $phpversion_required
                        )
                    ), 
                    esc_html__('Minimum requirement of PHP version does not meet.', 'okv-oauth')
                );
                exit(1);
            }// endif;
            if (version_compare(get_bloginfo('version'), $wordpress_required_version, '<')) {
                wp_die(
                    esc_html(
                        sprintf(
                            // translators: %1$s Current WordPress version, %2$s Required WordPress version.
                            __('Your WordPress version does not meet the requirement. (%1$s < %2$s).', 'okv-oauth'), 
                            get_bloginfo('version'),
                            $wordpress_required_version
                        )
                    ),
                    esc_html__('Minimum requirement of WordPress version does not meet.', 'okv-oauth')
                );
                exit(1);
            }// endif;
            unset($phpversion, $phpversion_required, $wordpress_required_version);

            // Get `$wpdb` global var.
            global $wpdb;
            $wpdb->show_errors();

            // get current options for use incase it is update.
            $okv_oauth_options = $this->getOptions();

            // Add option to site or multisite -----------------------------
            if (is_multisite()) {
                // This site is multisite. Add/update options, create/alter tables on all sites.
                $sites = get_sites(['number' => 0]);
                if ($sites) {
                    foreach ($sites as $site) {
                        switch_to_blog($site->blog_id);
                        $this->activateAddUpdateOption($okv_oauth_options);
                        restore_current_blog();
                    }
                }
                unset($site, $sites);
            } else {
                // This site is single site. Add/update options, create/alter tables on current site.
                $this->activateAddUpdateOption($okv_oauth_options);
            }

            unset($okv_oauth_options);
        }// activate


        /**
         * Check for option on current site and add if not exists, or update if option is older.
         * 
         * @since 1.7.0 Renamed from `activationAddUpdateOption`.
         * @param array $current_options current options values for check and use in case of update options.
         */
        public function activateAddUpdateOption(array $current_options = [])
        {
            if (false === $this->alreadySetupAllOptions) {
                $this->setupAllOptions();
                $this->alreadySetupAllOptions = true;
            }

            if (defined('WP_DEBUG') && WP_DEBUG === true) {
                // for debug.
                error_log('plugin activate or updated for site ' . get_current_blog_id());// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
            }

            // check current option exists or not.
            $current_options = get_option($this->main_option_name);

            // check old version option names and move them into new option name.
            // @todo[rundiz] Delete this block of code on v 1.8+.
            if (false === $current_options) {
                // if newly installed (at least for this version). current option is not exists, add it.
                $sub_options = [];

                if (is_array($this->all_options) && !empty($this->all_options)) {
                    $sub_options = $this->all_options;
                    // get previous version options and update into new options
                    $okvoauth_login_method = get_option('okvoauth_login_method');
                    $okvoauth_login_cookie_expiration = get_option('okvoauth_login_cookie_expiration');
                    $okvoauth_google_client_id = get_option('okvoauth_google_client_id');
                    $okvoauth_google_client_secret = get_option('okvoauth_google_client_secret');
                    if (isset($sub_options['login_method']) && false !== $okvoauth_login_method) {
                        $sub_options['login_method'] = $okvoauth_login_method;
                    }
                    if (isset($sub_options['login_expiration']) && false !== $okvoauth_login_cookie_expiration) {
                        $sub_options['login_expiration'] = $okvoauth_login_cookie_expiration;
                    }
                    if (isset($sub_options['google_client_id']) && false !== $okvoauth_google_client_id) {
                        $sub_options['google_client_id'] = $okvoauth_google_client_id;
                        if (isset($sub_options['google_login_enable'])) {
                            $sub_options['google_login_enable'] = '1';
                        }
                    }
                    if (isset($sub_options['google_client_secret']) && false !== $okvoauth_google_client_secret) {
                        $sub_options['google_client_secret'] = $okvoauth_google_client_secret;
                        if (isset($sub_options['google_login_enable'])) {
                            $sub_options['google_login_enable'] = '1';
                        }
                    }
                    delete_option('okvoauth_login_method');
                    delete_option('okvoauth_login_cookie_expiration');
                    delete_option('okvoauth_google_client_id');
                    delete_option('okvoauth_google_client_secret');
                    unset($okvoauth_google_client_id, $okvoauth_google_client_secret, $okvoauth_login_cookie_expiration, $okvoauth_login_method);
                    // end get previous version options and update into new options
                }

                $sub_options = maybe_serialize($sub_options);
                add_option($this->main_option_name, $sub_options, '', false);
                unset($sub_options);
            }// endif;
            // end check old version option names. ---------------------

            // rename option name from pre v1.6.5 that was used `rundizoauth_option` to be current option name defined in `main_option_name` property.
            // @todo[rundiz] Delete this block of code on v 1.8+.
            $pre1_6_5_options = get_option('rundizoauth_options');// this option name will not be renamed.
            if (
                'rundizoauth_options' !== $this->main_option_name && // this option name will not be renamed.
                is_string($pre1_6_5_options) && 
                '' !== $pre1_6_5_options
            ) {
                // if there is an option from previous version that use wrong name.
                // in older versions this plugin was use option name prefix with `rundizoauth_` which is not match with plugin slug.
                // move them to new option name that match plugin slug.
                update_option($this->main_option_name, $pre1_6_5_options, false);
                // delete previous old option name.
                delete_option('rundizoauth_options');// this option name will not be renamed.
            }
            unset($pre1_6_5_options);
            // end rename option name. -----------

            unset($current_options);

            // add custom rewrite rules.
            $RewriteRules = new \OKVOauth\App\Controllers\Front\RewriteRules();
            $RewriteRules->addRewriteRules();
            unset($RewriteRules);
            flush_rewrite_rules();

            $this->deleteOldOptions();
        }// activateAddUpdateOption


        /**
         * Delete old options from previous version.
         * 
         * @since 1.5.6
         */
        private function deleteOldOptions()
        {
            delete_option('okvoauth_login_method');
            delete_option('okvoauth_login_cookie_expiration');
            delete_option('okvoauth_google_client_id');
            delete_option('okvoauth_google_client_secret');
        }// deleteOldOptions


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            // register activate hook
            register_activation_hook(OKVOAUTH_FILE, [$this, 'activate']);
        }// registerHooks


        /**
         * Delete the plugin.
         * 
         * This method is keep for fallback when some old installation may call to this method even after the plugin is already updated.
         * 
         * @deprecated since 1.7.2
         */
        public static function uninstall()
        {
            Uninstallation::uninstall();
        }// uninstall


    }// Activation
}
