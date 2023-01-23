<?php
/**
 * Rundiz Plugin template
 * 
 * @package rundiz-wordpress-plugin-server
 */


namespace RundizOauth\App\Libraries;

/**
 * RundizSettings class for render pre-setup values. This will render tabs, form fields and content in each tabs.
 */
if (!class_exists('\\RundizOauth\\App\\Libraries\\RundizSettings')) {
    class RundizSettings
    {


        /**
         * settings config file.
         * @var type 
         */
        public $settings_config_file;


        /**
         * get settings config file and its data.
         * 
         * @return array return settings config data.
         */
        private function getConfigFile()
        {
            $setting_file = $this->settings_config_file;

            if (empty($setting_file) || !is_string($setting_file)) {
                wp_die('Settings configuration file was not set.');
            }

            $loader = new \RundizOauth\App\Libraries\Loader();
            return $loader->loadConfig($setting_file);
        }// getConfigFile


        /**
         * get all setting fields id from setting config file.
         * 
         * @return array return array of setting id and default value.
         */
        public function getSettingsFieldsId()
        {
            $settings_config = $this->getConfigFile();
            $output = [];

            if (!is_array($settings_config)) {
                return $output;
            }

            if (is_array($settings_config) && array_key_exists('setting_tabs', $settings_config) && is_array($settings_config['setting_tabs'])) {
                foreach ($settings_config['setting_tabs'] as $tab_key => $tabs) {
                    if (is_array($tabs) && array_key_exists('fields', $tabs)) {
                        foreach ($tabs['fields'] as $field_key => $fields) {
                            if (is_array($fields)) {
                                if (array_key_exists('type', $fields) && 'checkbox' === $fields['type'] && array_key_exists('options', $fields) && is_array($fields['options'])) {
                                    // this is checkbox which 1 field can contain multiple checkboxes.
                                    foreach ($fields['options'] as $checkbox_key => $checkboxes) {
                                        if (is_array($checkboxes)) {
                                            $default = '';
                                            if (array_key_exists('default', $checkboxes)) {
                                                $default = $checkboxes['default'];
                                            }

                                            if (array_key_exists('id', $checkboxes)) {
                                                $output[$checkboxes['id']] = $default;
                                            }
                                        }
                                    }
                                    unset($checkbox_key, $checkboxes);
                                } else {
                                    // this is normal form field. (input, textarea, radio, select)
                                    $default = '';
                                    if (array_key_exists('default', $fields)) {
                                        $default = $fields['default'];
                                    }

                                    if (array_key_exists('id', $fields)) {
                                        $output[$fields['id']] = $default;
                                    }

                                }
                                unset($default);
                            }// endif is_array($fields)
                        }// endforeach;
                        unset($field_key, $fields);
                    }
                }// endforeach;
                unset($tab_key, $tabs);
            }

            unset($settings_config);
            return $output;
        }// getSettingsFieldsId


        /**
         * get settings page. this is not include form and nonce. you have to write it yourself.
         * 
         * @param array $options_values options values.
         * @return string return settings tabbed page. not include form and nonce.
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
            $output .= '<div class="rd-settings-tabs tabs-'.$tab_style.'">'."\n";
            unset($tab_style);

            // render tabs -------------------------
            $output .= "\t".'<ul class="tab-pane">'."\n";
            if (is_array($settings_config) && array_key_exists('setting_tabs', $settings_config)) {
                foreach ($settings_config['setting_tabs'] as $tab_key => $tabs) {
                    $output .= "\t\t".'<li>';
                    $output .= '<a href="#tabs-'.$tab_key.'">';
                    if (is_array($tabs) && array_key_exists('icon', $tabs)) {
                        $output .= '<i class="tab-icon '.$tabs['icon'].'"></i> ';
                    }
                    $output .= '<span class="tab-text">'.(is_array($tabs) && array_key_exists('title', $tabs) ? $tabs['title'] : '').'</span>';
                    $output .= '</a>';
                    $output .= '</li>'."\n";
                }
                unset($tab_key, $tabs);
            }
            // .tab-pane
            $output .= "\t".'</ul>'."\n";

            // render tab content ----------------
            $output .= "\t".'<div class="tab-content">'."\n";
            if (is_array($settings_config) && array_key_exists('setting_tabs', $settings_config)) {
                foreach ($settings_config['setting_tabs'] as $tab_key => $tabs) {
                    $output .= "\t\t".'<div id="tabs-'.$tab_key.'">'."\n";
                    if (is_array($tabs) && array_key_exists('fields', $tabs) && is_array($tabs['fields'])) {
                        $output .= $this->renderFields($tabs['fields'], stripslashes_deep($options_values));
                    }
                    // #tabs-xx
                    $output .= "\t\t".'</div>'."\n";
                }
                unset($tab_key, $tabs);
            }
            // .tab-content
            $output .= "\t".'</div>'."\n";
            
            // .rd-settings-tabs
            $output .= '</div><!--.rd-settings-tabs-->'."\n";

            unset($settings_config);
            return $output;
        }// getSettingsPage


        /**
         * get form submitted data from all available fields id.
         * 
         * @return array return array data that is ready to save or populate form with getSettingsPage() method.
         */
        public function getSubmittedData()
        {
            $fields_id = $this->getSettingsFieldsId();
            $output = [];

            if (is_array($fields_id)) {
                foreach ($fields_id as $key => $item) {
                    // get key without square bracket []
                    $key_no_sqb = preg_replace('/\[.*?\]/', '', $key);

                    if (isset($_REQUEST[$key_no_sqb])) {
                        $output[$key] = sanitize_text_field(wp_unslash($_REQUEST[$key_no_sqb]));
                    } else {
                        $output[$key] = '';
                    }
                }// endforeach;
            }

            unset($fields_id, $item, $key, $key_no_sqb);

            return $output;
        }// getSubmittedData


        /**
         * render tab content input fields.
         * 
         * @param array $tab_fields tab fields in settings config.
         * @param array $options_values options values.
         * @return string return rendered tab content input fields.
         */
        private function renderFields(array $tab_fields, array $options_values = [])
        {
            $output = "\t\t\t".'<table class="form-table">'."\n";
            $output .= "\t\t\t\t".'<tbody>'."\n";

            foreach ($tab_fields as $field_key => $fields) {
                if (is_array($fields) && array_key_exists('type', $fields)) {
                    $output .= "\t\t\t\t\t".'<tr>'."\n";
                    switch ($fields['type']) {
                        case 'code_editor':
                            $output .= "\t\t\t\t\t\t".'<th scope="row">'."\n";
                            $output .= "\t\t\t\t\t\t\t".'<label>';
                            $output .= (array_key_exists('title', $fields) ? $fields['title'] : '');
                            $output .= '</label>'."\n";
                            $output .= "\t\t\t\t\t\t".'</th>'."\n";
                            $output .= "\t\t\t\t\t\t".'<td>'."\n";
                            $output .= "\t\t\t\t\t\t\t".$this->renderFormCodeEditor($field_key, $fields, $options_values);
                            if (array_key_exists('description', $fields)) {
                                $output .= "\t\t\t\t\t\t\t".'<p class="description">'.$fields['description'].'</p>'."\n";
                            }
                            $output .= "\t\t\t\t\t\t".'</td>'."\n";
                            break;
                        case 'code_editor_full':
                            $output .= "\t\t\t\t\t\t".'<td colspan="2" style="padding-left: 0;">'."\n";
                            $output .= "\t\t\t\t\t\t\t".'<div><strong>'.(array_key_exists('title', $fields) ? $fields['title'] : '').'</strong></div>'."\n";
                            $output .= "\t\t\t\t\t\t\t".$this->renderFormCodeEditor($field_key, $fields, $options_values);
                            if (array_key_exists('description', $fields)) {
                                $output .= "\t\t\t\t\t\t\t".'<p class="description">'.$fields['description'].'</p>'."\n";
                            }
                            $output .= "\t\t\t\t\t\t".'</td>'."\n";
                            break;

                        case 'editor':
                            $output .= "\t\t\t\t\t\t".'<th scope="row">'."\n";
                            $output .= "\t\t\t\t\t\t\t".'<label>';
                            $output .= (array_key_exists('title', $fields) ? $fields['title'] : '');
                            $output .= '</label>'."\n";
                            $output .= "\t\t\t\t\t\t".'</th>'."\n";
                            $output .= "\t\t\t\t\t\t".'<td>'."\n";
                            $output .= "\t\t\t\t\t\t\t".$this->renderFormEditor($field_key, $fields, $options_values);
                            if (array_key_exists('description', $fields)) {
                                $output .= "\t\t\t\t\t\t\t".'<p class="description">'.$fields['description'].'</p>'."\n";
                            }
                            $output .= "\t\t\t\t\t\t".'</td>'."\n";
                            break;
                        case 'editor_full':
                            $output .= "\t\t\t\t\t\t".'<td colspan="2" style="padding-left: 0;">'."\n";
                            $output .= "\t\t\t\t\t\t\t".'<div><strong>'.(array_key_exists('title', $fields) ? $fields['title'] : '').'</strong></div>'."\n";
                            $output .= "\t\t\t\t\t\t\t".$this->renderFormEditor($field_key, $fields, $options_values);
                            if (array_key_exists('description', $fields)) {
                                $output .= "\t\t\t\t\t\t\t".'<p class="description">'.$fields['description'].'</p>'."\n";
                            }
                            $output .= "\t\t\t\t\t\t".'</td>'."\n";
                            break;

                        case 'html':
                            $output .= "\t\t\t\t\t\t".'<th scope="row">'."\n";
                            $output .= "\t\t\t\t\t\t\t".'<label>';
                            $output .= (array_key_exists('title', $fields) ? $fields['title'] : '');
                            $output .= '</label>'."\n";
                            $output .= "\t\t\t\t\t\t".'</th>'."\n";
                            $output .= "\t\t\t\t\t\t".'<td>'."\n";
                            $output .= "\t\t\t\t\t\t\t".(array_key_exists('content', $fields) ? $fields['content'] : '')."\n";
                            $output .= "\t\t\t\t\t\t".'</td>'."\n";
                            break;
                        case 'html_full':
                            $output .= "\t\t\t\t\t\t".'<td colspan="2" style="padding-left: 0;">'."\n";
                            $output .= "\t\t\t\t\t\t\t".(array_key_exists('content', $fields) ? $fields['content'] : '')."\n";
                            $output .= "\t\t\t\t\t\t".'</td>'."\n";
                            break;

                        case 'media':
                            $output .= "\t\t\t\t\t\t".'<th scope="row">'."\n";
                            $output .= "\t\t\t\t\t\t\t".'<label>';
                            $output .= (array_key_exists('title', $fields) ? $fields['title'] : '');
                            $output .= '</label>'."\n";
                            $output .= "\t\t\t\t\t\t".'</th>'."\n";
                            $output .= "\t\t\t\t\t\t".'<td>'."\n";
                            $output .= "\t\t\t\t\t\t\t".$this->renderFormMedia($field_key, $fields, $options_values);
                            if (array_key_exists('description', $fields)) {
                                $output .= "\t\t\t\t\t\t\t".'<p class="description">'.$fields['description'].'</p>'."\n";
                            }
                            $output .= "\t\t\t\t\t\t".'</td>'."\n";
                            break;

                        case 'checkbox':
                        case 'radio':
                            // above is group of input clicking.
                        case 'select':
                            // above is select box.
                        case 'color':
                        case 'date':
                        case 'email':
                        case 'number':
                        case 'password':
                        case 'range':
                        case 'textarea':
                        case 'text':
                        case 'url':
                            $output .= "\t\t\t\t\t\t".'<th scope="row">'."\n";
                            $output .= "\t\t\t\t\t\t\t".'<label for="'.(array_key_exists('id', $fields) ? $fields['id'] : $field_key).'">';
                            $output .= (array_key_exists('title', $fields) ? $fields['title'] : '');
                            $output .= '</label>'."\n";
                            $output .= "\t\t\t\t\t\t".'</th>'."\n";
                            $output .= "\t\t\t\t\t\t".'<td>'."\n";
                            $output .= "\t\t\t\t\t\t\t".$this->renderFormInput($field_key, $fields, $options_values);
                            if (array_key_exists('description', $fields)) {
                                $output .= "\t\t\t\t\t\t\t".'<p class="description">'.$fields['description'].'</p>'."\n";
                            }
                            $output .= "\t\t\t\t\t\t".'</td>'."\n";
                            break;
                        default:
                    }
                    $output .= "\t\t\t\t\t".'</tr>'."\n";
                }
            }
            unset($field_key, $fields);

            $output .= "\t\t\t\t".'</tbody>'."\n";
            $output .= "\t\t\t".'</table>'."\n";

            return $output;
        }// renderFields


        /**
         * render form code editor.
         * 
         * @param integer $field_key key fields array.
         * @param array $fields fields array.
         * @param array $options_values options values.
         * @return string return rendered input.
         */
        private function renderFormCodeEditor($field_key, array $fields, array $options_values = [])
        {
            $field_name = (array_key_exists('id', $fields) ? $fields['id'] : $field_key);
            if (is_array($options_values) && array_key_exists($field_name, $options_values)) {
                $field_value = $options_values[$field_name];
            } else {
                $field_value = (array_key_exists('default', $fields) ? $fields['default'] : '');
            }

            $output = '<textarea name="'.$field_name.'" id="textarea-editor-'.$field_name.'">'.esc_textarea($field_value).'</textarea>'."\n";
            $output .= '<div id="editor-'.$field_name.'"';
            $output .= ' class="ace-editor ace-editor-display-element"';
            $output .= ' data-target_textarea="#textarea-editor-'.$field_name.'"';
            if (array_key_exists('mode', $fields)) {
                $output .= ' data-editor_mode="'.$fields['mode'].'"';
            }
            $output .= '>';
            $output .= '</div>'."\n";

            unset($field_name, $field_value);
            return $output;
        }// renderFormCodeEditor


        /**
         * render form editor.
         * 
         * @param integer $field_key key fields array.
         * @param array $fields fields array.
         * @param array $options_values options values.
         * @return string return rendered input.
         */
        private function renderFormEditor($field_key, array $fields, array $options_values = [])
        {
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
         * render form input.
         * 
         * @param integer $field_key key fields array.
         * @param array $fields fields array.
         * @param array $options_values options values.
         * @return string return rendered input.
         */
        private function renderFormInput($field_key, array $fields, array $options_values = [])
        {
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
                $output .= ' id="'.$field_name.'"';
                if (!isset($fields['input_attributes']['class'])) {
                    $output .= ' class="regular-text"';
                }
                $output .= ' type="'.$field_type.'"';
                $output .= ' value="'.esc_attr($field_value).'"';
                $output .= ' name="'.$field_name.'"';
                if (array_key_exists('input_attributes', $fields) && is_array($fields['input_attributes'])) {
                    foreach ($fields['input_attributes'] as $attribute_name => $attribute_value) {
                        if (!in_array($attribute_name, ['id', 'name', 'type', 'value'])) {
                            $output .= ' '.$attribute_name.'="'.$attribute_value.'"';
                        }
                    }
                    unset($attribute_name, $field_type, $attribute_value);
                }
                $output .= '>'."\n";
            }

            unset($field_name, $field_value);
            return $output;
        }// renderFormInput


        /**
         * render form input type checkbox.
         * 
         * @param integer $field_key key fields array.
         * @param array $fields fields array.
         * @param array $options_values options values.
         * @return string return rendered input.
         */
        private function renderFormInputCheckbox($field_key, array $fields, array $options_values = [])
        {
            // get default values. (for array check box only).
            if (array_key_exists('default', $fields)) {
                $field_value_array = (array) $fields['default'];
            }

            if (array_key_exists('options', $fields) && is_array($fields['options'])) {
                $output = '<fieldset>'."\n";
                $output .= '<legend class="screen-reader-text">'.(array_key_exists('title', $fields) ? $fields['title'] : '').'</legend>'."\n";
                $i = 1;
                foreach ($fields['options'] as $checkbox_key => $checkboxes) {
                    $checkbox_id = (array_key_exists('id', $checkboxes) ? $checkboxes['id'] : '');

                    if (is_array($checkboxes)) {
                        $output .= '<label>';
                        $output .= '<input type="checkbox"';
                        $output .= ' name="'.$checkbox_id.'"';
                        if (array_key_exists('value', $checkboxes)) {
                            $output .= ' value="'.$checkboxes['value'].'"';
                            if (strpos($checkbox_id, '[') === false) {
                                // this is not check box array.
                                if (!is_array($options_values) || (is_array($options_values) && !array_key_exists($checkbox_id, $options_values))) {
                                    // no saved value, use default.
                                    $field_value = (array_key_exists('default', $checkboxes) ? $checkboxes['default'] : '');
                                }
                                if (is_array($options_values) && array_key_exists($checkbox_id, $options_values)) {
                                    $field_value = $options_values[$checkbox_id];
                                }
                                
                                if (isset($field_value) && $checkboxes['value'] === $field_value) {
                                    $output .= ' checked="checked"';
                                }
                            } else {
                                // this is check box array.
                                // check that options values contain this checked. this can override default automatically.
                                if (is_array($options_values) && array_key_exists($checkbox_id, $options_values)) {
                                    $field_value_array = (array) $options_values[$checkbox_id];
                                }

                                $field_value = (isset($field_value_array) ? $field_value_array : []);

                                if (isset($field_value) && is_array($field_value) && in_array($checkboxes['value'], $field_value)) {
                                    $output .= ' checked="checked"';
                                }
                            }
                        }
                        if (array_key_exists('input_attributes', $checkboxes) && is_array($checkboxes['input_attributes'])) {
                            foreach ($checkboxes['input_attributes'] as $attribute_name => $attribute_value) {
                                if (!in_array($attribute_name, ['id', 'name', 'type', 'value', 'checked'])) {
                                    $output .= ' '.$attribute_name.'="'.$attribute_value.'"';
                                }
                            }
                            unset($attribute_name, $attribute_value);
                        }
                        $output .= '>';
                        if (array_key_exists('title', $checkboxes)) {
                            $output .= ' '.$checkboxes['title'];
                        }
                        $output .= '</label>'."\n";
                        if (array_key_exists('description', $checkboxes)) {
                            $output .= '<p class="description">' . $checkboxes['description'] . '</p>';
                        }

                        if (!array_key_exists('description', $checkboxes) && $i < count($fields['options'])) {
                            $output .= '<br>'."\n";
                        }
                        ++$i;
                    }

                    unset($checkbox_id, $field_value);
                }// endforeach;
                unset($checkbox_key, $checkboxes, $i);
                $output .= '</fieldset>'."\n";
            }

            unset($field_value_array);
            if (isset($output)) {
                return $output;
            }
        }// renderFormInputCheckbox


        /**
         * render form input radio.
         * 
         * @param integer $field_key key fields array.
         * @param array $fields fields array.
         * @param array $options_values options values.
         * @return string return rendered input.
         */
        private function renderFormInputRadio($field_key, array $fields, array $options_values = [])
        {
            $field_name = (array_key_exists('id', $fields) ? $fields['id'] : '');
            // check values
            if (is_array($options_values) && array_key_exists($field_name, $options_values)) {
                $field_value = $options_values[$field_name];
            } else {
                $field_value = (array_key_exists('default', $fields) ? $fields['default'] : '');
            }

            if (array_key_exists('options', $fields) && is_array($fields['options'])) {
                $output = '<fieldset>'."\n";
                $output .= '<legend class="screen-reader-text">'.(array_key_exists('title', $fields) ? $fields['title'] : '').'</legend>'."\n";
                $i = 1;
                foreach ($fields['options'] as $radio_key => $radio_buttons) {
                    if (is_array($radio_buttons)) {
                        $output .= '<label>';
                        $output .= '<input type="radio"';
                        $output .= ' name="'.$field_name.'"';
                        if (array_key_exists('value', $radio_buttons)) {
                            $output .= ' value="'.$radio_buttons['value'].'"';
                            if ($field_value === $radio_buttons['value']) {
                                $output .= ' checked="checked"';
                            }
                        }
                        if (array_key_exists('input_attributes', $radio_buttons) && is_array($radio_buttons['input_attributes'])) {
                            foreach ($radio_buttons['input_attributes'] as $attribute_name => $attribute_value) {
                                if (!in_array($attribute_name, ['id', 'name', 'type', 'value', 'checked'])) {
                                    $output .= ' '.$attribute_name.'="'.$attribute_value.'"';
                                }
                            }
                            unset($attribute_name, $attribute_value);
                        }
                        $output .= '>';
                        if (array_key_exists('title', $radio_buttons)) {
                            $output .= ' '.$radio_buttons['title'];
                        }
                        $output .= '</label>'."\n";
                        if (array_key_exists('description', $radio_buttons)) {
                            $output .= '<p class="description">' . $radio_buttons['description'] . '</p>';
                        }

                        if (!array_key_exists('description', $radio_buttons) && $i < count($fields['options'])) {
                            $output .= '<br>'."\n";
                        }
                        ++$i;
                    }
                }// endforeach;
                unset($i, $radio_buttons, $radio_key);
                $output .= '</fieldset>'."\n";
            }

            unset($field_name, $field_value);
            if (isset($output)) {
                return $output;
            }
        }// renderFormInputRadio


        /**
         * render form media upload.
         * 
         * @param integer $field_key key fields array.
         * @param array $fields fields array.
         * @param array $options_values options values.
         * @return string return rendered input.
         */
        private function renderFormMedia($field_key, array $fields, array $options_values = [])
        {
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
                if (in_array($fields['mode'], ['preview_all', 'preview_url', 'preview_img', 'no_preview_img', 'no_preview_url'])) {
                    $preview_mode = $fields['mode'];
                }
            }

            $output = '';
            if ('preview_all' === $preview_mode || 'preview_url' === $preview_mode || 'no_preview_img' === $preview_mode) {
                $output = '<input type="text" id="preview-media-url-'.$field_name.'" class="large-text" value="'.(is_array($field_values) && array_key_exists('url', $field_values) ? esc_url($field_values['url']) : '').'" readonly>'."\n";
            }
            $output .= '<input type="hidden" id="media-id-'.$field_name.'" name="'.$field_name.'[id]" value="'.(is_array($field_values) && array_key_exists('id', $field_values) ? $field_values['id'] : '').'">'."\n";
            $output .= '<input type="hidden" id="media-height-'.$field_name.'" name="'.$field_name.'[height]" value="'.(is_array($field_values) && array_key_exists('height', $field_values) ? $field_values['height'] : '').'">'."\n";
            $output .= '<input type="hidden" id="media-width-'.$field_name.'" name="'.$field_name.'[width]" value="'.(is_array($field_values) && array_key_exists('width', $field_values) ? $field_values['width'] : '').'">'."\n";
            $output .= '<input type="hidden" id="media-url-'.$field_name.'" name="'.$field_name.'[url]" value="'.(is_array($field_values) && array_key_exists('url', $field_values) ? $field_values['url'] : '').'">'."\n";
            $output .= '<input type="hidden" id="media-large-'.$field_name.'" name="'.$field_name.'[large]" value="'.(is_array($field_values) && array_key_exists('large', $field_values) ? $field_values['large'] : '').'">'."\n";
            $output .= '<input type="hidden" id="media-medium-'.$field_name.'" name="'.$field_name.'[medium]" value="'.(is_array($field_values) && array_key_exists('medium', $field_values) ? $field_values['medium'] : '').'">'."\n";
            $output .= '<input type="hidden" id="media-thumbnail-'.$field_name.'" name="'.$field_name.'[thumbnail]" value="'.(is_array($field_values) && array_key_exists('thumbnail', $field_values) ? $field_values['thumbnail'] : '').'">'."\n";
            $output .= '<br>'."\n";
            if ('preview_all' === $preview_mode || 'preview_img' === $preview_mode || 'no_preview_url' === $preview_mode) {
                $output .= '<div class="image-preview image-preview-'.$field_name.'">';
                if (is_array($field_values) && array_key_exists('thumbnail', $field_values) && !empty($field_values['thumbnail'])) {
                    $output .= '<img src="'.$field_values['thumbnail'].'" alt="">';
                }
                $output .= '</div>'."\n";
            }
            $output .= '<input type="button" class="button-secondary upload-media-button" value="'.__('Upload', 'okv-oauth').'" data-input_target="'.$field_name.'">'."\n";
            $output .= '<input type="button" class="button-secondary remove-media-button" value="'.__('Remove', 'okv-oauth').'" data-input_target="'.$field_name.'">'."\n";

            unset($field_name, $field_values, $preview_mode);
            return $output;
        }// renderFormMedia


        /**
         * render form select box.
         * 
         * @param integer $field_key key fields array.
         * @param array $fields fields array.
         * @param array $options_values options values.
         * @return string return rendered input.
         */
        private function renderFormSelectbox($field_key, array $fields, array $options_values = [])
        {
            $field_type = (array_key_exists('type', $fields) ? $fields['type'] : 'text');
            $field_name = (array_key_exists('id', $fields) ? $fields['id'] : $field_key);
            // check values
            if (is_array($options_values) && array_key_exists($field_name, $options_values)) {
                $field_value = $options_values[$field_name];
            } else {
                $field_value = (array_key_exists('default', $fields) ? $fields['default'] : '');
            }

            $output = '<select';
            $output .= ' id="'.$field_name.'"';
            $output .= ' name="'.$field_name.'"';
            if (array_key_exists('input_attributes', $fields) && is_array($fields['input_attributes'])) {
                foreach ($fields['input_attributes'] as $attribute_name => $attribute_value) {
                    if (!in_array($attribute_name, ['id', 'name'])) {
                        $output .= ' '.$attribute_name.'="'.$attribute_value.'"';
                    }
                }
                unset($attribute_name, $field_type, $attribute_value);
            }
            $output .= '>'."\n";
            if (array_key_exists('options', $fields)) {
                foreach ($fields['options'] as $option_key => $option_item1) {
                    if (is_array($option_item1)) {
                        $output .= '<optgroup label="'.$option_key.'">'."\n";
                        foreach ($option_item1 as $option_item2 => $option_item3) {
                            $output .= '<option value="'.$option_item2.'"';
                            if (!is_array($field_value) && $field_value === $option_item2) {
                                $output .= ' selected="selected"';
                            } elseif (is_array($field_value) && in_array($option_item2, $field_value)) {
                                $output .= ' selected="selected"';
                            }
                            $output .= '>';
                            $output .= esc_html($option_item3);
                            $output .= '</option>'."\n";
                        }
                        $output .= '</optgroup>'."\n";
                    } else {
                        $output .= '<option value="'.$option_key.'"';
                        if (!is_array($field_value) && $field_value === $option_key) {
                            $output .= ' selected="selected"';
                        } elseif (is_array($field_value) && in_array($option_key, $field_value)) {
                            $output .= ' selected="selected"';
                        }
                        $output .= '>';
                        $output .= esc_html($option_item1);
                        $output .= '</option>'."\n";
                    }
                }// endforeach;
                unset($option_item1, $option_item2, $option_item3, $option_key);
            }
            $output .= '</select>'."\n";

            unset($field_name, $field_value);
            return $output;
        }// renderFormSelectbox


        /**
         * render form textarea.
         * 
         * @param integer $field_key key fields array.
         * @param array $fields fields array.
         * @param array $options_values options values.
         * @return string return rendered input.
         */
        private function renderFormTextarea($field_key, array $fields, array $options_values = [])
        {
            $field_type = (array_key_exists('type', $fields) ? $fields['type'] : 'text');
            $field_name = (array_key_exists('id', $fields) ? $fields['id'] : $field_key);
            // check values
            if (is_array($options_values) && array_key_exists($field_name, $options_values)) {
                $field_value = $options_values[$field_name];
            } else {
                $field_value = (array_key_exists('default', $fields) ? $fields['default'] : '');
            }

            $output = '<textarea';
            $output .= ' id="'.$field_name.'"';
            if (!isset($fields['input_attributes']['class'])) {
                $output .= ' class="large-text"';
            }
            $output .= ' name="'.$field_name.'"';
            if (array_key_exists('input_attributes', $fields)) {
                foreach ($fields['input_attributes'] as $attribute_name => $attribute_value) {
                    if (!in_array($attribute_name, ['id', 'name', 'type', 'value'])) {
                        $output .= ' '.$attribute_name.'="'.$attribute_value.'"';
                    }
                }
                unset($attribute_name, $field_type, $attribute_value);
            }
            $output .= '>';
            $output .= esc_textarea($field_value);
            $output .= '</textarea>'."\n";

            unset($field_name, $field_value);
            return $output;
        }// renderFormTextarea


    }
}