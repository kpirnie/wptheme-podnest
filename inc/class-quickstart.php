<?php

/**
 * Quickstart Method Content
 *
 * Registers editable quickstart methods for the homepage CTA.
 *
 * @package PodNest
 */

defined('ABSPATH') || exit;

final class PodNest_Quickstart
{
    public const POST_TYPE = 'podnest_quickstart';

    public function __construct()
    {
        add_action('init', [$this, 'register']);
        add_action('add_meta_boxes', [$this, 'register_meta_box']);
        add_action('save_post_' . self::POST_TYPE, [$this, 'save']);
    }

    public function register(): void
    {
        register_post_type(self::POST_TYPE, [
            'labels' => [
                'name'          => __('Quickstart Methods', 'podnest'),
                'singular_name' => __('Quickstart Method', 'podnest'),
                'add_new_item'  => __('Add Quickstart Method', 'podnest'),
                'edit_item'     => __('Edit Quickstart Method', 'podnest'),
                'menu_name'     => __('Quickstart', 'podnest'),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => 'podnest-content',
            'show_in_rest' => true,
            'supports'     => ['title', 'editor', 'excerpt', 'page-attributes'],
            'rewrite'      => false,
            'has_archive'  => false,
            'menu_icon'    => 'dashicons-admin-tools',
        ]);

        register_post_meta(self::POST_TYPE, '_pn_icon', [
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => 'string',
            'auth_callback' => static fn() => current_user_can('edit_posts'),
            'description'   => 'Emoji or short icon for the quickstart method.',
        ]);
    }

    public function register_meta_box(): void
    {
        add_meta_box(
            'pn-quickstart-meta',
            __('Quickstart Settings', 'podnest'),
            [$this, 'render_meta_box'],
            self::POST_TYPE,
            'side',
            'high'
        );
    }

    public function render_meta_box(WP_Post $post): void
    {
        wp_nonce_field('pn_quickstart_save', 'pn_quickstart_nonce');
        $icon = (string) get_post_meta($post->ID, '_pn_icon', true);
        ?>
        <p>
            <label for="pn_icon" style="display:block;font-weight:600;margin-bottom:4px;">
                <?php esc_html_e('Icon (emoji or short text)', 'podnest'); ?>
            </label>
            <input type="text"
                id="pn_icon"
                name="pn_icon"
                value="<?php echo esc_attr($icon); ?>"
                style="width:100%;"
                placeholder="🚀">
        </p>
        <p class="description">
            <?php esc_html_e('Title → method label. Excerpt/Content → description.', 'podnest'); ?>
        </p>
        <?php
    }

    public function save(int $post_id): void
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (! current_user_can('edit_post', $post_id)) {
            return;
        }

        if (! isset($_POST['pn_quickstart_nonce']) ||
            ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pn_quickstart_nonce'])), 'pn_quickstart_save')) {
            return;
        }

        update_post_meta($post_id, '_pn_icon', sanitize_text_field(wp_unslash($_POST['pn_icon'] ?? '')));
    }
}
