<?php
function piforge_send_blog_request($target, $subject, $word_number, $imperative_word, $structure, $language)
{
    if (
        !current_user_can('edit_posts')
        || (!wp_doing_ajax())
    ) {
        return wp_die('You are not allowed to access this part of the site. <br/>Please contact your administrator.');
    }
    $api_key = get_option('piforge_api_key', false);
    if (!$api_key) {
        wp_die("Api key not found");
    }
    $data = array(
        'target' => $target,
        'subject' => $subject,
        'word_number' => intval($word_number),
        'imperative_word' => $imperative_word,
        'structure' => $structure,
        'language' => $language
    );

    $body = wp_json_encode($data);
    $response = wp_remote_post(
        'https://piforge1-1-546qq6j.ew.gateway.dev/api/v1/blog',
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
    return $response;
}