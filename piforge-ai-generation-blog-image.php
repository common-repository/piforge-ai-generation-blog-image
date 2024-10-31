<?php
/**
 * Plugin Name: Piforge AI - Generation | Blog | Image
 * Plugin URI:  https://piforge.ai/plugins/wordpress/piforge_ai
 * Description: The best Optimise SEO image generator directly integrated in wordpress
 * Author: PiForge
 * Version: 1.1.0
 * Requires at least: 6.0
 * Requires PHP:      7.0
 * Author URI:  https://piForge.ai/plugins/
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * License: GPLv2
 * Text Domain: piforge_ai
 */

/*
Piforge AI - Generation | Blog | Image is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Piforge AI - Generation | Blog | Image is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Piforge AI - Generation | Blog | Image. If not, see http://www.gnu.org/licenses/gpl-2.0.txt.
*/

if (!defined('ABSPATH'))
    exit; // Quitte si accès direct

require_once (__DIR__ . "/settings/piforge_settings.php");
require_once (__DIR__ . "/media/piforge_media.php");
require_once (__DIR__ . "/blog/piforge_blog.php");
require_once (__DIR__ . "/widget/piforge_widget.php");
require_once (__DIR__ . "/functions/piforge_save_api_key.php");
require_once (__DIR__ . "/functions/piforge_send_blog_request.php");
require_once (__DIR__ . "/functions/piforge_send_image_request.php");
require_once (__DIR__ . "/functions/piforge_toogle_webp.php");

//script
// register_activation_hook(__FILE__, 'PIFORGE_activateScript');
// register_deactivation_hook(__FILE__, 'PIFORGE_deactivateScript');



register_uninstall_hook(__FILE__, 'PIFORGE_uninstaller_piforge');

//admin_init functions
add_action(
    'admin_init',
    'piforge_init_plugin'
);
//admin_menu
add_action(
    'admin_menu',
    'piforge_custom_media_submenu'
);
//widget
add_action(
    'wp_dashboard_setup',
    'piforge_widget'
);
add_action(
    'admin_enqueue_scripts',
    'piforge_enqueue_scripts'
);
add_action(
    'admin_enqueue_scripts',
    'piforge_enqueue_styles'
);
// ajax handler
add_action(
    'wp_ajax_piforge_image_request',
    'piforge_image_handler'
);
add_action(
    'wp_ajax_piforge_blog_request',
    'piforge_blog_handler'
);
add_action(
    'wp_ajax_piforge_save_draft_request',
    'piforge_save_draft_callback'
);


