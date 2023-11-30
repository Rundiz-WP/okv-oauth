<?php
/**
 * @package rundiz-oauth
 * @since 1.5.1
 */


namespace RundizOauth\App\Widgets;


if (!class_exists('\\RundizOauth\\App\\Widgets\\LoginLinksWidget')) {
    class LoginLinksWidget extends \WP_Widget
    {


        /**
         * @var string Widget title.
         */
        private $widgetTitle;


        /**
         * Class constructor.
         */
        public function __construct()
        {
            parent::__construct(
                    'rdoauth_loginlinks_widget', // base ID
                    __('Login links widget', 'okv-oauth'), 
                    [
                        'description' => __('Display links to login, logout, and other (depend on settings).', 'okv-oauth'),
                    ]
            );
        }// __construct


        /**
         * Admin widget form
         * 
         * @see WP_Widget::form()
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) 
        {
            if (isset($instance['rdoauth-loginlinks-widget-title'])) {
                $this->widgetTitle = $instance['rdoauth-loginlinks-widget-title'];
            }

            // output form
            $output = '<p>';
            $output .= '<label for="' . $this->get_field_id('rdoauth-loginlinks-widget-title') . '">' . __('Title', 'okv-oauth') . ':</label>';
            $output .= '<input id="' . $this->get_field_id('rdoauth-loginlinks-widget-title') . '" class="widefat" type="text" name="' . $this->get_field_name('rdoauth-loginlinks-widget-title') . '" value="' . esc_attr($this->widgetTitle) . '">';
            $output .= '</p>';

            $output .= '<p>';
            $output .= '<input id="' . $this->get_field_id('rdoauth-loginlinks-displaylink-admin') . '" class="input-checkbox" type="checkbox" name="' . $this->get_field_name('rdoauth-loginlinks-displaylink-admin') . '" value="1"' . 
                (isset($instance['rdoauth-loginlinks-displaylink-admin']) ? checked($instance['rdoauth-loginlinks-displaylink-admin'], '1', false) : '') . 
            '> ';
            $output .= '<label for="' . $this->get_field_id('rdoauth-loginlinks-displaylink-admin') . '">' . __('Display link to admin dashboard', 'okv-oauth') . ':</label>';
            $output .= '</p>';

            $output .= '<p>';
            $output .= '<input id="' . $this->get_field_id('rdoauth-loginlinks-displaylink-editprofile') . '" class="input-checkbox" type="checkbox" name="' . $this->get_field_name('rdoauth-loginlinks-displaylink-editprofile') . '" value="1"' . 
                (isset($instance['rdoauth-loginlinks-displaylink-editprofile']) ? checked($instance['rdoauth-loginlinks-displaylink-editprofile'], '1', false) : '') . 
            '> ';
            $output .= '<label for="' . $this->get_field_id('rdoauth-loginlinks-displaylink-editprofile') . '">' . __('Display link to edit profile', 'okv-oauth') . ':</label>';
            $output .= '</p>';

            $output .= '<p>';
            $output .= '<label for="' . $this->get_field_id('rdoauth-loginlinks-useelement') . '">' . __('Use HTML element', 'okv-oauth') . ':</label>';
            $output .= '<select id="' . $this->get_field_id('rdoauth-loginlinks-useelement') . '" name="' . $this->get_field_name('rdoauth-loginlinks-useelement') . '">';
            /* translators: %1$s: HTML element that will be use. */
            $output .= '<option value=""' . (isset($instance['rdoauth-loginlinks-useelement']) ? selected($instance['rdoauth-loginlinks-useelement'], '', false) : ' selected') . '>' . esc_html(sprintf(__('Use %1$s', 'okv-oauth'), 'ul & li')) . '</option>';
            /* translators: %1$s: HTML element that will be use. */
            $output .= '<option value="div_p"' . (isset($instance['rdoauth-loginlinks-useelement']) ? selected($instance['rdoauth-loginlinks-useelement'], 'div_p', false) : ' ') . '>' . esc_html(sprintf(__('Use %1$s', 'okv-oauth'), 'div & p')) . '</option>';
            /* translators: %1$s: HTML element that will be use. */
            $output .= '<option value="div_div"' . (isset($instance['rdoauth-loginlinks-useelement']) ? selected($instance['rdoauth-loginlinks-useelement'], 'div_div', false) : ' ') . '>' . esc_html(sprintf(__('Use %1$s', 'okv-oauth'), 'div & div')) . '</option>';
            $output .= '</select>';
            $output .= '</p>';

            $output .= '<p>';
            $output .= '<label for="' . $this->get_field_id('rdoauth-loginlinks-wrapper-classes') . '">' . __('List wrapper classes', 'okv-oauth') . ':</label>';
            $output .= '<input id="' . $this->get_field_id('rdoauth-loginlinks-wrapper-classes') . '" class="widefat" type="text" name="' . $this->get_field_name('rdoauth-loginlinks-wrapper-classes') . '" value="' . esc_attr($instance['rdoauth-loginlinks-wrapper-classes']) . '">';
            $output .= '</p>';

            $output .= '<p>';
            $output .= '<label for="' . $this->get_field_id('rdoauth-loginlinks-listitem-classes') . '">' . __('List item classes', 'okv-oauth') . ':</label>';
            $output .= '<input id="' . $this->get_field_id('rdoauth-loginlinks-listitem-classes') . '" class="widefat" type="text" name="' . $this->get_field_name('rdoauth-loginlinks-listitem-classes') . '" value="' . esc_attr($instance['rdoauth-loginlinks-listitem-classes']) . '">';
            $output .= '</p>';

            echo $output;

            unset($output);
        }// form


        /**
         * Generate open, close for line and wrapper.
         * 
         * @param string $useElement The use element option.
         * @param string $wrapperClasses The wrapper classes.
         * @param string $lineClasses The list item line classes.
         * @return array Return indexed array of these values by order:<br> 
         *      [open wrapper, close wrapper, open line, close line]
         */
        private function generateOpenCloseLineAndWrapper($useElement, $wrapperClasses = '', $lineClasses = '')
        {
            if (!empty($lineClasses)) {
                $lineClasses = ' ' . $lineClasses;
            }

            if ('div_p' === $useElement) {
                $openWrapper = '<div class="' . esc_attr($wrapperClasses) . '">' . PHP_EOL;
                $closeWrapper = '</div>' . PHP_EOL;
                $openLine = '<p class="rd-oauth-loginlinks-widget-list-item' . $lineClasses . '">';
                $closeLine = '</p>';
            } elseif ('div_div' === $useElement) {
                $openWrapper = '<div class="' . esc_attr($wrapperClasses) . '">' . PHP_EOL;
                $closeWrapper = '</div>' . PHP_EOL;
                $openLine = '<div class="rd-oauth-loginlinks-widget-list-item' . $lineClasses . '">';
                $closeLine = '</div>';
            } else {
                $openWrapper = '<ul class="' . esc_attr($wrapperClasses) . '">' . PHP_EOL;
                $closeWrapper = '</ul>' . PHP_EOL;
                $openLine = '<li class="rd-oauth-loginlinks-widget-list-item' . $lineClasses . '">';
                $closeLine = '</li>';
            }

            return [$openWrapper, $closeWrapper, $openLine, $closeLine];
        }// generateOpenCloseLineAndWrapper


        /**
         * Sanitize widget form values as they are saved.
         * 
         * @see WP_Widget::update()
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance)
        {
            $instance = $old_instance;

            if (isset($new_instance['rdoauth-loginlinks-widget-title'])) {
                $instance['rdoauth-loginlinks-widget-title'] = sanitize_text_field($new_instance['rdoauth-loginlinks-widget-title']);
            }
            $instance['rdoauth-loginlinks-displaylink-admin'] = (isset($new_instance['rdoauth-loginlinks-displaylink-admin']) && '1' === $new_instance['rdoauth-loginlinks-displaylink-admin'] ? '1' : '');
            $instance['rdoauth-loginlinks-displaylink-editprofile'] = (isset($new_instance['rdoauth-loginlinks-displaylink-editprofile']) && '1' === $new_instance['rdoauth-loginlinks-displaylink-editprofile'] ? '1' : '');
            $instance['rdoauth-loginlinks-useelement'] = (isset($new_instance['rdoauth-loginlinks-useelement']) ? sanitize_text_field($new_instance['rdoauth-loginlinks-useelement']) : '');
            if (isset($new_instance['rdoauth-loginlinks-wrapper-classes'])) {
                $instance['rdoauth-loginlinks-wrapper-classes'] = sanitize_text_field($new_instance['rdoauth-loginlinks-wrapper-classes']);
            }
            if (isset($new_instance['rdoauth-loginlinks-listitem-classes'])) {
                $instance['rdoauth-loginlinks-listitem-classes'] = sanitize_text_field($new_instance['rdoauth-loginlinks-listitem-classes']);
            }

            return $instance;
        }// update


        /**
         * Front-end display of widget
         * 
         * @see WP_Widget::widget()
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) 
        {
            $widget_title = $this->widgetTitle;
            if (isset($instance['rdoauth-loginlinks-widget-title'])) {
                $widget_title = $instance['rdoauth-loginlinks-widget-title'];
            }

            // set output front-end widget ---------------------------------
            $output = $args['before_widget'] . PHP_EOL;

            if (isset($instance['rdoauth-loginlinks-widget-title']) && !empty($instance['rdoauth-loginlinks-widget-title'])) {
                $output .= $args['before_title'] . apply_filters('widget_title', $instance['rdoauth-loginlinks-widget-title']) . $args['after_title'] . PHP_EOL;
            }

            $currentUrl = ( is_ssl() ? 'https://' : 'http://' ) . 
                (isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : '') . 
                (isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '');
            $isUserLoggedIn = is_user_logged_in();
            $wrapperClasses = ($isUserLoggedIn ? 'logged-in' : 'logged-out');
            if (!empty($instance['rdoauth-loginlinks-wrapper-classes'])) {
                $wrapperClasses .= ' ' . $instance['rdoauth-loginlinks-wrapper-classes'];
            }
            $lineClasses = (isset($instance['rdoauth-loginlinks-listitem-classes']) ? $instance['rdoauth-loginlinks-listitem-classes'] : '');
            $useElement = (isset($instance['rdoauth-loginlinks-useelement']) ? $instance['rdoauth-loginlinks-useelement'] : '');

            list($openWrapper, $closeWrapper, $openLine, $closeLine) = $this->generateOpenCloseLineAndWrapper($useElement, $wrapperClasses, $lineClasses);
            unset($lineClasses, $useElement, $wrapperClasses);

            // list site-admin/register/login/edit profile/logout links
            $output .= $openWrapper;
            // apply filters after open the wrapper.
            // @since 1.5.2
            $output .= apply_filters('rdoauth_loginlinkswidgetblock_afteropenwrapper', '', $openLine, $closeLine);
            if ($isUserLoggedIn) {
                // if logged in.
                // apply filters for logged in users, before display links.
                // @since 1.5.2
                $output .= apply_filters('rdoauth_loginlinkswidgetblock_loggedin_beforelinks', '', $openLine, $closeLine);
                if (isset($instance['rdoauth-loginlinks-displaylink-admin']) && '1' === $instance['rdoauth-loginlinks-displaylink-admin']) {
                    // if setting to allowed link to admin.
                    $output .= $openLine . '<a href="' . admin_url() . '">' . __('Site Admin') . '</a>' . $closeLine . PHP_EOL;
                }
                if (isset($instance['rdoauth-loginlinks-displaylink-editprofile']) && '1' === $instance['rdoauth-loginlinks-displaylink-editprofile']) {
                    // if setting to allowed link to edit profile.
                    $output .= $openLine . '<a href="' . get_edit_user_link() . '">' . __('Edit Profile') . '</a>' . $closeLine . PHP_EOL;
                }
                // apply filters for logged in users, after  display links.
                // @since 1.5.2
                $output .= apply_filters('rdoauth_loginlinkswidgetblock_loggedin_afterlinks', '', $openLine, $closeLine);
            } else {
                // if NOT logged in.
                if (get_option('users_can_register')) {
                    // if setting is allowed user register.
                    $output .= $openLine . '<a href="' . wp_registration_url() . '">' . __('Register', 'okv-oauth') . '</a>' . $closeLine . PHP_EOL;
                }
            }// endif;
            $output .= $openLine . wp_loginout($currentUrl, false) . $closeLine . PHP_EOL;
            // apply filters after login/logout.
            // @since 1.5.2
            $output .= apply_filters('rdoauth_loginlinkswidgetblock_afterloginout', '', $openLine, $closeLine);
            unset($currentUrl, $isUserLoggedIn);
            $output .= $closeWrapper;
            unset($closeLine, $closeWrapper, $openLine, $openWrapper);

            $output .= $args['after_widget'] . PHP_EOL;

            echo $output;

            // clear unused variables
            unset($output);
        }// widget


    }
}