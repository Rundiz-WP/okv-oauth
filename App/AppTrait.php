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
         * @var \OKVOauth\App\Libraries\Loader The loader class if it has been initiated. Make sure that this property must be set before use.
         */
        protected $Loader = null;


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
         * DB settings version.
         * 
         * @since 1.7.4
         * @var string Version number of current DB settings.
         */
        private $db_settings_version = '1.0';


        /**
         * Get DB settings version.
         * 
         * @since 1.7.4
         * @return string Version number of current DB settings.
         */
        public function getDBSettingsVersion()
        {
            if (!is_string($this->db_settings_version)) {
                return '1.0';
            }

            return $this->db_settings_version;
        }// getDBSettingsVersion


        /**
         * Get `Loader` object from `Loader` property.
         * 
         * This method is in main AppTrait.
         *
         * @return \OKVOauth\App\Libraries\Loader Return the `Loader` object.
         */
        protected function getLoader()
        {
            if (!$this->Loader instanceof \OKVOauth\App\Libraries\Loader) {
                $this->Loader = new \OKVOauth\App\Libraries\Loader();
            }
            return $this->Loader;
        }// getLoader


        /**
         * Get all options of this plugin from DB.
         * 
         * This method is in main AppTrait.
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
                // `get_option()` already unserializes internally - no need to re-run `maybe_unserialize()`.
                if (is_string($get_option)) {
                    // if older version of this plugin may still use manual serialize/unserialize.
                    // @todo[rundiz] delete this `if` block on version 2.0+
                    $get_option = maybe_unserialize($get_option);
                    if (!is_array($get_option)) {
                        $get_option = [];
                    }
                }

                // process data before save with `save_callback` option. -----------------------------
                $config_values = $this->getLoader()->loadConfig();
                $settings_config_file = '';
                if (is_array($config_values) && array_key_exists('rundiz_settings_config_file', $config_values)) {
                    // if there is config value about config file.
                    $settings_config_file = $config_values['rundiz_settings_config_file'];
                }
                unset($config_values);

                $RundizSettings = new \OKVOauth\App\Libraries\RundizSettings();
                $RundizSettings->settings_config_file = $settings_config_file;
                $get_option = $RundizSettings->processDisplayCallback($get_option);
                unset($RundizSettings, $settings_config_file);
                // end process data before save with `save_callback` option. -------------------------

                ${$option_name} = (array) $get_option;// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
            }

            unset($get_option);
            return ${$option_name};// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
        }// getOptions


        /**
         * Save the settings from settings page, using Rundiz settings.
         * 
         * This method is in main AppTrait.
         * 
         * @param array $data The associative array of submitted data in key => value
         * @return bool Return `true` if saved successfully. return `false` if not updated.
         */
        public function saveOptions(array $data)
        {
            $data = stripslashes_deep($data);

            // process data before save with `save_callback` option. -----------------------------
            $config_values = $this->getLoader()->loadConfig();
            $settings_config_file = '';
            if (is_array($config_values) && array_key_exists('rundiz_settings_config_file', $config_values)) {
                // if there is config value about config file.
                $settings_config_file = $config_values['rundiz_settings_config_file'];
            }
            unset($config_values);

            $RundizSettings = new \OKVOauth\App\Libraries\RundizSettings();
            $RundizSettings->settings_config_file = $settings_config_file;
            $data = $RundizSettings->processSaveCallback($data);
            unset($RundizSettings, $settings_config_file);
            // end process data before save with `save_callback` option. -------------------------

            $data['db_settings_version'] = $this->getDBSettingsVersion();

            return update_option($this->main_option_name, $data, false);
        }// saveOptions


        /**
         * Set `Loader` object to `Loader` property.
         * 
         * This method is in main AppTrait.
         *
         * @param \OKVOauth\App\Libraries\Loader $Loader The `Loader` object.
         */
        public function setLoader($Loader)
        {
            $this->Loader = $Loader;
        }// setLoader


    }// AppTrait
}
