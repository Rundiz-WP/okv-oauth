<?php
/**
 * Main app trait for common works.
 * 
 * @package okv-oauth
 */


namespace OKVOauth\App;

if (!trait_exists('\\OKVOauth\\App\\AppTrait')) {
    trait AppTrait
    {


        /**
         * Main option name.
         * 
         * @var string set main option name of this plugin. the name should be english, number, underscore, or anycharacters that can be set to variable. for example: 'okv_oauth_options' will be set to $okv_oauth_options
         * @uses call this trait method $this->getOptions(); before access $okv_oauth_options in global variable.
         */
        public $main_option_name = 'okv_oauth_options';

        /**
         * All available options.
         * these options will be accessible via main option name variable. for example: options name 'the_name' can call from $okv_oauth_options['the_name'];.
         * 
         * @var array set all options available for this plugin. it must be 2d array (key => default value, key2 => default value, ...)
         */
        public $all_options = [
            'okv_oauth_db_version' => '1.0',
        ];


        /**
         * Get all options of this plugin.
         * 
         * @return array return array value of all options.
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
         * @param array $data array of submitted data in key => value
         * @return bool return true if saved successfully. return false if not updated.
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
         * you have to call this method in activation, settings controller
         */
        public function setupAllOptions()
        {
            // load config values to get settings config file.
            $loader = new \OKVOauth\App\Libraries\Loader();
            $config_values = $loader->loadConfig();
            if (is_array($config_values) && array_key_exists('rundiz_settings_config_file', $config_values)) {
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
