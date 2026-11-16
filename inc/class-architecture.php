<?php

/**
 * Architecture Pod Content
 *
 * Registers editable architecture diagram pods for the homepage.
 *
 * @package PodNest
 */

defined('ABSPATH') || exit;

final class PodNest_Architecture
{
    public const POST_TYPE = 'podnest_arch_pod';

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
                'name'          => __('Architecture Pods', 'podnest'),
                'singular_name' => __('Architecture Pod', 'podnest'),
                'add_new_item'  => __('Add Architecture Pod', 'podnest'),
                'edit_item'     => __('Edit Architecture Pod', 'podnest'),
                'menu_name'     => __('Architecture', 'podnest'),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => 'podnest-content',
            'show_in_rest' => true,
            'supports'     => ['title', 'page-attributes'],
            'rewrite'      => false,
            'has_archive'  => false,
            'menu_icon'    => 'dashicons-networking',
        ]);

        foreach (['_pn_icon', '_pn_services'] as $field) {
            register_post_meta(self::POST_TYPE, $field, [
                'show_in_rest'  => true,
                'single'        => true,
                'type'          => 'string',
                'auth_callback' => static fn() => current_user_can('edit_posts'),
            ]);
        }
    }

    public function register_meta_box(): void
    {
        add_meta_box(
            'pn-architecture-meta',
            __('Architecture Pod Settings', 'podnest'),
            [$this, 'render_meta_box'],
            self::POST_TYPE,
            'normal',
            'high'
        );
    }

    public function render_meta_box(WP_Post $post): void
    {
        wp_nonce_field('pn_architecture_save', 'pn_architecture_nonce');
        $icon     = (string) get_post_meta($post->ID, '_pn_icon', true);
        $services = (string) get_post_meta($post->ID, '_pn_services', true);
        ?>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="pn_icon"><?php esc_html_e('Icon (emoji or short text)', 'podnest'); ?></label></th>
                <td><input type="text" id="pn_icon" name="pn_icon"
                    value="<?php echo esc_attr($icon); ?>" class="regular-text" placeholder="🅆"></td>
            </tr>
            <tr>
                <th scope="row"><label for="pn_services"><?php esc_html_e('Services', 'podnest'); ?></label></th>
                <td><input type="text" id="pn_services" name="pn_services"
                    value="<?php echo esc_attr($services); ?>" class="large-text"
                    placeholder="PHP · MariaDB · Valkey · Nginx"></td>
            </tr>
        </table>
        <p class="description">
            <?php esc_html_e('Title → pod label. Services → text beneath the label. Order → diagram position.', 'podnest'); ?>
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

        if (! isset($_POST['pn_architecture_nonce']) ||
            ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pn_architecture_nonce'])), 'pn_architecture_save')) {
            return;
        }

        update_post_meta($post_id, '_pn_icon', sanitize_text_field(wp_unslash($_POST['pn_icon'] ?? '')));
        update_post_meta($post_id, '_pn_services', sanitize_text_field(wp_unslash($_POST['pn_services'] ?? '')));
    }
}
