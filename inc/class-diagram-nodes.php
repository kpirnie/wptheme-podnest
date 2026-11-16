<?php

/**
 * Architecture Diagram Node Content
 *
 * @package PodNest
 */

defined('ABSPATH') || exit;

final class PodNest_Diagram_Nodes
{
    public const POST_TYPE = 'podnest_arch_node';

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
                'name'          => __('Diagram Nodes', 'podnest'),
                'singular_name' => __('Diagram Node', 'podnest'),
                'add_new_item'  => __('Add Diagram Node', 'podnest'),
                'edit_item'     => __('Edit Diagram Node', 'podnest'),
                'menu_name'     => __('Diagram Nodes', 'podnest'),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => 'podnest-content',
            'show_in_rest' => true,
            'supports'     => ['title'],
            'rewrite'      => false,
            'has_archive'  => false,
            'menu_icon'    => 'dashicons-networking',
        ]);

        foreach (['_pn_icon', '_pn_subtitle', '_pn_position'] as $field) {
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
            'pn-diagram-node-meta',
            __('Diagram Node Settings', 'podnest'),
            [$this, 'render_meta_box'],
            self::POST_TYPE,
            'normal',
            'high'
        );
    }

    public function render_meta_box(WP_Post $post): void
    {
        wp_nonce_field('pn_diagram_node_save', 'pn_diagram_node_nonce');
        $icon     = (string) get_post_meta($post->ID, '_pn_icon', true);
        $subtitle = (string) get_post_meta($post->ID, '_pn_subtitle', true);
        $position = (string) get_post_meta($post->ID, '_pn_position', true);
        ?>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="pn_icon"><?php esc_html_e('Icon (emoji or short text)', 'podnest'); ?></label></th>
                <td><input type="text" id="pn_icon" name="pn_icon"
                    value="<?php echo esc_attr($icon); ?>" class="regular-text" placeholder="🌐"></td>
            </tr>
            <tr>
                <th scope="row"><label for="pn_subtitle"><?php esc_html_e('Subtitle', 'podnest'); ?></label></th>
                <td><input type="text" id="pn_subtitle" name="pn_subtitle"
                    value="<?php echo esc_attr($subtitle); ?>" class="large-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="pn_position"><?php esc_html_e('Diagram Position', 'podnest'); ?></label></th>
                <td>
                    <select id="pn_position" name="pn_position">
                        <?php foreach (['internet' => 'Internet', 'proxy' => 'Reverse Proxy', 'manager' => 'PodNest Manager'] as $value => $label) : ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($position, $value); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description"><?php esc_html_e('Use one published node for each position.', 'podnest'); ?></p>
                </td>
            </tr>
        </table>
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

        if (! isset($_POST['pn_diagram_node_nonce']) ||
            ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pn_diagram_node_nonce'])), 'pn_diagram_node_save')) {
            return;
        }

        update_post_meta($post_id, '_pn_icon', sanitize_text_field(wp_unslash($_POST['pn_icon'] ?? '')));
        update_post_meta($post_id, '_pn_subtitle', sanitize_text_field(wp_unslash($_POST['pn_subtitle'] ?? '')));
        $position = sanitize_key(wp_unslash($_POST['pn_position'] ?? ''));
        if (in_array($position, ['internet', 'proxy', 'manager'], true)) {
            update_post_meta($post_id, '_pn_position', $position);
        }
    }
}
