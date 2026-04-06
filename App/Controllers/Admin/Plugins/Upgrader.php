<?php
/**
 * Upgrade or update the plugin action.
 *
 * @package okv-oauth
 * @since 1.7.2
 */


namespace OKVOauth\App\Controllers\Admin\Plugins;


if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('\\OKVOauth\App\Controllers\Admin\Plugins\\Upgrader')) {
    /**
     * Plugin upgrader class.
     * 
     * @since 1.7.2
     */
    class Upgrader implements \OKVOauth\App\Controllers\ControllerInterface
    {


        use \OKVOauth\App\AppTrait;


        /**
         * @var string Plugin updated transient name. For mark in transient that this plugin is just updated.
         */
        const PLUGIN_UPDATED_TRANSIENT_NAME = 'okv_oauth_updated__transient';


        /**
         * Detect this plugin updated and display link or maybe redirect to manual update page.
         *
         * This method will be run as new version of code.<br>
         * To understand more about new version of code, please read more on `updateProcessComplete()` method.
         *
         * @link https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices Reference.
         */
        public function detectPluginUpdate()
        {
            if (get_transient(static::PLUGIN_UPDATED_TRANSIENT_NAME) && current_user_can('update_plugins')) {
                // if there is updated transient and current user can update plugins.
                // trigger activate to make things up to date.
                $Activation = new Activation();
                $okv_oauth_options = $this->getOptions();

                if (is_multisite()) {
                    // this site is multisite. activate on all site.
                    $sites = get_sites(['number' => 0]);
                    if ($sites) {
                        foreach ($sites as $site) {
                            switch_to_blog($site->blog_id);
                            $Activation->activateAddUpdateOption($okv_oauth_options);
                            restore_current_blog();
                        }
                    }
                    unset($site, $sites);
                } else {
                    // this site is single site. activate on single site.
                    $Activation->activateAddUpdateOption($okv_oauth_options);
                }

                unset($Activation, $okv_oauth_options);
                delete_transient(static::PLUGIN_UPDATED_TRANSIENT_NAME);
            }// endif;
        }// detectPluginUpdate


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            // On update/upgrade plugin completed, set transient and let `detectPluginUpdate()` work.
            add_action('upgrader_process_complete', [$this, 'updateProcessComplete'], 10, 2);
            // On WordPress has finished loading but before any headers are sent, detect plugin is just updated.
            add_action('init', [$this, 'detectPluginUpdate']);
        }// registerHooks


        /**
         * After update plugin completed.
         *
         * This method will be called while running the current version of this plugin, not the new one that just updated.
         * For example: You are running 1.0 and just updated to 2.0. The 2.0 version will not working here yet but 1.0 is working.
         * So, any code here will not work as the new version. Please be aware!
         *
         * This method will add the transient to be able to detect updated and run the manual update in `detectPluginUpdate()` method.
         *
         * @link https://developer.wordpress.org/reference/hooks/upgrader_process_complete/ Reference.
         * @link https://developer.wordpress.org/reference/classes/wp_upgrader/ Reference.
         * @param \WP_Upgrader $upgrader The `\WP_Upgrader` class.
         * @param array $hook_extra Array of bulk item update data.
         */
        public function updateProcessComplete(\WP_Upgrader $upgrader, array $hook_extra)
        {
            if (is_array($hook_extra) && array_key_exists('action', $hook_extra) && array_key_exists('type', $hook_extra) && array_key_exists('plugins', $hook_extra)) {
                if ('update' === $hook_extra['action'] && 'plugin' === $hook_extra['type'] && is_array($hook_extra['plugins']) && !empty($hook_extra['plugins'])) {
                    $this_plugin = plugin_basename(OKVOAUTH_FILE);
                    foreach ($hook_extra['plugins'] as $key => $plugin) {
                        if ($this_plugin === $plugin) {
                            // if this plugin is in the updated plugins.
                            // set transient to let it run later. this transient will be called and run in `detectPluginUpdate()` method.
                            set_transient(static::PLUGIN_UPDATED_TRANSIENT_NAME, 1);
                            break;
                        }
                    }// endforeach;
                    unset($key, $plugin, $this_plugin);
                }// endif update plugin and plugins not empty.
            }// endif; $hook_extra
        }// updateProcessComplete


    }// Upgrader
}
