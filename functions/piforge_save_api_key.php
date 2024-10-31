<?php
function piforge_save_api_key()
{
    if (
        !is_admin()
        || !current_user_can('manage_options')
    ) {
        return wp_die('You are not allowed to access this part of the site. <br/>Please contact your administrator.');
    }
    if (
        isset($_POST['piforge_api_key'])
        && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['settings'])), 'custom-upload-form')
    ) {
        $api_key = sanitize_text_field(wp_unslash($_POST['piforge_api_key']));
        update_option('piforge_api_key', $api_key);
        wp_admin_notice("Apikey saved", array("type" => "success", "dismissible" => true));
    }
}



