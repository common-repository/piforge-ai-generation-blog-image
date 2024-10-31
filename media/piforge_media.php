<?php

function piforge_media_submenu_content()
{
    if (!current_user_can('edit_posts') || (wp_doing_ajax())) {
        return wp_die('You are not allowed to access this part of the site');
    }
    ?>

    <div class="wrap" id="wrap">
        <h2>PiForge | Image Generation</h2>
        <div style="display:flex; align-items:center;justify-content:space-between;">
            <a href="https://piforge.ai" target="_BLANK">
                <img style="width:40px; background-color: black; border-radius: 10px; padding:5px; "
                    src="<?php echo esc_html(plugin_dir_url(dirname(__FILE__))) . 'assets/logo_square_1024_1024.png'; ?>">
            </a>
            <h3>Pro IA generation on demand</h3>
        </div>

        <div id="custom_notice" class="notice is-dismissible"></div>
        <div id="media_container">

            <div id="cols_ontainer">
                <div id="left_col">
                    <form id="piforge_request_image_form">
                        <?php wp_nonce_field('piforge_request_image_form', 'image'); ?>
                        <label for="prompt">Describe your image with text</label>
                        <textarea id="prompt" name="prompt" placeholder="Describe here" rows="10" cols="30"></textarea>
                        <label for="product">Callable product</label>
                        <select name="product" id="product">
                            <option value="core_ai">Core general</option>
                            <option value="flat_style_core_ai">Flat style</option>
                        </select>
                        <label for="style_gen">Choose a style</label>
                        <select name="style_gen" id="style_gen">
                            <option value="">Automatic</option>
                        </select>
                        <label for="count">Image count</label>
                        <select name="count" id="count">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                        <label for="ratio">Ratio of output image</label>
                        <select name="ratio" id="ratio">
                            <option value="1200:800">1200:800 (highlight image 3:2) </option>
                            <option value="1024:1024">1024:1024 (1:1)</option>
                            <option value="800:1200">800:1200 (2:3)</option>
                            <option value="1400:600">1400:600 (7:3)</option>
                            <option value="600:1400">600:1400 (3:7)</option>
                        </select>
                        <label> </label>
                        <div style="display:flex;">
                            <input type="submit" class="button-primary" id="form_button_1" value="Generate"> <span
                                id="generate__spinner" class="spinner"></span>
                        </div>

                    </form>
                </div>

                <div id="image_container">
                </div>
            </div>
        </div>
    </div>

    <?php

}
