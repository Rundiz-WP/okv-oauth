<?php
/**
 * Main app trait for common works.
 * 
 * @package okv-oauth
 */


namespace OKVOauth\App;

if (!trait_exists('\\OKVOauth\\App\\AppTrait')) {
    /**
     * Main application trait.
     */
    trait AppTrait
    {


        /**
         * Main option name.
         * 
         * @var string Set main option name of this plugin. the name should be english, number, underscore, 
         *              or any characters that can be set to variable. 
         *              For example: `'okv_oauth_options'` will be set to `$okv_oauth_options`
         * @uses Call the trait method `getOptions();` before access `$okv_oauth_options` in global variable.
         */
        public $main_option_name = 'okv_oauth_options';

        /**
         * All available options.
         * 
         * These options will be accessible via main option name variable. 
         * For example: options name `'the_name'` can call from `$okv_oauth_options['the_name'];`.
         * If you want to access this property, please call to `setupAllOptions()` method first.
         * 
         * @var array Set all options available for this plugin. it must be 2D array (`key => default value, key2 => default value, ...`)
         */
        public $all_options = [
            'okv_oauth_db_version' => '1.0',
        ];


        /**
         * Get all options of this plugin.
         * 
         * @return array Return associative array value of all options where the key is option name.
         */
        public function getOptions()
        {
            $option_name = $this->main_option_name;
            global ${$option_name};// phpcs:ignore PHPCompatibility.Variables.ForbiddenGlobalVariableVariable.NonBareVariableFound
            ${$option_name} = [];// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

            $get_option = get_option($option_name);
            if (false !== $get_option) {
                // if option has value.
                ${$option_name} = maybe_unserialize($get_option);// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                unset($get_option);
                return (array) ${$option_name};
            }

            unset($get_option);
            return [];
        }// getOptions


        /**
         * Save options.
         * 
         * @param array $data The associative array of submitted data in key => value
         * @return bool Return `true` if saved successfully. return `false` if not updated.
         */
        public function saveOptions(array $data)
        {
            $get_option = get_option($this->main_option_name);
            $sub_options = maybe_serialize(stripslashes_deep($data));
            if (false !== $get_option) {
                return update_option($this->main_option_name, $sub_options, false);
            } else {
                return add_option($this->main_option_name, $sub_options, '', false);
            }
        }// saveOptions


        /**
         * Setup all options from settings config file.
         * 
         * This will be set all config settings into `all_options` property.
         * You have to call this method if you want to call to `all_options` property.
         * 
         * This method will not load saved settings data from DB. The value in settings fields are all default value.
         */
        public function setupAllOptions()
        {
            // load config values to get settings config file.
            $loader = new \OKVOauth\App\Libraries\Loader();
            $config_values = $loader->loadConfig();
            if (is_array($config_values) && array_key_exists('rundiz_settings_config_file', $config_values)) {
                // if there is config value about config file.
                $settings_config_file = $config_values['rundiz_settings_config_file'];
            } else {
                echo 'Settings configuration file was not set.';
                die('Settings configuration file was not set.');
            }
            unset($config_values, $loader);

            $RundizSettings = new \OKVOauth\App\Libraries\RundizSettings();
            $RundizSettings->settings_config_file = $settings_config_file;
            $this->all_options = array_merge($this->all_options, $RundizSettings->getSettingsFieldsId());
            unset($RundizSettings, $settings_config_file);
        }// setupAllOptions


    }// AppTrait
}