function PIFORGE_uninstaller_piforge()
{
    // Delete the 'piforge_api_key' option from the WordPress database
    delete_option('piforge_api_key');

    // Delete the 'gd_loaded' option from the WordPress database
    delete_option('piforge_gd_loaded');

    // Remove the 'piforge_image_request' action from the 'wp_ajax_piforge_image_request' hook
    remove_action(
        'wp_ajax_piforge_image_request',
        'piforge_image_handler'
    );

    // Remove the 'piforge_blog_request' action from the 'wp_ajax_piforge_blog_request' hook
    remove_action(
        'wp_ajax_piforge_blog_request',
        'piforge_blog_handler'
    );

    // Remove the 'piforge_save_draft_request' action from the 'wp_ajax_piforge_save_draft_request' hook
    remove_action(
        'wp_ajax_piforge_save_draft_request',
        'piforge_save_draft_callback'
    );
}
//Script for ajax request
//Script for ajax request
function piforge_enqueue_scripts()
{
    // Enqueue the 'piforge_image_request' script with jQuery as a dependency
    wp_enqueue_script(
        'piforge_image_request',
        plugin_dir_url(__FILE__) . 'js/piforge_image_request.js',
        array('jquery'),
        filemtime(plugin_dir_path(__FILE__) . 'js/piforge_image_request.js'),
        true
    );

    // Localize the 'piforge_image_request' script with the 'piforge_image_script' object
    wp_localize_script(
        'piforge_image_request',
        'piforge_image_script',
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        )
    );

    // Enqueue the 'piforge_blog_request' script with jQuery as a dependency
    wp_enqueue_script(
        'piforge_blog_request',
        plugin_dir_url(__FILE__) . 'js/piforge_blog_request.js',
        array('jquery'),
        filemtime(plugin_dir_path(__FILE__) . 'js/piforge_blog_request.js'),
        true
    );

    // Localize the 'piforge_blog_request' script with the 'piforge_blog_script' object
    wp_localize_script(
        'piforge_blog_request',
        'piforge_blog_script',
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        )
    );

    // Enqueue the 'piforge_save_draft_request' script with jQuery as a dependency
    wp_enqueue_script(
        'piforge_save_draft_request',
        plugin_dir_url(__FILE__) . 'js/piforge_send_to_draft.js',
        array('jquery'),
        filemtime(plugin_dir_path(__FILE__) . 'js/piforge_send_to_draft.js'),
        true
    );

    // Localize the 'piforge_save_draft_request' script with the 'piforge_save_draft_callback' object
    wp_localize_script(
        'piforge_save_draft_request',
        'piforge_save_draft_callback',
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('piforge_save_draft_nonce')
        )
    );
}
function piforge_enqueue_styles()
{
    // Enqueue the 'piforge_image_request' script with jQuery as a dependency
    wp_enqueue_style(
        'piforge_media_style',
        plugin_dir_url(__FILE__) . 'media/piforge_media_style.css',
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'media/piforge_media_style.css'),
        "screen"
    );

    // Enqueue the 'piforge_blog_request' script with jQuery as a dependency
    wp_enqueue_style(
        'piforge_blog_style',
        plugin_dir_url(__FILE__) . 'blog/piforge_blog_style.css',
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'blog/piforge_blog_style.css'),
        "screen"
    );
    wp_enqueue_style(
        'piforge_settings_style',
        plugin_dir_url(__FILE__) . 'settings/piforge_settings_style.css',
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'settings/piforge_settings_style.css'),
        "screen"
    );






}
// backend handler was called by jquery form interceptors
// backend handler was called by jquery form interceptors
function piforge_image_handler()
{
    // Check if the user has the 'edit_posts' capability, if the request is an AJAX request,
    // if the 'image' parameter is set, and if the nonce is valid
    if (
        !current_user_can('edit_posts')
        || (!wp_doing_ajax())
        || !isset($_POST['image'])
        || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['image'])), 'piforge_request_image_form')
    ) {
        // Send a JSON error response with a 403 status code
        wp_send_json_error(array("message" => "Forbidden with your right, contact your administrator."), 403, 0);
        wp_die();
    }

    // Retrieve the form data and sanitize it
    $prompt_from_form = sanitize_text_field(wp_unslash($_POST['prompt']));
    $style_gen_from_form = sanitize_text_field(wp_unslash($_POST['style_gen']));
    $count_from_form = isset($_POST['count']) ? intval(sanitize_text_field(wp_unslash($_POST['count']))) : 1;
    $ratio_from_form = sanitize_text_field(wp_unslash($_POST['ratio']));
    $product_from_form = sanitize_text_field(wp_unslash($_POST['product']));

    // Retrieve the 'piforge_api_key' option from the WordPress database
    $api_key = get_option('piforge_api_key', false);

    // Check if the API key is set
    if (!$api_key) {
        // Send a JSON error response with a 401 status code
        wp_send_json_error(array("message" => "API key Not found \n Go to settings > Piforge settings for add your API key."), 401, 0);
        wp_die();
    }

    // Send the image request to the Piforge API
    $piforge_response = piforge_send_image_request(
        $prompt_from_form,
        $style_gen_from_form,
        $count_from_form,
        $ratio_from_form,
        $product_from_form
    );

    // Retrieve the response status code and body
    $status_code = wp_remote_retrieve_response_code($piforge_response);
    $response_body = wp_remote_retrieve_body($piforge_response);

    // Check if the response status code is 200
    if ($status_code == 200) {
        // Send a JSON success response with the response body decoded as an array
        wp_send_json_success(json_decode($response_body, true), $status_code, 0);
    } else {
        // Send a JSON error response with the response body decoded as an array
        wp_send_json_error(json_decode($response_body, true), $status_code, 0);
    }

    wp_die();
}
// backend handler was called by jquery form interceptors
function piforge_blog_handler()
{
    // Check if the user has the 'edit_posts' capability, if the request is an AJAX request,
    // if the 'blog' parameter is set, and if the nonce is valid
    if (
        !current_user_can('edit_posts')
        || !wp_doing_ajax()
        || !isset($_POST['blog'])
        || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['blog'])), 'piforge_request_blog_form')
    ) {
        // Send a JSON error response with a 403 status code
        wp_send_json_error(array("message" => "Forbidden with your right, contact your administrator."), 403, 0);
        wp_die();
    }

    // Retrieve the form data and sanitize it
    $target_from_form = sanitize_text_field(wp_unslash($_POST['target']));
    $subject_gen_from_form = sanitize_text_field(wp_unslash($_POST['subject_gen']));
    $word_number_from_form = isset($_POST['word_number']) ? intval(sanitize_text_field(wp_unslash($_POST['word_number']))) : 400;
    $imperative_word_from_form = sanitize_text_field(wp_unslash($_POST['imperative_word']));
    $structure_from_form = sanitize_text_field(wp_unslash($_POST['structure']));
    $language_from_form = sanitize_text_field(wp_unslash($_POST['language']));

    // Retrieve the 'piforge_api_key' option from the WordPress database
    $api_key = get_option('piforge_api_key', false);

    // Check if the API key is set
    if (!$api_key) {
        // Send a JSON error response with a 401 status code
        wp_send_json_error(array("message" => "API key Not found \n Go to settings > Piforge settings for add your API key."), 401, 0);
        wp_die();
    }

    // Send the blog request to the Piforge API
    $piforge_response = piforge_send_blog_request($target_from_form, $subject_gen_from_form, $word_number_from_form, $imperative_word_from_form, $structure_from_form, $language_from_form);

    // Retrieve the response status code and body
    $status_code = wp_remote_retrieve_response_code($piforge_response);
    $response_body = wp_remote_retrieve_body($piforge_response);
    // Decoding JSON data
    $data = json_decode($response_body, true);

    // Check whether the response contains HTML
    if (isset($data['success']) && $data['success'] === true && isset($data['data']['choices'][0]['message']['content'])) {
        // HTML content retrieval
        $html_content = $data['data']['choices'][0]['message']['content'];
        // Clean up HTML content
        $data['data']['choices'][0]['message']['content'] = wp_kses_post($html_content);
    }
    // Check if the response status code is 200
    if ($status_code == 200) {
        // Send a JSON success response with the response body decoded as an array
        wp_send_json_success($data, $status_code, 0);
    } else {
        // Send a JSON error response with the response body decoded as an array
        wp_send_json_error($data, $status_code, 0);
    }

    wp_die();
}

