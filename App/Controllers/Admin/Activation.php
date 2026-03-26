<?php
/**
 * Activation class is the class that will be working on activate, deactivate, delete WordPress plugin.
 * 
 * @package okv-oauth
 */


namespace OKVOauth\App\Controllers\Admin;


if (!class_exists('\\OKVOauth\\App\\Controllers\\Activation')) {
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
         * Add links to plugin actions area
         * 
         * @param array $actions current plugin actions. (including deactivate, edit).
         * @param string $plugin_file the plugin file for checking.
         * @return array return modified links
         */
        public function actionLinks($actions, $plugin_file)
        {
            static $plugin;
            
            if (!isset($plugin)) {
                $plugin = plugin_basename(OKVOAUTH_FILE);
            }
            
            if ($plugin === $plugin_file) {
                $link['settings'] = '<a href="' . esc_url(get_admin_url(null, 'options-general.php?page=rd-oauth-settings')) . '">' . __('Settings', 'okv-oauth') . '</a>';
                $actions = array_merge($link, $actions);
            }
            
            return $actions;
        }// actionLinks


        /**
         * Activate the plugin by admin on wp plugin page.
         * 
         * @since 1.7.0 Renamed from `activation`.
         * @global \wpdb $wpdb WordPress db class.
         */
        public function activate()
        {
            // do something that will happens on activate plugin.
            $wordpress_required_version = '4.6.0';
            $phpversion_required = '5.4';
            if (function_exists('phpversion')) {
                $phpversion = phpversion();
            }
            if (!isset($phpversion) || (isset($phpversion) && false === $phpversion)) {
                if (defined('PHP_VERSION')) {
                    $phpversion = PHP_VERSION;
                } else {
                    // can't detect php version
                    $phpversion = '4';
                }
            }
            if (version_compare($phpversion, $phpversion_required, '<')) {
                /* translators: %1$s: Current PHP version, %2$s: Required PHP version. */
                wp_die(
                    wp_kses_post(
                        sprintf(
                            /* translators: %1$s current php version, %2$s required php version. */
                            __('You are using PHP %1$s which does not meet minimum requirement. Please consider upgrade PHP version or contact plugin author for this help.<br><br>Minimum requirement:<br>PHP %2$s', 'okv-oauth'), 
                            $phpversion, 
                            $phpversion_required
                        )
                    ), 
                    esc_html__('Minimum requirement of PHP version does not meet.', 'okv-oauth')
                );
                exit;
            }
            if (version_compare(get_bloginfo('version'), $wordpress_required_version, '<')) {
                /* translators: %1$s: Current WordPress version, %2$s: Required WordPress version. */
                wp_die(
                    esc_html(
                        sprintf(
                            /* translators: %1$s current WordPress version, %2$s required WP version. */
                            __('Your WordPress version does not meet the requirement. (%1$s < %2$s).', 'okv-oauth'), 
                            get_bloginfo('version'), 
                            $wordpress_required_version
                        )
                    )
                );
                exit;
            }
            unset($phpversion, $phpversion_required, $wordpress_required_version);

            // get wpdb global var.
            global $wpdb;
            $wpdb->show_errors();

            // get current options for use incase it is update.
            $okv_oauth_options = $this->getOptions();

            // add option to site or multisite -----------------------------
            if (is_multisite()) {
                // this site is multisite. activate on all site.
                $sites = get_sites();
                if ($sites) {
                    foreach ($sites as $site) {
                        switch_to_blog($site->blog_id);
                        $this->activateAddUpdateOption($okv_oauth_options);
                        restore_current_blog();
                    }
                }
                unset($site, $sites);
            } else {
                // this site is single site. activate on single site.
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
        private function activateAddUpdateOption(array $current_options = [])
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
            // @todo[rundiz] Delete this block of code on v 1.7+.
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
            // @todo[rundiz] Delete this block of code on v 1.7+.
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
         * Deactivate the plugin hook.
         * 
         * @since 1.7.0 Renamed from `deactivation`.
         */
        public function deactivate()
        {
            // do something that will be happens on deactivate plugin.
            // remove all added rewrite rules.
            flush_rewrite_rules();
        }// deactivate


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
         * Get main_option_name from trait which is non-static from any static method.
         * 
         * @return string
         */
        private static function getMainOptionName()
        {
            $class = new self();
            return $class->main_option_name;
        }// getMainOptionName


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            // register activate hook
            register_activation_hook(OKVOAUTH_FILE, [$this, 'activate']);
            // register deactivate hook
            register_deactivation_hook(OKVOAUTH_FILE, [$this, 'deactivate']);
            // register uninstall hook. this hook will be work on delete plugin.
            // * register uninstall hook MUST be static method or function.
            register_uninstall_hook(OKVOAUTH_FILE, array('\\OKVOauth\\App\\Controllers\\Admin\\Activation', 'uninstall'));
            // on update/upgrade plugin
            add_action('upgrader_process_complete', [$this, 'updatePlugin'], 10, 2);

            // add filter action links. this will be displayed in actions area of plugin page. for example: xxxbefore | Activate | Edit | Delete | xxxafter
            add_filter('plugin_action_links', [$this, 'actionLinks'], 10, 5);
            // add filter to row meta. (in plugin page below description). for example: By xxx | Visit plugin site | xxxafter
            add_filter('plugin_row_meta', [$this, 'rowMeta'], 10, 2);
        }// registerHooks


        /**
         * Add links to row meta that is in Plugins page under plugin description. For example: xxxbefore | By xxx | Visit plugin site | xxxafter
         * 
         * @staticvar string $plugin The plugin file name.
         * @param array $links Current meta links
         * @param string $file The plugin file name for checking.
         * @return array Return modified links.
         */
        public function rowMeta(array $links, $file)
        {
            static $plugin;
            
            if (!isset($plugin)) {
                $plugin = plugin_basename(OKVOAUTH_FILE);
            }
            
            if ($plugin === $file) {
                $after_link = [];

                $after_link[] = '<a href="https://rundiz.com/en/donate" target="donate">' . __('Donate', 'okv-oauth') . '</a>';
                $links = array_merge($links, $after_link);
                unset($after_link);
            }
            
            return $links;
        }// rowMeta


        /**
         * Delete the plugin.
         * 
         * @global \wpdb $wpdb
         */
        public static function uninstall()
        {
            // do something that will be happens on delete plugin.
            global $wpdb;
            $wpdb->show_errors();

            // delete options.
            if (is_multisite()) {
                // this is multi site, delete options in all sites.
                $sites = get_sites();
                if ($sites) {
                    foreach ($sites as $site) {
                        switch_to_blog($site->blog_id);
                        delete_option(static::getMainOptionName());
                        delete_option('widget_rdoauth_loginlinks_widget');
                        restore_current_blog();
                    }
                }
                unset($site, $sites);
            } else {
                // this is single site, delete options in single site.
                delete_option(static::getMainOptionName());
                delete_option('widget_rdoauth_loginlinks_widget');
            }
        }// uninstall


        /**
         * Works on update plugin.
         * 
         * @link https://developer.wordpress.org/reference/hooks/upgrader_process_complete/ Reference.
         * @link https://codex.wordpress.org/Plugin_API/Action_Reference/upgrader_process_complete Reference.
         * @param \WP_Upgrader $upgrader `WP_Upgrader` instance. In other contexts this might be a `Theme_Upgrader`, `Plugin_Upgrader`, `Core_Upgrade`, or `Language_Pack_Upgrader` instance.
         * @param array $hook_extra Array of bulk item update data.
         */
        public function updatePlugin($upgrader, array $hook_extra)
        {
            if (is_array($hook_extra) && array_key_exists('action', $hook_extra) && array_key_exists('type', $hook_extra) && array_key_exists('plugins', $hook_extra)) {
                if ('update' === $hook_extra['action'] && 'plugin' === $hook_extra['type'] && is_array($hook_extra['plugins']) && !empty($hook_extra['plugins'])) {
                    $this_plugin = plugin_basename(OKVOAUTH_FILE);
                    foreach ($hook_extra['plugins'] as $key => $plugin) {
                        if ($this_plugin === $plugin) {
                            $this_plugin_updated = true;
                            break;
                        }
                    }// endforeach;
                    unset($key, $plugin, $this_plugin);

                    if (isset($this_plugin_updated) && true === $this_plugin_updated) {
                        global $wpdb;
                        $wpdb->show_errors();

                        // get current options for use incase it is update.
                        $okv_oauth_options = $this->getOptions();

                        // add option to site or multisite -----------------------------
                        if (is_multisite()) {
                            // this site is multisite. activate on all site.
                            $sites = get_sites();
                            if ($sites) {
                                foreach ($sites as $site) {
                                    switch_to_blog($site->blog_id);
                                    $this->activateAddUpdateOption($okv_oauth_options);
                                    restore_current_blog();
                                }
                            }
                            unset($site, $sites);
                        } else {
                            // this site is single site. activate on single site.
                            $this->activateAddUpdateOption($okv_oauth_options);
                        }

                        unset($okv_oauth_options);
                    }// endif; $this_plugin_updated
                }// endif update plugin and plugins not empty.
            }// endif; $hook_extra
        }// updatePlugin


    }// Activation
}
