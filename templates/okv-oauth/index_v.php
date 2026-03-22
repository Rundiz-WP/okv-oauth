<?php 
/**
 * This template is based on "Bootstrap Basic" theme.
 * 
 * @package okv-oauth
 * 
 * phpcs:disable Generic.WhiteSpace.ScopeIndent.Incorrect, Generic.WhiteSpace.ScopeIndent.IncorrectExact
 */


if (!defined('ABSPATH')) {
    exit();
}

get_header(); 
?> 
<div id="main-column" class="col">
    <div class="post">
        <h1><?php esc_html_e('Rundiz OAuth', 'okv-oauth'); ?></h1>
        <?php 
        if (isset($form_error_msg)) {
        ?> 
        <div class="rd-oauth-alert rd-oauth-alert-error">
            <p><?php echo wp_kses_post($form_error_msg); ?></p>
        </div>
        <?php
        }
        ?> 
        <?php 
        if (isset($form_success_msg)) {
        ?> 
        <div class="rd-oauth-alert rd-oauth-alert-success">
            <p><?php echo wp_kses_post($form_success_msg); ?></p>
        </div>
        <?php
        }
        ?> 
    </div>
</div>
<?php 
get_footer();
