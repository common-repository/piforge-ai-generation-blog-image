<?php
function piforge_send_image_request($prompt = "A cat in space", $style, $image_count, $ratio, $product)
{
    if (
        !current_user_can('edit_posts')
        || !wp_doing_ajax()
    ) {
        return wp_die('You are not allowed to access this part of the site. <br/>Please contact your administrator.');
    }
    $api_key = get_option('piforge_api_key', false);
    if (!$api_key) {
        return false;
    }
    [$width, $height] = explode(":", $ratio);
    $prompt_length = strlen($prompt);
    if ($prompt_length == 0) {
        $prompt = "A cat in space";
    }

    $data = array(
        'product' => $product,
        'prompt' => $prompt,
        'style' => $style,
        'denoise' => 1,
        "width" => intval($width),
        "height" => intval($height),
        'cfg' => 5,
        'image_count' => intval($image_count),
    );

    $body = wp_json_encode($data);
    $response = wp_remote_post(
        'https://piforge1-1-546qq6j.ew.gateway.dev/api/v1/images',
        array(
            'timeout' => 3000,
            'blocking' => true,
            'body' => $body,
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer " . $api_key
            )
        )
    );
    // Vérifiez si la requête a réussi
    $status_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    $decoded_response = json_decode($response_body, true);


    if (!is_wp_error($response) && $status_code === 200) {
        // Vérifier si la clé "data" existe dans la réponse
        if (isset($decoded_response["img_urls"])) {
            // Récupérer les données de l'élément "data"
            $images_urls = $decoded_response["img_urls"];
            if (is_array($images_urls) && !empty($images_urls)) {
                foreach ($images_urls as $image_url) {
                    if (extension_loaded('gd') && function_exists("imagecreatefrompng") && function_exists("imagewebp") && function_exists("imagedestroy") && get_option("piforge_gd_loaded") === "on") {
                        piforge_save_image_as_webp($image_url, $prompt);
                    } else {
                        piforge_save_image_as_png($image_url, $prompt);
                    }

                }
            }
        }
    }
    return $response;
}
function piforge_save_image_as_png($image_url, $prompt)
{
    $response = wp_remote_get($image_url);
    // Check if the request was successful
    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        wp_die("An error has occurred in the image request: the URL of the piforge image cannot be received.");
    }
    $image_data = wp_remote_retrieve_body($response);
    if ($image_data === false) {
        wp_die("Image was not saved. An error was occured.");
    }
    $png = imagecreatefromstring($image_data);
    if ($png === false) {
        wp_die("Image was not saved. An error was occured.");
    }
    $webp_upload_path = wp_upload_dir()["basedir"];
    $filename = wp_unique_filename($webp_upload_path, 'Piforge_image.png', null);
    $webp_path = $webp_upload_path . $filename;
    $filetype = wp_check_filetype($webp_path, null);
    $file = array(
        'name' => $filename,
        'type' => $filetype['type'],
        'tmp_name' => $webp_path,
        'error' => 0,
        'size' => filesize($webp_path)
    );
    $attachment_id = media_handle_sideload($file, 0, $prompt, array('test_form' => false));

    // Vérifier les erreurs lors du téléchargement du fichier
    if (is_wp_error($attachment_id)) {
        // Gérer l'erreur de téléchargement
        return $attachment_id;
    }
    // Retourner l'ID de l'attachement
    return $attachment_id;
}
function piforge_save_image_as_webp($image_url, $prompt)
{
    $response = wp_remote_get($image_url);

    // Check if the request was successful
    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        wp_die("An error has occurred in the image request: the URL of the piforge image cannot be received.");
    }
    $image_data = wp_remote_retrieve_body($response);
    if ($image_data === false) {
        return piforge_save_image_as_png($image_url, $prompt);
    }
    $png = imagecreatefromstring($image_data);
    if ($png === false) {
        return piforge_save_image_as_png($image_url, $prompt);
    }
    $webp_upload_path = wp_upload_dir()["basedir"];
    $filename = wp_unique_filename($webp_upload_path, 'Piforge_image.webp', null);
    $webp_path = $webp_upload_path . $filename;
    if (!imagewebp($png, $webp_path)) {
        return piforge_save_image_as_png($image_url, $prompt);
    }
    imagedestroy($png);
    $filetype = wp_check_filetype($webp_path, null);
    $file = array(
        'name' => $filename,
        'type' => $filetype['type'],
        'tmp_name' => $webp_path,
        'error' => 0,
        'size' => filesize($webp_path)
    );
    media_handle_sideload($file, 0, $prompt, array('test_form' => false));

}