function piforge_save_draft_callback()
{
    // Check if the user has the 'edit_posts' capability and if the nonce is valid
    if (
        !current_user_can('edit_posts')
        || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])))
    ) {
        // Send a JSON error response
        wp_send_json_error('You are not authorized to perform this action.');
        wp_die();
    }

    // Retrieve the editor content and sanitize it
    $content = isset($_POST['content']) ? sanitize_text_field(wp_unslash($_POST['content'])) : '';
    //update api to return markdown
    $title = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '';

    // Create a new draft post with the specified content and title
    $post_id = wp_insert_post(
        array(
            'post_content' => $content,
            'post_title' => $title,
            'post_status' => 'draft',
            'post_type' => 'post'
        )
    );

    // Check if the post was created successfully
    if ($post_id) {
        // Send a JSON success response with the post ID
        wp_send_json_success('Draft saved successfully.', $post_id);
    } else {
        // Send a JSON error response
        wp_send_json_error('Error saving draft.');
    }

    // Make sure to stop the script after sending the response
    wp_die();
}
//create menu
function piforge_custom_media_submenu()
{
    // Add a submenu page under the 'upload.php' menu with the specified title,
    // menu title, capability, menu slug, and callback function
    add_submenu_page(
        'upload.php',
        'PiForge',
        'Image Generation',
        'upload_files',
        'piforge_media',
        'piforge_media_submenu_content'
    );

    // Add a submenu page under the 'options-general.php' menu with the specified title,
    // menu title, capability, menu slug, and callback function
    add_submenu_page(
        'options-general.php',
        'PiForge',
        'Piforge Settings',
        'upload_files',
        'piforge_settings',
        'piforge_settings_submenu_content'
    );

    // Add a submenu page under the 'edit.php' menu with the specified title,
    // menu title, capability, menu slug, and callback function
    add_submenu_page(
        'edit.php',
        'PiForge',
        'Auto Blog',
        'upload_files',
        'piforge_blog',
        'piforge_blog_submenu_content'
    );
}
// create widget
function piforge_widget()
{
    // Add a dashboard widget with the specified ID, title, and callback function
    wp_add_dashboard_widget(
        'piforge_widget',
        'Piforge | Information',
        'piforge_widget_content'
    );
}


function piforge_init_plugin()
{
    // Check if the user is an administrator and has the 'manage_options' capability
    if (
        is_admin()
        && current_user_can('manage_options')
    ) {
        // Call the 'piforge_save_api_key' function in the 'function' folder
        piforge_save_api_key();
        // Call the 'piforge_toogle_webp' function in the 'function' folder
        piforge_toogle_webp();
    }
}

