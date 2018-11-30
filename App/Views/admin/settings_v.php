<div class="wrap">
    <h1><?php echo __('Rundiz OAuth settings', 'okv-oauth'); ?></h1>

    <?php if (isset($form_result_class) && isset($form_result_msg)) { ?> 
    <div class="<?php echo $form_result_class; ?> notice is-dismissible">
        <p>
            <strong><?php echo $form_result_msg; ?></strong>
        </p>
        <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e('Dismiss this notice.'); ?></span></button>
    </div>
    <?php } ?> 

    <form method="post">
        <?php wp_nonce_field(); ?> 
        <?php if (isset($settings_page)) {echo $settings_page;} ?> 
        <?php submit_button(); ?> 
    </form>
</div><!--.wrap-->