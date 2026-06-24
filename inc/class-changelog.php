<?php

/**
 * Changelog — GitHub Commit Feed
 *
 * Fetches recent commit messages from the PodNest repository and caches
 * them in a transient. A daily WP-Cron event refreshes the cache so the
 * public changelog stays current without hitting the API on page load.
 * Rendered on the front end by the podnest/changelog block.
 *
 * @package PodNest
 * @since   1.2.0
 */

defined('ABSPATH') || exit;

/**
 * Class PodNest_Changelog
 *
 * Instantiated once by PodNest_Theme to register the refresh cron. Data
 * access and rendering are static so the block render callback can use
 * them without re-instantiating (which would re-run cron scheduling).
 */
final class PodNest_Changelog
{
    /** Transient key holding the parsed commit array. */
    private const CACHE_KEY = 'podnest_changelog_commits';

    /** Cron hook fired once daily to refresh the cache. */
    private const CRON_HOOK = 'podnest_changelog_refresh';

    /** GitHub commits API endpoint for the main branch (100 = API max). */
    private const API_URL = 'https://api.github.com/repos/kpirnie/podnest/commits?sha=main&per_page=100';

    // -- Constructor -----------------------------------------------

    /**
     * Hooks the daily refresh cron and ensures it is scheduled. Also
     * clears the schedule when the theme is switched away.
     */
    public function __construct()
    {
        add_action(self::CRON_HOOK, [self::class, 'refresh']);
        add_action('switch_theme', [self::class, 'unschedule']);
        add_shortcode('podnest_version', [self::class, 'version_shortcode']);

        if (! wp_next_scheduled(self::CRON_HOOK)) {
            wp_schedule_event(time(), 'daily', self::CRON_HOOK);
        }
    }

    // -- Data ------------------------------------------------------

    /**
     * Returns cached commits, fetching once if the cache is cold.
     *
     * @param  int $count Max commits to return. 0 (or negative) = all cached.
     * @return array<int, array{message: string, url: string, date: string}>
     */
    public static function commits(int $count = 0): array
    {
        $commits = get_transient(self::CACHE_KEY);

        if (false === $commits) {
            $commits = self::refresh();
        }

        if (! is_array($commits)) {
            return [];
        }

        return $count > 0 ? array_slice($commits, 0, $count) : $commits;
    }

    /**
     * Fetches commits from GitHub, parses them, and stores the transient.
     *
     * Merge commits are skipped; only the subject line of each message is
     * kept. Runs daily via cron and lazily on a cold cache. On failure the
     * existing (stale) cache is preserved rather than blanking the page.
     *
     * @return array<int, array{message: string, url: string, date: string}>
     */
    public static function refresh(): array
    {
        $response = wp_remote_get(self::API_URL, [
            'headers' => ['User-Agent' => 'wp-podnest-theme'],
            'timeout' => 8,
        ]);

        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            $stale = get_transient(self::CACHE_KEY);
            return is_array($stale) ? $stale : [];
        }

        $raw = json_decode(wp_remote_retrieve_body($response), true);

        if (! is_array($raw)) {
            return [];
        }

        $commits = [];
        foreach ($raw as $item) {
            $message = (string) ($item['commit']['message'] ?? '');
            if ('' === $message || str_starts_with($message, 'Merge ')) {
                continue;
            }

            /* Subject line only — drop the body */
            $commits[] = [
                'message' => trim(strtok($message, "\n")),
                'url'     => (string) ($item['html_url'] ?? ''),
                'date'    => (string) ($item['commit']['author']['date'] ?? ''),
            ];
        }

        set_transient(self::CACHE_KEY, $commits, DAY_IN_SECONDS);

        return $commits;
    }

    /**
     * Clears the scheduled refresh event (on theme switch).
     *
     * @return void
     */
    public static function unschedule(): void
    {
        $timestamp = wp_next_scheduled(self::CRON_HOOK);
        if ($timestamp) {
            wp_unschedule_event($timestamp, self::CRON_HOOK);
        }
    }

    // -- Render ----------------------------------------------------

    /**
     * Builds the changelog list HTML.
     *
     * @param  int $count Max commits to show. 0 = all cached.
     * @return string
     */
    public static function render_html(int $count = 5): string
    {
        $commits = self::commits($count);

        if (empty($commits)) {
            return '<p class="pn-muted" style="font-style:italic;">'
                . esc_html__('No changelog entries available right now.', 'podnest')
                . '</p>';
        }

        $items = '';
        foreach ($commits as $c) {
            $date = $c['date']
                ? date_i18n(get_option('date_format'), strtotime($c['date']))
                : '';

            $msg = $c['url']
                ? sprintf('<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>', esc_url($c['url']), esc_html($c['message']))
                : esc_html($c['message']);

            $items .= sprintf(
                '<li class="pn-changelog-item"><span class="pn-changelog-msg">%s</span>%s</li>',
                $msg,
                $date ? '<time class="pn-changelog-date" datetime="' . esc_attr($c['date']) . '">' . esc_html($date) . '</time>' : ''
            );
        }

        return '<ul class="pn-changelog">' . $items . '</ul>';
    }

    /**
     * Shortcode: outputs the latest PodNest version as a styled chip.
     *
     * Usage: [podnest_version]  or  [podnest_version bare="1"] for text only.
     *
     * @param  array<string, mixed>|string $atts Shortcode attributes.
     * @return string
     */
    public static function version_shortcode($atts = []): string
    {
        $atts    = shortcode_atts(['bare' => '0'], (array) $atts, 'podnest_version');
        $version = function_exists('podnest_latest_version') ? podnest_latest_version() : '';

        if ('' === $version) {
            return '';
        }

        if ('1' === (string) $atts['bare']) {
            return esc_html($version);
        }

        return '<span class="pn-version-chip">' . esc_html($version) . '</span>';
    }
}
