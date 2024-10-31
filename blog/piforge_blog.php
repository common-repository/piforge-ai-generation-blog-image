<?php

function piforge_blog_submenu_content()
{


    if (!current_user_can('edit_posts') || (wp_doing_ajax())) {
        return wp_die('You are not allowed to access this part of the site. <br/>Please contact your administrator.');
    }
    ?>

    <div class="wrap" id="wrap">
        <h2>PiForge | Auto Blogging</h2>
        <div style="display:flex; align-items:center;justify-content:space-between;">
            <a href="https://piforge.ai" target="_BLANK">
                <img style="width:40px;background-color: black; border-radius: 10px; padding:5px;  "
                    src="<?php echo esc_html(plugin_dir_url(dirname(__FILE__))) . 'assets/logo_square_1024_1024.png'; ?>">
            </a>
            <h3>Pro IA generation on demand</h3>
        </div>

        <div id="custom_notice" class="notice  is-dismissible"></div>
        <div id="blog_container">

            <div id="cols_ontainer">
                <div id="left_col">
                    <form id="piforge_request_blog_form">
                        <?php wp_nonce_field('piforge_request_blog_form', 'blog'); ?>
                        <label for="language">Choose language</label>
                        <select name="language" id="language" required>
                            <option value="english">English</option>
                            <option value="french">French</option>
                            <option value="german">German</option>
                            <option value="chinese">Chinese</option>
                            <option value="spanish">Spanish</option>
                            <option value="hindi">Hindi</option>
                            <option value="arabic">Arabic</option>
                            <option value="bengali">Bengali</option>
                            <option value="portuguese">Portuguese</option>
                            <option value="russian">Russian</option>
                            <option value="japanese">Japanese</option>
                        </select>
                        <label for="target">Target SEO request</label>
                        <textarea id="target" name="target" placeholder="How to rank a blog on google" rows="6" cols="40"
                            required></textarea>
                        <label for="subject">Subject of your content</label>
                        <textarea id="subject" name="subject" placeholder="Tutorials and Seo help" rows="6" cols="40"
                            required></textarea>


                        <label for="imperative_word">Imperative word you want in your content <br> (Separate with
                            ";")</label>
                        <textarea id="imperative_word" name="imperative_word"
                            placeholder="SEO; Piforge; AI; Ranking; SemRush; Wordpress;" rows="6" cols="40"
                            required></textarea>

                        <label for="word_number">The length of your content</label>

                        <select name="word_number" id="word_number" required>
                            <option value="500">Short</option>
                            <option value="1200">Long</option>
                        </select>
                        <label for="structure">The structure of your content</label>
                        <select name="structure" id="structure" required>
                            <option value="h1 p h2 p p h3 ul>li h3 p p h4">Simple</option>
                            <option
                                value="h1 p h2 p p ul>li h3 ul>li h3 p p h4 p p h4 p p h2 p p  ul>li h3 ul>li h3 p p h4 p p h4 p p">
                                Complex</option>
                        </select>

                        <label> </label>
                        <div style="display:flex;">
                            <input type="submit" class="button-primary" id="form_button_2" value="Generate"> <span
                                id="generate__spinner" class="spinner"></span>
                        </div>

                    </form>
                </div>
                <div id="editor_container" style="">
                    <button class="button saveDraftBtn" id="save_draft_button">Save as draft</button>
                    <?php
                    // Affiche le contenu de l'éditeur classique
                    wp_editor(
                        "",
                        'auto-blog-editor',
                        array(
                            'media_buttons' => true,
                            'textarea_name' => 'auto_blog_content',
                            'tinymce' => true,
                            'quicktags' => true,
                            'teeny' => false,
                            'textarea_rows' => 27,
                            'editor_class' => 'auto-blog-editor'
                        )
                    );

                    ?>
                    <button class="button saveDraftBtn" id="save_draft_button">Save as
                        draft</button>
                </div>
            </div>
        </div>
    </div>

    <?php
}