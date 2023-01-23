<?php 
/**
 * This template is based on "Bootstrap Basic" theme.
 * 
 * @package rundiz-oauth
 */
get_header(); 
?> 
<div id="main-column" class="col">
    <div class="post">
        <h1><?php _e('Rundiz OAuth', 'okv-oauth'); ?></h1>
        <?php 
        if (isset($form_error_msg)) {
        ?> 
        <div class="rd-oauth-alert rd-oauth-alert-error">
            <p><?php echo $form_error_msg; ?></p>
        </div>
        <?php
        }
        ?> 
        <?php 
        if (isset($form_success_msg)) {
        ?> 
        <div class="rd-oauth-alert rd-oauth-alert-success">
            <p><?php echo $form_success_msg; ?></p>
        </div>
        <?php
        }
        ?> 
    </div>
</div>
<?php 
get_footer();