<?php

/**
 * Editable Homepage Section Copy
 *
 * @package PodNest
 */

defined('ABSPATH') || exit;

final class PodNest_Section_Copy
{
    public const POST_TYPE = 'podnest_section_copy';

    private const SECTIONS = [
        'features'     => 'Features',
        'architecture' => 'Architecture',
        'runtimes'     => 'Runtimes',
        'security'     => 'Security',
        'cta'          => 'Get Started',
        'pricing'      => 'Pricing',
    ];

    public function __construct()
    {
        add_action('init', [$this, 'register']);
        add_action('add_meta_boxes', [$this, 'register_meta_box']);
        add_action('save_post_' . self::POST_TYPE, [$this, 'save']);
        add_filter('manage_' . self::POST_TYPE . '_posts_columns', [$this, 'columns']);
        add_action('manage_' . self::POST_TYPE . '_posts_custom_column', [$this, 'column_content'], 10, 2);
    }

    public function register(): void
    {
        register_post_type(self::POST_TYPE, [
            'labels' => [
                'name'          => __('Section Copy', 'podnest'),
                'singular_name' => __('Section Copy', 'podnest'),
                'menu_name'     => __('Section Copy', 'podnest'),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => 'podnest-content',
            'show_in_rest' => true,
            'supports'     => ['title', 'excerpt'],
            'rewrite'      => false,
            'has_archive'  => false,
        ]);

        foreach (['_pn_section', '_pn_eyebrow'] as $field) {
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
        add_meta_box('pn-section-copy-meta', __('Section Settings', 'podnest'),
            [$this, 'render_meta_box'], self::POST_TYPE, 'normal', 'high');
    }

    public function render_meta_box(WP_Post $post): void
    {
        wp_nonce_field('pn_section_copy_save', 'pn_section_copy_nonce');
        $section = (string) get_post_meta($post->ID, '_pn_section', true);
        $eyebrow = (string) get_post_meta($post->ID, '_pn_eyebrow', true);
        ?>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="pn_section"><?php esc_html_e('Section', 'podnest'); ?></label></th>
                <td>
                    <select id="pn_section" name="pn_section">
                        <?php foreach (self::SECTIONS as $value => $label) : ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($section, $value); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="pn_eyebrow"><?php esc_html_e('Eyebrow', 'podnest'); ?></label></th>
                <td><input id="pn_eyebrow" name="pn_eyebrow" type="text"
                    value="<?php echo esc_attr($eyebrow); ?>" class="regular-text"></td>
            </tr>
        </table>
        <p class="description"><?php esc_html_e('Title = heading. Excerpt = description. Create one published post per section.', 'podnest'); ?></p>
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

        if (! isset($_POST['pn_section_copy_nonce']) ||
            ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pn_section_copy_nonce'])), 'pn_section_copy_save')) {
            return;
        }

        $section = sanitize_key(wp_unslash($_POST['pn_section'] ?? ''));
        if (isset(self::SECTIONS[$section])) {
            update_post_meta($post_id, '_pn_section', $section);
        }
        update_post_meta($post_id, '_pn_eyebrow', sanitize_text_field(wp_unslash($_POST['pn_eyebrow'] ?? '')));
    }

    public function columns(array $columns): array
    {
        $result = [];
        foreach ($columns as $key => $label) {
            $result[$key] = $label;
            if ('title' === $key) {
                $result['pn_section'] = __('Section', 'podnest');
            }
        }
        return $result;
    }

    public function column_content(string $column, int $post_id): void
    {
        if ('pn_section' === $column) {
            $section = (string) get_post_meta($post_id, '_pn_section', true);
            echo esc_html(self::SECTIONS[$section] ?? '—');
        }
    }

    public static function value(string $section, string $field, string $fallback): string
    {
        static $posts = [];
        if (! array_key_exists($section, $posts)) {
            $matches = get_posts([
                'post_type'      => self::POST_TYPE,
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'meta_key'       => '_pn_section',
                'meta_value'     => $section,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ]);
            $posts[$section] = $matches[0] ?? null;
        }

        $post = $posts[$section];
        if (! $post) {
            return $fallback;
        }

        if ('title' === $field) {
            $value = $post->post_title;
        } elseif ('description' === $field) {
            $value = $post->post_excerpt;
        } elseif ('eyebrow' === $field) {
            $value = get_post_meta($post->ID, '_pn_eyebrow', true);
        } else {
            return $fallback;
        }

        return '' !== trim((string) $value) ? (string) $value : $fallback;
    }
}
