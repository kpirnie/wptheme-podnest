<?php

/**
 * Admin Icon Columns
 *
 * Displays icon metadata in Security, Quickstart, and Architecture lists.
 *
 * @package PodNest
 */

defined('ABSPATH') || exit;

final class PodNest_Admin_Icon_Columns
{
    public function __construct()
    {
        foreach ([
            PodNest_CPTs::SECURITY,
            PodNest_Quickstart::POST_TYPE,
            PodNest_Architecture::POST_TYPE,
        ] as $post_type) {
            add_filter("manage_{$post_type}_posts_columns", [$this, 'columns']);
            add_action("manage_{$post_type}_posts_custom_column", [$this, 'column_content'], 10, 2);
        }
    }

    public function columns(array $columns): array
    {
        $result = [];

        foreach ($columns as $key => $label) {
            $result[$key] = $label;

            if ('title' === $key) {
                $result['pn_icon'] = __('Icon', 'podnest');
            }
        }

        return $result;
    }

    public function column_content(string $column, int $post_id): void
    {
        if ('pn_icon' === $column) {
            echo esc_html((string) get_post_meta($post_id, '_pn_icon', true));
        }
    }
}
