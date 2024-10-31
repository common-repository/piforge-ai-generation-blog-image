<?php
function piforge_toogle_webp()
{

    if (
        isset($_POST['form_id'])
        && sanitize_text_field(wp_unslash($_POST['form_id'])) === 'activate_webp_form' && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['settings'])), 'activate_webp_form')
    ) {
        if (isset($_POST['new_gen_img'])) {
            if (
                extension_loaded('gd') && function_exists("imagecreatefrompng") && function_exists("imagewebp") &&
                function_exists("imagedestroy")
            ) {
                update_option('piforge_gd_loaded', "on");
                wp_admin_notice("Enabled", array("type" => "success", "dismissible" => true));
            } else {
                update_option('piforge_gd_loaded', false);
                wp_admin_notice("Missing module GD image in your php configuration, in php.ini. \n<a href='https://piforge.ai/docs/wordpress/gd' target='_BLANK'>See a tutorial for enabling this PHP feature on your server.</a>", array("type" => "success", "dismissible" => true));
            }
        } else {
            if (
                extension_loaded('gd') && function_exists("imagecreatefrompng") && function_exists("imagewebp") &&
                function_exists("imagedestroy")
            ) {
                update_option('piforge_gd_loaded', false);
                wp_admin_notice("Disabled", array("type" => "success", "dismissible" => true));
            } else {
                update_option('piforge_gd_loaded', false);
                wp_admin_notice("Missing module GD image in your php configuration, in php.ini. \n<a href='https://piforge.ai/docs/wordpress/gd' target='_BLANK'>See a tutorial for enabling this PHP feature on your server.</a>", array("type" => "success", "dismissible" => true));
            }
        }
    }
}