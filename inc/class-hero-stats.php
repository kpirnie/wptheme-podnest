<?php

/**
 * Hero Statistics
 *
 * @package PodNest
 */

defined('ABSPATH') || exit;

final class PodNest_Hero_Stats
{
    public const POST_TYPE = 'podnest_hero_stat';

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
            'labels'        => [
                'name'          => __('Hero Stats', 'podnest'),
                'singular_name' => __('Hero Stat', 'podnest'),
                'menu_name'     => __('Hero Stats', 'podnest'),
            ],
            'public'        => false,
            'show_ui'       => true,
            'show_in_menu'  => 'podnest-content',
            'show_in_rest'  => true,
            'supports'      => ['title', 'page-attributes'],
            'has_archive'   => false,
            'rewrite'       => false,
        ]);

        register_post_meta(self::POST_TYPE, '_pn_stat_value', [
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => 'string',
            'auth_callback' => static fn() => current_user_can('edit_posts'),
        ]);
        register_post_meta(self::POST_TYPE, '_pn_stat_latest_version', [
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => 'boolean',
            'auth_callback' => static fn() => current_user_can('edit_posts'),
        ]);
    }

    public function register_meta_box(): void
    {
        add_meta_box('pn-hero-stat-meta', __('Stat Settings', 'podnest'),
            [$this, 'render_meta_box'], self::POST_TYPE, 'normal', 'high');
    }

    public function render_meta_box(WP_Post $post): void
    {
        wp_nonce_field('pn_hero_stat_save', 'pn_hero_stat_nonce');
        $value = (string) get_post_meta($post->ID, '_pn_stat_value', true);
        $latest = (bool) get_post_meta($post->ID, '_pn_stat_latest_version', true);
        ?>
        <p>
            <label for="pn_stat_value"><?php esc_html_e('Value', 'podnest'); ?></label>
            <input type="text" id="pn_stat_value" name="pn_stat_value"
                value="<?php echo esc_attr($value); ?>" class="regular-text">
        </p>
        <p>
            <label>
                <input type="checkbox" name="pn_stat_latest_version" value="1" <?php checked($latest); ?>>
                <?php esc_html_e('Use current PodNest release instead of Value', 'podnest'); ?>
            </label>
        </p>
        <p class="description"><?php esc_html_e('Title = label. Set Order to control display order.', 'podnest'); ?></p>
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
        if (! isset($_POST['pn_hero_stat_nonce']) ||
            ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pn_hero_stat_nonce'])), 'pn_hero_stat_save')) {
            return;
        }

        update_post_meta($post_id, '_pn_stat_value', sanitize_text_field(wp_unslash($_POST['pn_stat_value'] ?? '')));
        update_post_meta($post_id, '_pn_stat_latest_version', ! empty($_POST['pn_stat_latest_version']));
    }

    public function columns(array $columns): array
    {
        $result = [];
        foreach ($columns as $key => $label) {
            $result[$key] = $label;
            if ('title' === $key) {
                $result['pn_value'] = __('Value', 'podnest');
            }
        }
        return $result;
    }

    public function column_content(string $column, int $post_id): void
    {
        if ('pn_value' === $column) {
            echo esc_html(get_post_meta($post_id, '_pn_stat_latest_version', true)
                ? __('Latest release (dynamic)', 'podnest')
                : (string) get_post_meta($post_id, '_pn_stat_value', true));
        }
    }

    public static function items(): array
    {
        $posts = get_posts([
            'post_type'      => self::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
        ]);

        if (! $posts) {
            return [
                ['MIT', 'License'],
                ['Go', 'Language'],
                ['Podman', 'Runtime'],
                [podnest_latest_version(), 'Released'],
            ];
        }

        $items = [];
        foreach ($posts as $post) {
            $value = get_post_meta($post->ID, '_pn_stat_latest_version', true)
                ? podnest_latest_version()
                : (string) get_post_meta($post->ID, '_pn_stat_value', true);
            $items[] = [$value, $post->post_title];
        }
        return $items;
    }
}
