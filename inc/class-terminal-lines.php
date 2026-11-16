<?php

/**
 * Terminal Lines
 *
 * Editable lines for the homepage terminal animation.
 *
 * @package PodNest
 */

defined('ABSPATH') || exit;

final class PodNest_Terminal_Lines
{
    public const POST_TYPE = 'podnest_terminal_line';

    private const STYLES = [
        ''                          => 'Blank / Default',
        'pn-term-out'               => 'Output',
        'pn-term-info'              => 'Information',
        'pn-term-ok'                => 'Success',
        'pn-term-prompt pn-term-cmd' => 'Command',
    ];

    public function __construct()
    {
        add_action('init', [$this, 'register']);
        add_action('add_meta_boxes', [$this, 'register_meta_box']);
        add_action('save_post_' . self::POST_TYPE, [$this, 'save']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_lines'], 20);
        add_filter('manage_' . self::POST_TYPE . '_posts_columns', [$this, 'columns']);
        add_action('manage_' . self::POST_TYPE . '_posts_custom_column', [$this, 'column_content'], 10, 2);
    }

    public function register(): void
    {
        register_post_type(self::POST_TYPE, [
            'labels'       => [
                'name'          => __('Terminal Lines', 'podnest'),
                'singular_name' => __('Terminal Line', 'podnest'),
                'menu_name'     => __('Terminal Lines', 'podnest'),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => 'podnest-content',
            'show_in_rest' => true,
            'supports'     => ['title', 'page-attributes'],
            'has_archive'  => false,
            'rewrite'      => false,
        ]);

        foreach (['_pn_terminal_text' => 'string', '_pn_terminal_style' => 'string', '_pn_terminal_delay' => 'integer'] as $key => $type) {
            register_post_meta(self::POST_TYPE, $key, [
                'show_in_rest'  => true,
                'single'        => true,
                'type'          => $type,
                'auth_callback' => static fn() => current_user_can('edit_posts'),
            ]);
        }
    }

    public function register_meta_box(): void
    {
        add_meta_box('pn-terminal-settings', __('Terminal Settings', 'podnest'),
            [$this, 'render_meta_box'], self::POST_TYPE, 'normal', 'high');
    }

    public function render_meta_box(WP_Post $post): void
    {
        wp_nonce_field('pn_terminal_save', 'pn_terminal_nonce');
        $text  = (string) get_post_meta($post->ID, '_pn_terminal_text', true);
        $style = (string) get_post_meta($post->ID, '_pn_terminal_style', true);
        $delay = (int) get_post_meta($post->ID, '_pn_terminal_delay', true);
        ?>
        <p>
            <label for="pn_terminal_text"><?php esc_html_e('Line text (leave empty for a blank line)', 'podnest'); ?></label><br>
            <input type="text" id="pn_terminal_text" name="pn_terminal_text"
                class="large-text" value="<?php echo esc_attr($text); ?>">
        </p>
        <p>
            <label for="pn_terminal_style"><?php esc_html_e('Appearance', 'podnest'); ?></label><br>
            <select id="pn_terminal_style" name="pn_terminal_style">
                <?php foreach (self::STYLES as $value => $label) : ?>
                    <option value="<?php echo esc_attr($value); ?>" <?php selected($style, $value); ?>>
                        <?php echo esc_html__($label, 'podnest'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="pn_terminal_delay"><?php esc_html_e('Delay (milliseconds after animation starts)', 'podnest'); ?></label><br>
            <input type="number" id="pn_terminal_delay" name="pn_terminal_delay"
                min="0" max="60000" step="100" value="<?php echo esc_attr($delay); ?>">
        </p>
        <p class="description"><?php esc_html_e('Title is for admin identification only. Use Order to arrange lines; delays determine their appearance time.', 'podnest'); ?></p>
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
        if (! isset($_POST['pn_terminal_nonce']) ||
            ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pn_terminal_nonce'])), 'pn_terminal_save')) {
            return;
        }

        $text = isset($_POST['pn_terminal_text']) && is_string($_POST['pn_terminal_text'])
            ? sanitize_text_field(wp_unslash($_POST['pn_terminal_text'])) : '';
        $style = isset($_POST['pn_terminal_style']) && is_string($_POST['pn_terminal_style'])
            ? sanitize_text_field(wp_unslash($_POST['pn_terminal_style'])) : '';
        $delay = isset($_POST['pn_terminal_delay']) ? min(60000, max(0, absint($_POST['pn_terminal_delay']))) : 0;

        update_post_meta($post_id, '_pn_terminal_text', $text);
        update_post_meta($post_id, '_pn_terminal_style', isset(self::STYLES[$style]) ? $style : '');
        update_post_meta($post_id, '_pn_terminal_delay', $delay);
    }

    public function enqueue_lines(): void
    {
        if (! is_front_page()) {
            return;
        }

        $posts = get_posts([
            'post_type'      => self::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
        ]);

        if (empty($posts)) {
            return;
        }

        $lines = [];
        foreach ($posts as $post) {
            $style = (string) get_post_meta($post->ID, '_pn_terminal_style', true);
            $lines[] = [
                'text'  => (string) get_post_meta($post->ID, '_pn_terminal_text', true),
                'cls'   => isset(self::STYLES[$style]) ? $style : '',
                'delay' => min(60000, max(0, (int) get_post_meta($post->ID, '_pn_terminal_delay', true))),
            ];
        }

        wp_add_inline_script('podnest-app', 'window.podnestTerminalLines = ' . wp_json_encode($lines) . ';', 'before');
    }

    public function columns(array $columns): array
    {
        $result = [];
        foreach ($columns as $key => $label) {
            $result[$key] = $label;
            if ('title' === $key) {
                $result['pn_terminal_text'] = __('Text', 'podnest');
                $result['pn_terminal_delay'] = __('Delay (ms)', 'podnest');
            }
        }
        return $result;
    }

    public function column_content(string $column, int $post_id): void
    {
        if ('pn_terminal_text' === $column) {
            echo esc_html((string) get_post_meta($post_id, '_pn_terminal_text', true));
        }
        if ('pn_terminal_delay' === $column) {
            echo esc_html((string) get_post_meta($post_id, '_pn_terminal_delay', true));
        }
    }
}
