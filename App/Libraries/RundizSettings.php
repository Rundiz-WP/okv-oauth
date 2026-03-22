<?php
/**
 * Rundiz Settings class.
 * 
 * @package okv-oauth
 */


namespace OKVOauth\App\Libraries;


if (!defined('ABSPATH')) {
    exit();
}


/**
 * Rundiz Settings class for render pre-setup values. This will render tabs, form fields and content in each tabs.
 */
if (!class_exists('\\OKVOauth\\App\\Libraries\\RundizSettings')) {
    /**
     * Rundiz Settings class.
     */
    class RundizSettings
    {


        /**
         * @var string Settings config file.
         */
        public $settings_config_file;


        /**
         * Get settings config file and its data.
         * 
         * @return array|false Return settings config data. Return `false` if failed.
         */
        private function getConfigFile()
        {
            $setting_file = $this->settings_config_file;

            if ('' === $setting_file || !is_string($setting_file)) {
                wp_die('Settings configuration file was not set.');
            }

            $loader = new \OKVOauth\App\Libraries\Loader();
            return $loader->loadConfig($setting_file);
        }// getConfigFile


        /**
         * Get settings fields data.
         * 
         * @since 1.8.4
         * @return array Return associative array where key is field `id` and value is `\stdClass` of field item.
         */
        protected function getSettingsFields()
        {
            $settings_config = $this->getConfigFile();
            $output = [];

            if (!is_array($settings_config)) {
                return $output;
            }

            if (is_array($settings_config) && array_key_exists('setting_tabs', $settings_config) && is_array($settings_config['setting_tabs'])) {
                // iterate each tab.
                foreach ($settings_config['setting_tabs'] as $tab_key => $tabs) {
                    if (!is_array($tabs) || !array_key_exists('fields', $tabs) || !is_iterable($tabs['fields'])) {
                        continue;
                    }

                    // iterate each field in this tab.
                    foreach ($tabs['fields'] as $field_key => $fields) {
                        if (!is_array($fields)) {
                            continue;
                        }

                        if (array_key_exists('type', $fields) && 'checkbox' === $fields['type'] && array_key_exists('options', $fields) && is_array($fields['options'])) {
                            // this is checkbox which 1 field can contain multiple checkboxes.
                            foreach ($fields['options'] as $checkbox_key => $checkboxes) {
                                if (!is_array($checkboxes)) {
                                    continue;
                                }

                                $default = '';
                                if (array_key_exists('default', $checkboxes)) {
                                    // if there is default of each checkbox.
                                    $default = $checkboxes['default'];
                                } elseif (array_key_exists('default', $fields)) {
                                    // if there is default of this set of checkboxes.
                                    $default = $fields['default'];
                                }
                                $sanitize_callback = null;
                                if (array_key_exists('sanitize_callback', $checkboxes)) {
                                    $sanitize_callback = $checkboxes['sanitize_callback'];
                                } elseif (array_key_exists('sanitize_callback', $fields)) {
                                    $sanitize_callback = $fields['sanitize_callback'];
                                }

                                if (array_key_exists('id', $checkboxes)) {
                                    $field = new \stdClass();
                                    $field->type = 'checkbox';
                                    $field->default = $default;
                                    $field->value = (isset($checkboxes['value']) ? $checkboxes['value'] : null);
                                    $field->input_attributes = (isset($checkboxes['input_attributes']) ? $checkboxes['input_attributes'] : null);
                                    $field->select_options = null;
                                    $field->sanitize_callback = $sanitize_callback;
                                    $output[$checkboxes['id']] = $field;
                                    unset($field);
                                }
                            }// endforeach; options of checkboxes.
                            unset($checkbox_key, $checkboxes);
                        } else {
                            // this is normal form field. (input, textarea, radio, select)
                            $default = '';
                            if (array_key_exists('default', $fields)) {
                                $default = $fields['default'];
                            }
                            $select_options = null;
                            if (array_key_exists('type', $fields) && 'select' === $fields['type']) {
                                if (array_key_exists('options', $fields)) {
                                    $select_options = $fields['options'];
                                }
                            }

                            if (array_key_exists('id', $fields)) {
                                $field = new \stdClass();
                                $field->type = (isset($fields['type']) ? $fields['type'] : null);
                                $field->default = $default;
                                $field->value = (isset($fields['value']) ? $fields['value'] : null);
                                $field->input_attributes = (isset($fields['input_attributes']) ? $fields['input_attributes'] : null);
                                $field->select_options = $select_options;
                                $field->sanitize_callback = (isset($fields['sanitize_callback']) ? $fields['sanitize_callback'] : null);
                                $output[$fields['id']] = $field;
                                unset($field);
                            }
                        }// endif check field type.
                        unset($default, $sanitize_callback, $select_options);
                    }// endforeach; fields in each tab
                    unset($field_key, $fields);
                }// endforeach; tabs
                unset($tab_key, $tabs);
            }

            unset($settings_config);
            return $output;
        }// getSettingsFields


        /**
         * Get all setting fields id from setting config file.
         * 
         * @return array Return associative array where key is field `id` and value is its default value. It can be returned empty array.
         */
        public function getSettingsFieldsId()
        {
            $fields = $this->getSettingsFields();
            $output = [];
            if (empty($fields)) {
                return $output;
            }

            foreach ($fields as $name => $field) {
                $output[$name] = $field->default;
            }// endforeach; $fields
            unset($field, $name);

            return $output;
        }// getSettingsFieldsId


        /**
         * Get settings page. This is not include form and nonce. You have to write it yourself.
         * 
         * @param array $options_values Options values.
         * @return string Return settings tabbed page. Not include form tag and nonce.
         */
        public function getSettingsPage(array $options_values = [])
        {
            $settings_config = $this->getConfigFile();
            $output = '';

            if (!is_array($settings_config)) {
                return $output;
            }

            // html open tab overall container
            $tab_style = 'vertical';
            if (is_array($settings_config) && array_key_exists('tab_style', $settings_config)) {
                // tab style must be vertical or horizontal.
                if ('vertical' === $settings_config['tab_style'] || 'horizontal' === $settings_config['tab_style']) {
                    $tab_style = $settings_config['tab_style'];
                }
            }
            $output .= '<div class="rd-settings-tabs tabs-' . $tab_style . '">' . "\n";
            unset($tab_style);

            // render tabs -------------------------
            $output .= "\t" . '<ul class="tab-pane">' . "\n";
            if (is_array($settings_config) && array_key_exists('setting_tabs', $settings_config)) {
                foreach ($settings_config['setting_tabs'] as $tab_key => $tabs) {
                    $output .= "\t\t" . '<li>';
                    $output .= '<a href="#tabs-' . $tab_key . '">';
                    if (is_array($tabs) && array_key_exists('icon', $tabs)) {
                        $output .= '<i class="tab-icon ' . $tabs['icon'] . '"></i> ';
                    }
                    $output .= '<span class="tab-text">' . (is_array($tabs) && array_key_exists('title', $tabs) ? $tabs['title'] : '') . '</span>';
                    $output .= '</a>';
                    $output .= '</li>' . "\n";
                }
                unset($tab_key, $tabs);
            }
            // .tab-pane
            $output .= "\t" . '</ul>' . "\n";

            // render tab content ----------------
            $output .= "\t" . '<div class="tab-content">' . "\n";
            if (is_array($settings_config) && array_key_exists('setting_tabs', $settings_config)) {
                foreach ($settings_config['setting_tabs'] as $tab_key => $tabs) {
                    $output .= "\t\t" . '<div id="tabs-' . $tab_key . '">' . "\n";
                    if (is_array($tabs) && array_key_exists('fields', $tabs) && is_array($tabs['fields'])) {
                        $output .= $this->renderFields($tabs['fields'], stripslashes_deep($options_values));
                    }
                    // #tabs-xx
                    $output .= "\t\t" . '</div>' . "\n";
                }
                unset($tab_key, $tabs);
            }
            // .tab-content
            $output .= "\t" . '</div>' . "\n";
            
            // .rd-settings-tabs
            $output .= '</div><!--.rd-settings-tabs-->' . "\n";

            unset($settings_config);
            return $output;
        }// getSettingsPage


        /**
         * Get form submitted data from all available fields id.
         * 
         * @return array Return associative array data that is ready to save or populate form with `getSettingsPage()` method.
         */
        public function getSubmittedData()
        {
            $fields = $this->getSettingsFields();
            $output = [];

            if (is_array($fields)) {
                foreach ($fields as $name => $field) {
                    // get key without square bracket []
                    $nameNoSb = preg_replace('/\[.*?\]/', '', $name);

                    // phpcs:ignore WordPress.Security.NonceVerification
                    if (isset($_REQUEST) && is_array($_REQUEST) && isset($_REQUEST[$nameNoSb])) {
                        if (isset($field->sanitize_callback) && is_callable($field->sanitize_callback)) {
                            // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput
                            $value = call_user_func($field->sanitize_callback, wp_unslash($_REQUEST[$nameNoSb]));
                        } else {
                            $value = wp_unslash($_REQUEST[$nameNoSb]);// phpcs:ignore
                        }
                        $output[$name] = $value;
                        unset($value);
                    } else {
                        $output[$name] = '';
                    }
                }// endforeach;
            }

            unset($field, $fields, $name, $nameNoSb);

            return $output;
        }// getSubmittedData


        /**
         * Render tab content input fields.
         * 
         * @param array $tab_fields Tab fields in settings config.
         * @param array $options_values Options values.
         * @return string Return rendered tab content input fields.
         */
        private function renderFields(array $tab_fields, array $options_values = [])
        {
            $output = "\t\t\t" . '<table class="form-table">' . "\n";
            $output .= "\t\t\t\t" . '<tbody>' . "\n";

            foreach ($tab_fields as $field_key => $fields) {
                if (is_array($fields) && array_key_exists('type', $fields)) {
                    $output .= "\t\t\t\t\t" . '<tr>' . "\n";
                    switch ($fields['type']) {
                        case 'code_editor':
                            $output .= "\t\t\t\t\t\t" . '<th scope="row">' . "\n";
                            $output .= "\t\t\t\t\t\t\t" . '<label>';
                            $output .= (array_key_exists('title', $fields) ? $fields['title'] : '');
                            $output .= '</label>' . "\n";
                            $output .= "\t\t\t\t\t\t" . '</th>' . "\n";
                            $output .= "\t\t\t\t\t\t" . '<td>' . "\n";
                            $output .= "\t\t\t\t\t\t\t" . $this->renderFormCodeEditor($field_key, $fields, $options_values);
                            if (array_key_exists('description', $fields)) {
                                $output .= "\t\t\t\t\t\t\t" . '<p class="description">' . $fields['description'] . '</p>' . "\n";
                            }
                            $output .= "\t\t\t\t\t\t" . '</td>' . "\n";
                            break;
                        case 'code_editor_full':
                            $output .= "\t\t\t\t\t\t" . '<td colspan="2" style="padding-left: 0;">' . "\n";
                            $output .= "\t\t\t\t\t\t\t" . '<div><strong>' . (array_key_exists('title', $fields) ? $fields['title'] : '') . '</strong></div>' . "\n";
                            $output .= "\t\t\t\t\t\t\t" . $this->renderFormCodeEditor($field_key, $fields, $options_values);
                            if (array_key_exists('description', $fields)) {
                                $output .= "\t\t\t\t\t\t\t" . '<p class="description">' . $fields['description'] . '</p>' . "\n";
                            }
                            $output .= "\t\t\t\t\t\t" . '</td>' . "\n";
                            break;

                        case 'editor':
                            $output .= "\t\t\t\t\t\t" . '<th scope="row">' . "\n";
                            $output .= "\t\t\t\t\t\t\t" . '<label>';
                            $output .= (array_key_exists('title', $fields) ? $fields['title'] : '');
                            $output .= '</label>' . "\n";
                            $output .= "\t\t\t\t\t\t" . '</th>' . "\n";
                            $output .= "\t\t\t\t\t\t" . '<td>' . "\n";
                            $output .= "\t\t\t\t\t\t\t" . $this->renderFormEditor($field_key, $fields, $options_values);
                            if (array_key_exists('description', $fields)) {
                                $output .= "\t\t\t\t\t\t\t" . '<p class="description">' . $fields['description'] . '</p>' . "\n";
                            }
                            $output .= "\t\t\t\t\t\t" . '</td>' . "\n";
                            break;
                        case 'editor_full':
                            $output .= "\t\t\t\t\t\t" . '<td colspan="2" style="padding-left: 0;">' . "\n";
                            $output .= "\t\t\t\t\t\t\t" . '<div><strong>' . (array_key_exists('title', $fields) ? $fields['title'] : '') . '</strong></div>' . "\n";
                            $output .= "\t\t\t\t\t\t\t" . $this->renderFormEditor($field_key, $fields, $options_values);
                            if (array_key_exists('description', $fields)) {
                                $output .= "\t\t\t\t\t\t\t" . '<p class="description">' . $fields['description'] . '</p>' . "\n";
                            }
                            $output .= "\t\t\t\t\t\t" . '</td>' . "\n";
                            break;

                        case 'html':
                            $output .= "\t\t\t\t\t\t" . '<th scope="row">' . "\n";
                            $output .= "\t\t\t\t\t\t\t" . '<label>';
                            $output .= (array_key_exists('title', $fields) ? $fields['title'] : '');
                            $output .= '</label>' . "\n";
                            $output .= "\t\t\t\t\t\t" . '</th>' . "\n";
                            $output .= "\t\t\t\t\t\t" . '<td>' . "\n";
                            $output .= "\t\t\t\t\t\t\t" . (array_key_exists('content', $fields) ? $fields['content'] : '') . "\n";
                            $output .= "\t\t\t\t\t\t" . '</td>' . "\n";
                            break;
                        case 'html_full':
                            $output .= "\t\t\t\t\t\t" . '<td colspan="2" style="padding-left: 0;">' . "\n";
                            $output .= "\t\t\t\t\t\t\t" . (array_key_exists('content', $fields) ? $fields['content'] : '') . "\n";
                            $output .= "\t\t\t\t\t\t" . '</td>' . "\n";
                            break;

                        case 'media':
                            $output .= "\t\t\t\t\t\t" . '<th scope="row">' . "\n";
                            $output .= "\t\t\t\t\t\t\t" . '<label>';
                            $output .= (array_key_exists('title', $fields) ? $fields['title'] : '');
                            $output .= '</label>' . "\n";
                            $output .= "\t\t\t\t\t\t" . '</th>' . "\n";
                            $output .= "\t\t\t\t\t\t" . '<td>' . "\n";
                            $output .= "\t\t\t\t\t\t\t" . $this->renderFormMedia($field_key, $fields, $options_values);
                            if (array_key_exists('description', $fields)) {
                                $output .= "\t\t\t\t\t\t\t" . '<p class="description">' . $fields['description'] . '</p>' . "\n";
                            }
                            $output .= "\t\t\t\t\t\t" . '</td>' . "\n";
                            break;
                        case 'checkbox':
                        case 'radio':
                            // input check box like
                        case 'select':
                            // select box
                        case 'color':
                        case 'date':
                        case 'email':
                        case 'number':
                        case 'password':
                        case 'range':
                        case 'textarea':
                        case 'text':
                        case 'url':
                            $output .= "\t\t\t\t\t\t" . '<th scope="row">' . "\n";
                            $output .= "\t\t\t\t\t\t\t" . '<label for="' . (array_key_exists('id', $fields) ? $fields['id'] : $field_key) . '">';
                            $output .= (array_key_exists('title', $fields) ? $fields['title'] : '');
                            $output .= '</label>' . "\n";
                            $output .= "\t\t\t\t\t\t" . '</th>' . "\n";
                            $output .= "\t\t\t\t\t\t" . '<td>' . "\n";
                            $output .= "\t\t\t\t\t\t\t" . $this->renderFormInput($field_key, $fields, $options_values);
                            if (array_key_exists('description', $fields)) {
                                $output .= "\t\t\t\t\t\t\t" . '<p class="description">' . $fields['description'] . '</p>' . "\n";
                            }
                            $output .= "\t\t\t\t\t\t" . '</td>' . "\n";
                            break;
                        default:
                    }
                    $output .= "\t\t\t\t\t" . '</tr>' . "\n";
                }
            }
            unset($field_key, $fields);

            $output .= "\t\t\t\t" . '</tbody>' . "\n";
            $output .= "\t\t\t" . '</table>' . "\n";

            return $output;
        }// renderFields


        /**
         * Render form code editor.
         * 
         * @param int $field_key Key fields array.
         * @param array $fields Fields array.
         * @param array $options_values Options values.
         * @return string Return rendered input.
         * @throws \InvalidArgumentException Throw exception if provided argument type is invalid.
         */
        private function renderFormCodeEditor($field_key, array $fields, array $options_values = [])
        {
            if (!is_numeric($field_key)) {
                throw new \InvalidArgumentException('The argument `$field_key` must be integer.');
            }
            $field_name = (array_key_exists('id', $fields) ? $fields['id'] : $field_key);
            if (is_array($options_values) && array_key_exists($field_name, $options_values)) {
                $field_value = $options_values[$field_name];
            } else {
                $field_value = (array_key_exists('default', $fields) ? $fields['default'] : '');
            }

            $output = '<textarea name="' . $field_name . '" id="textarea-editor-' . $field_name . '">' . esc_textarea($field_value) . '</textarea>' . "\n";
            $output .= '<div id="editor-' . $field_name . '"';
            $output .= ' class="ace-editor ace-editor-display-element"';
            $output .= ' data-target_textarea="#textarea-editor-' . $field_name . '"';
            if (array_key_exists('mode', $fields)) {
                $output .= ' data-editor_mode="' . $fields['mode'] . '"';
            }
            $output .= '>';
            $output .= '</div>' . "\n";

            unset($field_name, $field_value);
            return $output;
        }// renderFormCodeEditor


        /**
         * Render form editor.
         * 
         * @param int $field_key Key fields array.
         * @param array $fields Fields array.
         * @param array $options_values Options values.
         * @return string Return rendered input.
         * @throws \InvalidArgumentException Throw exception if provided argument type is invalid.
         */
        private function renderFormEditor($field_key, array $fields, array $options_values = [])
        {
            if (!is_numeric($field_key)) {
                throw new \InvalidArgumentException('The argument `$field_key` must be integer.');
            }
            $field_name = (array_key_exists('id', $fields) ? $fields['id'] : $field_key);
            if (is_array($options_values) && array_key_exists($field_name, $options_values)) {
                $field_value = $options_values[$field_name];
            } else {
                $field_value = (array_key_exists('default', $fields) ? $fields['default'] : '');
            }

            $settings = [];
            if (is_array($options_values) && array_key_exists('editor_settings', $fields)) {
                $settings = $fields['editor_settings'];
            }

            ob_start();
            wp_editor($field_value, $field_name, $settings);
            $output = ob_get_contents();
            ob_end_clean();

            unset($field_name, $field_value, $settings);
            return $output;
        }// renderFormEditor


        /**
         * Render form input.
         * 
         * @param int $field_key Key fields array.
         * @param array $fields Fields array.
         * @param array $options_values Options values.
         * @return string Return rendered input.
         * @throws \InvalidArgumentException Throw exception if provided argument type is invalid.
         */
        private function renderFormInput($field_key, array $fields, array $options_values = [])
        {
            if (!is_numeric($field_key)) {
                throw new \InvalidArgumentException('The argument `$field_key` must be integer.');
            }
            $field_type = (array_key_exists('type', $fields) ? $fields['type'] : 'text');

            if ('checkbox' === $field_type) {
                // input type checkbox or radio. ------------------------------
                $output = $this->renderFormInputCheckbox($field_key, $fields, $options_values);
            } elseif ('radio' === $field_type) {
                $output = $this->renderFormInputRadio($field_key, $fields, $options_values);
            } elseif ('select' === $field_type) {
                // select box ----------------------------------------------------
                $output = $this->renderFormSelectbox($field_key, $fields, $options_values);
            } elseif ('textarea' === $field_type) {
                // textarea ------------------------------------------------------
                $output = $this->renderFormTextarea($field_key, $fields, $options_values);
            } else {
                // all other input types -----------------------------------------
                $field_name = (array_key_exists('id', $fields) ? $fields['id'] : $field_key);
                // check values
                if (is_array($options_values) && array_key_exists($field_name, $options_values)) {
                    $field_value = $options_values[$field_name];
                } else {
                    $field_value = (array_key_exists('default', $fields) ? $fields['default'] : '');
                }

                $output = '<input';
                $output .= ' id="' . esc_attr($field_name) . '"';
                if (!isset($fields['input_attributes']['class'])) {
                    $output .= ' class="regular-text"';
                }
                $output .= ' type="' . esc_attr($field_type) . '"';
                $output .= ' value="' . esc_attr($field_value) . '"';
                $output .= ' name="' . esc_attr($field_name) . '"';
                if (array_key_exists('input_attributes', $fields) && is_array($fields['input_attributes'])) {
                    foreach ($fields['input_attributes'] as $attribute_name => $attribute_value) {
                        if (!in_array($attribute_name, ['id', 'name', 'type', 'value'], true)) {
                            $output .= ' ' . esc_attr($attribute_name) . '="' . esc_attr($attribute_value) . '"';
                        }
                    }
                    unset($attribute_name, $field_type, $attribute_value);
                }
                $output .= '>' . "\n";
            }

            unset($field_name, $field_value);
            return $output;
        }// renderFormInput


        /**
         * Render form input type checkbox.
         * 
         * @param int $field_key Key fields array.
         * @param array $fields Fields array.
         * @param array $options_values Options values.
         * @return string Return rendered input.
         * @throws \InvalidArgumentException Throw exception if provided argument type is invalid.
         */
        private function renderFormInputCheckbox($field_key, array $fields, array $options_values = [])
        {
            if (!is_numeric($field_key)) {
                throw new \InvalidArgumentException('The argument `$field_key` must be integer.');
            }
            // get default values. (for array check box only).
            if (array_key_exists('default', $fields)) {
                $field_value_array = (array) $fields['default'];
            }

            if (array_key_exists('options', $fields) && is_array($fields['options'])) {
                $output = '<fieldset>' . "\n";
                $output .= '<legend class="screen-reader-text">' . (array_key_exists('title', $fields) ? $fields['title'] : '') . '</legend>' . "\n";
                $i = 1;
                foreach ($fields['options'] as $checkbox_key => $checkboxes) {
                    $checkbox_id = (array_key_exists('id', $checkboxes) ? $checkboxes['id'] : '');

                    if (is_array($checkboxes)) {
                        $output .= '<label>';
                        $output .= '<input type="checkbox"';
                        $output .= ' name="' . esc_attr($checkbox_id) . '"';
                        if (array_key_exists('value', $checkboxes)) {
                            $output .= ' value="' . esc_attr($checkboxes['value']) . '"';
                            if (strpos($checkbox_id, '[') === false) {
                                // this is not check box array.
                                if (!is_array($options_values) || (is_array($options_values) && !array_key_exists($checkbox_id, $options_values))) {
                                    // no saved value, use default.
                                    $field_value = (array_key_exists('default', $checkboxes) ? $checkboxes['default'] : '');
                                }
                                if (is_array($options_values) && array_key_exists($checkbox_id, $options_values)) {
                                    $field_value = $options_values[$checkbox_id];
                                }
                                
                                if (isset($field_value) && strval($checkboxes['value']) === strval($field_value)) {
                                    $output .= ' checked="checked"';
                                }
                            } else {
                                // this is check box array.
                                // check that options values contain this checked. this can override default automatically.
                                if (is_array($options_values) && array_key_exists($checkbox_id, $options_values)) {
                                    $field_value_array = (array) $options_values[$checkbox_id];
                                }

                                $field_value = (isset($field_value_array) ? $field_value_array : []);

                                // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
                                if (isset($field_value) && is_array($field_value) && in_array($checkboxes['value'], $field_value)) {
                                    $output .= ' checked="checked"';
                                }
                            }
                        }
                        if (array_key_exists('input_attributes', $checkboxes) && is_array($checkboxes['input_attributes'])) {
                            foreach ($checkboxes['input_attributes'] as $attribute_name => $attribute_value) {
                                if (!in_array($attribute_name, ['id', 'name', 'type', 'value', 'checked'], true)) {
                                    $output .= ' ' . esc_attr($attribute_name) . '="' . esc_attr($attribute_value) . '"';
                                }
                            }
                            unset($attribute_name, $attribute_value);
                        }
                        $output .= '>';
                        if (array_key_exists('title', $checkboxes)) {
                            $output .= ' ' . $checkboxes['title'];
                        }
                        $output .= '</label>' . "\n";
                        if (array_key_exists('description', $checkboxes)) {
                            $output .= '<p class="description">' . $checkboxes['description'] . '</p>';
                        }

                        if (!array_key_exists('description', $checkboxes) && $i < count($fields['options'])) {
                            $output .= '<br>' . "\n";
                        }
                        ++$i;
                    }

                    unset($checkbox_id, $field_value);
                }// endforeach;
                unset($checkbox_key, $checkboxes, $i);
                $output .= '</fieldset>' . "\n";
            }

            unset($field_value_array);
            if (isset($output)) {
                return $output;
            }

            return '';
        }// renderFormInputCheckbox


        /**
         * Render form input radio.
         * 
         * @param int $field_key Key fields array.
         * @param array $fields Fields array.
         * @param array $options_values Options values.
         * @return string Return rendered input.
         * @throws \InvalidArgumentException Throw exception if provided argument type is invalid.
         */
        private function renderFormInputRadio($field_key, array $fields, array $options_values = [])
        {
            if (!is_numeric($field_key)) {
                throw new \InvalidArgumentException('The argument `$field_key` must be integer.');
            }
            $field_name = (array_key_exists('id', $fields) ? $fields['id'] : '');
            // check values
            if (is_array($options_values) && array_key_exists($field_name, $options_values)) {
                $field_value = $options_values[$field_name];
            } else {
                $field_value = (array_key_exists('default', $fields) ? $fields['default'] : '');
            }

            if (array_key_exists('options', $fields) && is_array($fields['options'])) {
                $output = '<fieldset>' . "\n";
                $output .= '<legend class="screen-reader-text">' . (array_key_exists('title', $fields) ? $fields['title'] : '') . '</legend>' . "\n";
                $i = 1;
                foreach ($fields['options'] as $radio_key => $radio_buttons) {
                    if (is_array($radio_buttons)) {
                        $output .= '<label>';
                        $output .= '<input type="radio"';
                        $output .= ' name="' . esc_attr($field_name) . '"';
                        if (array_key_exists('value', $radio_buttons)) {
                            $output .= ' value="' . esc_attr($radio_buttons['value']) . '"';
                            if (strval($field_value) === strval($radio_buttons['value'])) {
                                $output .= ' checked="checked"';
                            }
                        }
                        if (array_key_exists('input_attributes', $radio_buttons) && is_array($radio_buttons['input_attributes'])) {
                            foreach ($radio_buttons['input_attributes'] as $attribute_name => $attribute_value) {
                                if (!in_array($attribute_name, ['id', 'name', 'type', 'value', 'checked'], true)) {
                                    $output .= ' ' . esc_attr($attribute_name) . '="' . esc_attr($attribute_value) . '"';
                                }
                            }
                            unset($attribute_name, $attribute_value);
                        }
                        $output .= '>';
                        if (array_key_exists('title', $radio_buttons)) {
                            $output .= ' ' . $radio_buttons['title'];
                        }
                        $output .= '</label>' . "\n";
                        if (array_key_exists('description', $radio_buttons)) {
                            $output .= '<p class="description">' . $radio_buttons['description'] . '</p>';
                        }

                        if (!array_key_exists('description', $radio_buttons) && $i < count($fields['options'])) {
                            $output .= '<br>' . "\n";
                        }
                        ++$i;
                    }
                }// endforeach;
                unset($i, $radio_buttons, $radio_key);
                $output .= '</fieldset>' . "\n";
            }

            unset($field_name, $field_value);
            if (isset($output)) {
                return $output;
            }

            return '';
        }// renderFormInputRadio


        /**
         * Render form media upload.
         * 
         * @param int $field_key Key fields array.
         * @param array $fields Fields array.
         * @param array $options_values Options values.
         * @return string Return rendered input.
         * @throws \InvalidArgumentException Throw exception if provided argument type is invalid.
         */
        private function renderFormMedia($field_key, array $fields, array $options_values = [])
        {
            if (!is_numeric($field_key)) {
                throw new \InvalidArgumentException('The argument `$field_key` must be integer.');
            }
            wp_enqueue_script('jquery');
            wp_enqueue_media();

            $field_name = (array_key_exists('id', $fields) ? $fields['id'] : $field_key);
            // check values
            $field_values = [];
            if (is_array($options_values) && array_key_exists($field_name, $options_values)) {
                $field_values = $options_values[$field_name];
            }

            $preview_mode = 'preview_all';
            if (array_key_exists('mode', $fields)) {
                if (in_array($fields['mode'], ['preview_all', 'preview_url', 'preview_img', 'no_preview_img', 'no_preview_url'], true)) {
                    $preview_mode = $fields['mode'];
                }
            }

            $output = '';
            if ('preview_all' === $preview_mode || 'preview_url' === $preview_mode || 'no_preview_img' === $preview_mode) {
                $output = '<input type="text" id="preview-media-url-' . esc_attr($field_name) . '" class="large-text" value="' . (is_array($field_values) && array_key_exists('url', $field_values) ? esc_url($field_values['url']) : '') . '" readonly>' . "\n";
            }
            $output .= '<input type="hidden" id="media-id-' . esc_attr($field_name) . '" name="' . esc_attr($field_name) . '[id]" value="' . (is_array($field_values) && array_key_exists('id', $field_values) ? esc_attr($field_values['id']) : '') . '">' . "\n";
            $output .= '<input type="hidden" id="media-height-' . esc_attr($field_name) . '" name="' . esc_attr($field_name) . '[height]" value="' . (is_array($field_values) && array_key_exists('height', $field_values) ? esc_attr($field_values['height']) : '') . '">' . "\n";
            $output .= '<input type="hidden" id="media-width-' . esc_attr($field_name) . '" name="' . esc_attr($field_name) . '[width]" value="' . (is_array($field_values) && array_key_exists('width', $field_values) ? esc_attr($field_values['width']) : '') . '">' . "\n";
            $output .= '<input type="hidden" id="media-url-' . esc_attr($field_name) . '" name="' . esc_attr($field_name) . '[url]" value="' . (is_array($field_values) && array_key_exists('url', $field_values) ? esc_url($field_values['url']) : '') . '">' . "\n";
            $output .= '<input type="hidden" id="media-large-' . esc_attr($field_name) . '" name="' . esc_attr($field_name) . '[large]" value="' . (is_array($field_values) && array_key_exists('large', $field_values) ? esc_attr($field_values['large']) : '') . '">' . "\n";
            $output .= '<input type="hidden" id="media-medium-' . esc_attr($field_name) . '" name="' . esc_attr($field_name) . '[medium]" value="' . (is_array($field_values) && array_key_exists('medium', $field_values) ? esc_attr($field_values['medium']) : '') . '">' . "\n";
            $output .= '<input type="hidden" id="media-thumbnail-' . esc_attr($field_name) . '" name="' . esc_attr($field_name) . '[thumbnail]" value="' . (is_array($field_values) && array_key_exists('thumbnail', $field_values) ? esc_attr($field_values['thumbnail']) : '') . '">' . "\n";
            $output .= '<br>' . "\n";
            if ('preview_all' === $preview_mode || 'preview_img' === $preview_mode || 'no_preview_url' === $preview_mode) {
                $output .= '<div class="image-preview image-preview-' . esc_attr($field_name) . '">';
                if (is_array($field_values) && array_key_exists('thumbnail', $field_values) && '' !== $field_values['thumbnail']) {
                    $output .= '<img src="' . esc_attr($field_values['thumbnail']) . '" alt="">';
                }
                $output .= '</div>' . "\n";
            }
            $output .= '<input type="button" class="button-secondary upload-media-button" value="' . __('Upload', 'okv-oauth') . '" data-input_target="' . esc_attr($field_name) . '">' . "\n";
            $output .= '<input type="button" class="button-secondary remove-media-button" value="' . __('Remove', 'okv-oauth') . '" data-input_target="' . esc_attr($field_name) . '">' . "\n";

            unset($field_name, $field_values, $preview_mode);
            return $output;
        }// renderFormMedia


        /**
         * Render form select box.
         * 
         * @param int $field_key Key fields array.
         * @param array $fields Fields array.
         * @param array $options_values Options values.
         * @return string Return rendered input.
         * @throws \InvalidArgumentException Throw exception if provided argument type is invalid.
         */
        private function renderFormSelectbox($field_key, array $fields, array $options_values = [])
        {
            if (!is_numeric($field_key)) {
                throw new \InvalidArgumentException('The argument `$field_key` must be integer.');
            }
            $field_type = (array_key_exists('type', $fields) ? $fields['type'] : 'text');
            $field_name = (array_key_exists('id', $fields) ? $fields['id'] : $field_key);
            // check values
            if (is_array($options_values) && array_key_exists($field_name, $options_values)) {
                $field_value = $options_values[$field_name];
            } else {
                $field_value = (array_key_exists('default', $fields) ? $fields['default'] : '');
            }

            $output = '<select';
            $output .= ' id="' . esc_attr($field_name) . '"';
            $output .= ' name="' . esc_attr($field_name) . '"';
            if (array_key_exists('input_attributes', $fields) && is_array($fields['input_attributes'])) {
                foreach ($fields['input_attributes'] as $attribute_name => $attribute_value) {
                    if (!in_array($attribute_name, ['id', 'name'], true)) {
                        $output .= ' ' . esc_attr($attribute_name) . '="' . esc_attr($attribute_value) . '"';
                    }
                }
                unset($attribute_name, $field_type, $attribute_value);
            }
            $output .= '>' . "\n";
            if (array_key_exists('options', $fields)) {
                foreach ($fields['options'] as $option_key => $option_item1) {
                    if (is_array($option_item1)) {
                        $output .= '<optgroup label="' . esc_attr($option_key) . '">' . "\n";
                        foreach ($option_item1 as $option_item2 => $option_item3) {
                            $output .= '<option value="' . esc_attr($option_item2) . '"';
                            if (!is_array($field_value) && strval($field_value) === strval($option_item2)) {
                                $output .= ' selected="selected"';
                            } elseif (is_array($field_value) && in_array($option_item2, $field_value)) {// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
                                $output .= ' selected="selected"';
                            }
                            $output .= '>';
                            $output .= esc_html($option_item3);
                            $output .= '</option>' . "\n";
                        }
                        $output .= '</optgroup>' . "\n";
                    } else {
                        $output .= '<option value="' . esc_attr($option_key) . '"';
                        if (!is_array($field_value) && strval($field_value) === strval($option_key)) {
                            $output .= ' selected="selected"';
                        } elseif (is_array($field_value) && in_array($option_key, $field_value)) {// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
                            $output .= ' selected="selected"';
                        }
                        $output .= '>';
                        $output .= esc_html($option_item1);
                        $output .= '</option>' . "\n";
                    }
                }// endforeach;
                unset($option_item1, $option_item2, $option_item3, $option_key);
            }
            $output .= '</select>' . "\n";

            unset($field_name, $field_value);
            return $output;
        }// renderFormSelectbox


        /**
         * Render form textarea.
         * 
         * @param int $field_key Key fields array.
         * @param array $fields Fields array.
         * @param array $options_values Options values.
         * @return string Return rendered input.
         * @throws \InvalidArgumentException Throw exception if provided argument type is invalid.
         */
        private function renderFormTextarea($field_key, array $fields, array $options_values = [])
        {
            if (!is_numeric($field_key)) {
                throw new \InvalidArgumentException('The argument `$field_key` must be integer.');
            }
            $field_type = (array_key_exists('type', $fields) ? $fields['type'] : 'text');
            $field_name = (array_key_exists('id', $fields) ? $fields['id'] : $field_key);
            // check values
            if (is_array($options_values) && array_key_exists($field_name, $options_values)) {
                $field_value = $options_values[$field_name];
            } else {
                $field_value = (array_key_exists('default', $fields) ? $fields['default'] : '');
            }

            $output = '<textarea';
            $output .= ' id="' . esc_attr($field_name) . '"';
            if (!isset($fields['input_attributes']['class'])) {
                $output .= ' class="large-text"';
            }
            $output .= ' name="' . esc_attr($field_name) . '"';
            if (array_key_exists('input_attributes', $fields)) {
                foreach ($fields['input_attributes'] as $attribute_name => $attribute_value) {
                    if (!in_array($attribute_name, ['id', 'name', 'type', 'value'], true)) {
                        $output .= ' ' . esc_attr($attribute_name) . '="' . esc_attr($attribute_value) . '"';
                    }
                }
                unset($attribute_name, $field_type, $attribute_value);
            }
            $output .= '>';
            $output .= esc_textarea($field_value);
            $output .= '</textarea>' . "\n";

            unset($field_name, $field_value);
            return $output;
        }// renderFormTextarea


    }// RundizSettings
}
