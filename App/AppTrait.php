<?php
/**
 * Main app trait for common works.
 * 
 * @package rundiz-oauth
 */


namespace RundizOauth\App;

if (!trait_exists('\\RundizOauth\\App\\AppTrait')) {
    trait AppTrait
    {


        /**
         * main option name.
         * @var string set main option name of this plugin. the name should be english, number, underscore, or anycharacters that can be set to variable. for example: 'rundizoauth_options' will be set to $rundizoauth_options
         * @uses call this trait method $this->getOptions(); before access $rundizoauth_options in global variable.
         */
        public $main_option_name = 'rundizoauth_options';

        /**
         * all available options.
         * these options will be accessible via main option name variable. for example: options name 'the_name' can call from $rundizoauth_options['the_name'];.
         * @var array set all options available for this plugin. it must be 2d array (key => default value, key2 => default value, ...)
         */
        public $all_options = [
            'rdoauth_db_version' => '1.0',
        ];


        /**
         * get all options of this plugin.
         * 
         * @return array return array value of all options.
         */
        public function getOptions()
        {
            ${$this->main_option_name} = [];
            global ${$this->main_option_name};

            $get_option = get_option($this->main_option_name);
            if ($get_option !== false) {
                ${$this->main_option_name} = maybe_unserialize($get_option);
                unset($get_option);
                return (array) ${$this->main_option_name};
            }

            unset($get_option);
            return [];
        }// getOptions


        /**
         * save options.
         * 
         * @param array $data array of submitted data in key => value
         * @return boolean return true if saved successfully. return false if not updated.
         */
        public function saveOptions(array $data)
        {
            $get_option = get_option($this->main_option_name);
            $sub_options = maybe_serialize(stripslashes_deep($data));
            if ($get_option !== false) {
                return update_option($this->main_option_name, $sub_options);
            } else {
                return add_option($this->main_option_name, $sub_options);
            }
        }// saveOptions


        /**
         * setup all options from settings config file.
         * you have to call this method in activation, settings controller
         */
        public function setupAllOptions()
        {
            // load config values to get settings config file.
            $loader = new \RundizOauth\App\Libraries\Loader();
            $config_values = $loader->loadConfig();
            if (is_array($config_values) && array_key_exists('rundiz_settings_config_file', $config_values)) {
                $settings_config_file = $config_values['rundiz_settings_config_file'];
            } else {
                echo 'Settings configuration file was not set.';
                die('Settings configuration file was not set.');
                exit;
            }
            unset($config_values, $loader);

            $RundizSettings = new \RundizOauth\App\Libraries\RundizSettings();
            $RundizSettings->settings_config_file = $settings_config_file;
            $this->all_options = array_merge($this->all_options, $RundizSettings->getSettingsFieldsId());
            unset($RundizSettings, $settings_config_file);
        }// setupAllOptions


    }
}