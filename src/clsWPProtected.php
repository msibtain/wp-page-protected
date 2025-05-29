<?php
class clsWPProtected 
{
    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'wpp_add_meta_box']);
        add_action('save_post', [$this, 'wpp_save_meta_box']);

        add_filter('the_content', [$this, 'wpp_filter_content']);
        add_action('wp_head', [$this, 'wpp_add_styles']); 
    }

    function wpp_add_meta_box() {
        add_meta_box(
            'wpp_meta_box', // Meta box ID
            'Page Protection', // Meta box title
            [$this, 'wpp_meta_box_html'], // Callback function
            'page', // Post type
            'side', // Context
            'high' // Priority
        );
    }

    function wpp_meta_box_html($post) {
        $protected = get_post_meta($post->ID, '_wpp_is_protected', true);
        wp_nonce_field('wpp_save_meta_box', 'wpp_meta_box_nonce');
        ?>
        <p>
            <input type="checkbox" id="wpp_is_protected" name="wpp_is_protected" <?php checked($protected, 'yes'); ?> />
            <label for="wpp_is_protected">Protect this page content</label>
        </p>
        <p class="description">If checked, page content will only be visible to logged-in users.</p>
        <?php
    }

    function wpp_save_meta_box($post_id) {
        // Check if nonce is set
        if (!isset($_POST['wpp_meta_box_nonce'])) {
            return;
        }
    
        // Verify nonce
        if (!wp_verify_nonce($_POST['wpp_meta_box_nonce'], 'wpp_save_meta_box')) {
            return;
        }
    
        // If this is an autosave, don't do anything
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
    
        // Check user permissions
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    
        // Save the protection status
        $is_protected = isset($_POST['wpp_is_protected']) ? 'yes' : 'no';
        update_post_meta($post_id, '_wpp_is_protected', $is_protected);
    }

    function wpp_filter_content($content) {
        // Only filter page content
        if (!is_page()) {
            return $content;
        }
    
        // Get the current post ID
        $post_id = get_the_ID();
        
        // Check if the page is protected
        $is_protected = get_post_meta($post_id, '_wpp_is_protected', true);
        
        // If page is protected and user is not logged in
        if ($is_protected === 'yes' && !is_user_logged_in()) {
            $login_url = wp_login_url(get_permalink($post_id));
            return sprintf(
                '<div class="protected-content-notice">
                    <p>This content is protected. Please <a href="%s">log in</a> to view it.</p>
                </div>',
                esc_url($login_url)
            );
        }
        
        return $content;
    }

    function wpp_add_styles() {
        if (is_page()) {
            ?>
            <style>
                .protected-content-notice {
                    padding: 20px;
                    background-color: #f8f9fa;
                    border: 1px solid #dee2e6;
                    border-radius: 4px;
                    margin: 20px 0;
                    text-align: center;
                }
                .protected-content-notice p {
                    margin: 0;
                    color: #6c757d;
                }
                .protected-content-notice a {
                    color: #007bff;
                    text-decoration: none;
                }
                .protected-content-notice a:hover {
                    text-decoration: underline;
                }
            </style>
            <?php
        }
    }
    
}

new clsWPProtected();