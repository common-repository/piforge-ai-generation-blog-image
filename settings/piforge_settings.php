<?php
function piforge_settings_submenu_content()
{

    if (!is_admin() || !current_user_can('manage_options') || wp_doing_ajax()) {
        return wp_die('You are not allowed to access this part of the site. <br/>Please contact your administrator.');
    }
    $piforge_gd_loaded = get_option("piforge_gd_loaded", false);
    $api_key = get_option('piforge_api_key', false);
    ?>
    <div class="wrap">
        <h2>PiForge | Settings</h2>
        <div id="piforge_settings_header">
            <a href="https://piforge.ai" target="_BLANK">
                <img src="<?php echo esc_html(plugin_dir_url(dirname(__FILE__))) . 'assets/logo2.png'; ?>">
            </a>
            <!-- <span style="width: 50px;"></span> -->
            <p>Here you can add you API Key, for get it go to
                <a href="https://piforge.ai/user/account" target="_blank">
                    your account
                </a>
            </p>
        </div>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="piforge_api_key">Clé d'API :</label></th>
                    <form id="custom-upload-form" method="post">
                        <?php wp_nonce_field('custom-upload-form', 'settings'); ?>
                        <td>
                            <input type="<?php echo ($api_key ? "password" : "text"); ?>" id="piforge_api_key"
                                name="piforge_api_key" value="<?php echo esc_attr($api_key); ?>"
                                placeholder="Enter your API key">
                        </td>
                        <td><input type="submit" class="button-primary" value="Save"></td>
                    </form>
                </tr>
                <tr>
                    <th scope="row"><label for="new_gen_img">New generation image output :
                            <br /><small> Output format: Webp (next) or Png (old) </small></label></th>
                    <form id="activate_webp_form" method="post">
                        <?php wp_nonce_field('activate_webp_form', 'settings'); ?>
                        <input type="hidden" id="form_id" name="form_id" value="activate_webp_form">
                        <td>
                            <input type="checkbox" id="new_gen_img" name="new_gen_img" <?php echo ($piforge_gd_loaded === "on" ? "checked" : ""); ?>>
                        </td>
                        <td><input type="submit" class="button-primary" value="Save"></td>
                    </form>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
}
