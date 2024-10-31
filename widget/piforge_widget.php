<?php
function piforge_widget_content()
{
    if (wp_doing_ajax()) {
        return wp_die('You are not allowed to access this part of the site. <br/>Please contact your administrator.');
    }
    $api_key = get_option("piforge_api_key", false);
    if (!$api_key) {
        wp_die('Go to <a href="' . esc_html(admin_url()) . 'options-general.php?page=piforge_settings">Piforge settings</a> for add your API key.');
    }

    $response = wp_remote_get(
        'https://piforge1-1-546qq6j.ew.gateway.dev/api/v1/user',
        array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'bearer ' . $api_key
            )
        )
    );
    // Vérifiez si la requête a réussi
    $status_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    $decoded_response = json_decode($response_body, true);
    $credits = false;
    if ($status_code !== 200) {
        if (isset($decoded_response['message'])) {
            wp_die(esc_html($response_body["message"]));
        } else {
            wp_die('Bad request');
        }
    }
    if (isset($decoded_response['credits'])) {

        $credits = $decoded_response['credits'];
    }
    ?>
    <div class="wrap">
        <div style="display:flex;justify-content:start;width:100%;margin-left:9%;">
            <a href="https://piforge.ai" target="_BLANK">
                <img style="width:80px; background-color: black; border-radius: 10px; padding:10px; "
                    src="<?php echo esc_html(plugin_dir_url(dirname(__FILE__))) . 'assets/logo2.png'; ?>">
            </a>
        </div>
        <h3 style="text-align: right;">Api Key is
            <?php echo ($api_key ? "saved ! Good Job !" : "not Saved go to <a href='./options-general.php?page=piforge_settings'>Settigns to save it now</a>.") ?>
        </h3>
        <p style="text-align: right;">Current credits :
            <?php echo ($credits ? '<b>' . esc_html($credits) . '</b>' : "await response") ?>
        </p>
        <div style="width: 100%; display:flex; justify-content:space-evenly; margin-bottom: 20px;">
            <a class="button-primary"
                href="<?php echo esc_html(admin_url()) . 'options-general.php?page=piforge_settings'; ?>">Piforge
                settings</a>
            <a class="button-primary" href="<?php echo esc_html(admin_url()) . 'upload.php?page=piforge_media'; ?>">Generate
                images</a>
            <a class="button-primary" href="<?php echo esc_html(admin_url()) . 'edit.php?page=piforge_blog'; ?>">Generate
                content</a>

        </div>

    </div>
    <?php
}