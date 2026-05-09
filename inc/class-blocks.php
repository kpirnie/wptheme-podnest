<?php

/**
 * Gutenberg Block Registration & Server-Side Render Callbacks
 *
 * Each of the four PodNest blocks is a dynamic (server-side-rendered)
 * block. The editor JS (assets/js/editor/index.js) registers a client-
 * side placeholder; the actual HTML is generated here at render time so
 * it always reflects the current CPT content without requiring re-saving
 * pages that embed the blocks.
 *
 * @package PodNest
 * @since   1.1.0
 */

defined('ABSPATH') || exit;

/**
 * Class PodNest_Blocks
 *
 * Self-registers all hooks in the constructor. Instantiated once by
 * {@see PodNest_Theme::boot_feature_classes()}.
 */
final class PodNest_Blocks
{

    // ── Block name constants ──────────────────────────────────────

    /** Fully-qualified name of the marquee strip block. */
    public const MARQUEE  = 'podnest/marquee-strip';

    /** Fully-qualified name of the features grid block. */
    public const FEATURES = 'podnest/features-grid';

    /** Fully-qualified name of the runtimes grid block. */
    public const RUNTIMES = 'podnest/runtimes-grid';

    /** Fully-qualified name of the pricing table block. */
    public const PRICING  = 'podnest/pricing-table';

    // ── Constructor ───────────────────────────────────────────────

    /**
     * Registers the 'init' hook that registers all four blocks.
     */
    public function __construct()
    {
        add_filter('block_categories_all', [$this, 'register_category'], 5);
        add_action('init', [$this, 'register']);
    }

    // ── Block registration ────────────────────────────────────────

    /**
     * Registers all four blocks with WordPress.
     *
     * The editor_script handle must match what is enqueued in
     * {@see PodNest_Assets::enqueue_editor()}.
     *
     * @return void
     */
    public function register(): void
    {

        /* Shared editor script handle */
        $editor_script = 'podnest-blocks-editor';

        register_block_type(self::MARQUEE, [
            'editor_script'   => $editor_script,
            'render_callback' => [$this, 'render_marquee'],
            'attributes'      => [],
        ]);

        register_block_type(self::FEATURES, [
            'editor_script'   => $editor_script,
            'render_callback' => [$this, 'render_features'],
            'attributes'      => [
                /* Number of columns in the grid (2–4). */
                'columns' => ['type' => 'number', 'default' => 3],
            ],
        ]);

        register_block_type(self::RUNTIMES, [
            'editor_script'   => $editor_script,
            'render_callback' => [$this, 'render_runtimes'],
            'attributes'      => [
                /* Number of columns in the grid (2–5). */
                'columns' => ['type' => 'number', 'default' => 4],
            ],
        ]);

        register_block_type(self::PRICING, [
            'editor_script'   => $editor_script,
            'render_callback' => [$this, 'render_pricing'],
            'attributes'      => [],
        ]);
    }

    public function register_category(array $cats): array
    {
        array_unshift($cats, [
            'slug'  => 'podnest',
            'title' => 'PodNest',
            'icon'  => 'dashicons-shield',
        ]);
        return $cats;
    }

    // ── Public render callbacks ───────────────────────────────────

    /**
     * Renders the marquee strip block.
     *
     * Queries all published Marquee Items ordered by menu_order and outputs
     * a scrolling marquee track. Falls back to a hardcoded list when no
     * posts exist yet so the front page is never blank out of the box.
     *
     * @param  array<string, mixed> $attrs Block attributes (unused — no configurable attrs).
     * @param  string               $content Inner block content (unused — no inner blocks).
     * @return string HTML output.
     */
    public function render_marquee(array $attrs, string $content): string
    {
        $posts = $this->get_cpt_posts(PodNest_CPTs::MARQUEE);

        if (empty($posts)) {
            /* Build items from the hardcoded fallback set */
            $items = array_map(
                static fn(string $text) => '<div class="pn-marquee-item"><span class="pn-dot"></span>' . esc_html($text) . '</div>',
                $this->fallback_marquee_items()
            );
        } else {
            $items = array_map(
                static fn(WP_Post $p) => '<div class="pn-marquee-item"><span class="pn-dot"></span>' . esc_html($p->post_title) . '</div>',
                $posts
            );
        }

        return sprintf(
            '<div class="pn-marquee-strip" aria-hidden="true"><div class="pn-marquee-track">%s</div></div>',
            implode('', $items)
        );
    }

    /**
     * Renders the features grid block.
     *
     * Each Feature post contributes one card. The icon emoji, title, and
     * excerpt are pulled from the post; the optional learn-more URL comes
     * from post meta.
     *
     * @param  array<string, mixed> $attrs Block attributes. Expects 'columns' (int).
     * @param  string               $content Unused inner content.
     * @return string HTML output.
     */
    public function render_features(array $attrs, string $content): string
    {
        $posts = $this->get_cpt_posts(PodNest_CPTs::FEATURE);
        $cols  = max(2, min(4, (int) ($attrs['columns'] ?? 3)));

        if (empty($posts)) {
            return $this->empty_notice(__('Add Feature posts under PodNest Content → Features.', 'podnest'));
        }

        $cards = '';
        foreach ($posts as $i => $post) {
            $icon     = (string) get_post_meta($post->ID, '_pn_icon', true);
            $more_url = (string) get_post_meta($post->ID, '_pn_learn_more_url', true);
            $desc     = $post->post_excerpt
                ?: wp_trim_words(wp_strip_all_tags($post->post_content), 24, '…');
            $delay    = ($i % $cols) + 1;

            $more_link = $more_url
                ? sprintf(
                    '<a href="%s" class="pn-btn-ghost" style="margin-top:14px;font-size:0.82rem;">%s <span class="pn-arrow">→</span></a>',
                    esc_url($more_url),
                    esc_html__('Learn more', 'podnest')
                )
                : '';

            $cards .= sprintf(
                '<article class="pn-feature-card pn-reveal pn-reveal-delay-%d">%s<h3>%s</h3><p>%s</p>%s</article>',
                $delay,
                $icon ? '<span class="pn-feature-icon" aria-hidden="true">' . esc_html($icon) . '</span>' : '',
                esc_html($post->post_title),
                esc_html($desc),
                $more_link
            );
        }

        return sprintf('<div class="pn-grid-%d pn-features-grid">%s</div>', $cols, $cards);
    }

    /**
     * Renders the runtimes grid block.
     *
     * Each Runtime post contributes one card with icon, title, excerpt,
     * version chip badges, and an optional learn-more link.
     *
     * @param  array<string, mixed> $attrs Block attributes. Expects 'columns' (int).
     * @param  string               $content Unused inner content.
     * @return string HTML output.
     */
    public function render_runtimes(array $attrs, string $content): string
    {
        $posts = $this->get_cpt_posts(PodNest_CPTs::RUNTIME);
        $cols  = max(2, min(5, (int) ($attrs['columns'] ?? 4)));

        if (empty($posts)) {
            return $this->empty_notice(__('Add Runtime posts under PodNest Content → Runtimes.', 'podnest'));
        }

        $cards = '';
        foreach ($posts as $i => $post) {
            $icon     = (string) get_post_meta($post->ID, '_pn_icon', true);
            $versions = (string) get_post_meta($post->ID, '_pn_versions', true);
            $more_url = (string) get_post_meta($post->ID, '_pn_learn_more_url', true);
            $desc     = $post->post_excerpt
                ?: wp_trim_words(wp_strip_all_tags($post->post_content), 18, '…');
            $delay    = ($i % $cols) + 1;

            /* Build version chip badges */
            $chips = '';
            if ($versions) {
                $vlist = array_filter(array_map('trim', explode("\n", $versions)));
                foreach ($vlist as $v) {
                    $chips .= '<span class="pn-version-chip">' . esc_html($v) . '</span>';
                }
            }

            $more_link = $more_url
                ? sprintf(
                    '<a href="%s" class="pn-btn-ghost" style="margin-top:16px;font-size:0.82rem;justify-content:center;">%s <span class="pn-arrow">→</span></a>',
                    esc_url($more_url),
                    esc_html__('Learn more', 'podnest')
                )
                : '';

            $cards .= sprintf(
                '<article class="pn-site-type-card pn-reveal pn-reveal-delay-%d">%s<h3>%s</h3><p class="pn-muted" style="font-size:0.83rem;line-height:1.6;">%s</p>%s%s</article>',
                $delay,
                $icon ? '<span class="pn-site-type-icon" aria-hidden="true">' . esc_html($icon) . '</span>' : '',
                esc_html($post->post_title),
                esc_html($desc),
                $chips ? '<div class="pn-site-type-versions">' . $chips . '</div>' : '',
                $more_link
            );
        }

        return sprintf('<div class="pn-grid-%d">%s</div>', $cols, $cards);
    }

    /**
     * Renders the pricing table block.
     *
     * Each Pricing post becomes one pricing card. The featured card is
     * visually distinguished with a border, scale, and optional badge.
     * Feature list items prefixed with "x:" are rendered as unavailable.
     *
     * @param  array<string, mixed> $attrs Block attributes (unused).
     * @param  string               $content Unused inner content.
     * @return string HTML output.
     */
    public function render_pricing(array $attrs, string $content): string
    {
        $posts = $this->get_cpt_posts(PodNest_CPTs::PRICING);

        if (empty($posts)) {
            return $this->empty_notice(__('Add Pricing Tier posts under PodNest Content → Pricing.', 'podnest'));
        }

        $cards = '';
        foreach ($posts as $i => $post) {
            $price      = (string) get_post_meta($post->ID, '_pn_price', true);
            $unit       = (string) (get_post_meta($post->ID, '_pn_price_unit', true) ?: '/ hour');
            $tier       = (string) get_post_meta($post->ID, '_pn_tier_label', true);
            $badge      = (string) get_post_meta($post->ID, '_pn_badge_text', true);
            $featured   = (bool)   get_post_meta($post->ID, '_pn_is_featured', true);
            $feat_list  = (string) get_post_meta($post->ID, '_pn_features_list', true);
            $cta_text   = (string) (get_post_meta($post->ID, '_pn_cta_text', true) ?: __('Get in Touch', 'podnest'));
            $cta_url    = (string) (get_post_meta($post->ID, '_pn_cta_url', true) ?: '#contact');
            $desc       = wp_strip_all_tags(apply_filters('the_content', $post->post_content));
            $card_class = 'pn-pricing-card pn-reveal pn-reveal-delay-' . ($i + 1) . ($featured ? ' pn-featured' : '');
            $btn_class  = $featured ? 'pn-btn-primary' : 'pn-btn-secondary';

            $badge_html = ($badge && $featured)
                ? '<span class="pn-pricing-badge">' . esc_html($badge) . '</span>'
                : '';

            $tier_html  = $tier
                ? '<span class="pn-pricing-tier">' . esc_html($tier) . '</span>'
                : '';

            $desc_html  = $desc
                ? '<p>' . esc_html(wp_trim_words($desc, 20, '…')) . '</p>'
                : '';

            /* Build the included/excluded feature list */
            $list_html = '';
            if ($feat_list) {
                $list_html = '<ul class="pn-pricing-features">';
                foreach ($this->parse_pricing_features($feat_list) as $item) {
                    $list_html .= sprintf(
                        '<li><span class="%s" aria-hidden="true">%s</span><span%s>%s</span></li>',
                        $item['available'] ? 'pn-check' : 'pn-x',
                        $item['available'] ? '✓' : '✗',
                        $item['available'] ? '' : ' style="opacity:0.45"',
                        esc_html($item['text'])
                    );
                }
                $list_html .= '</ul>';
            }

            $cards .= sprintf(
                '<article class="%s">%s%s<div class="pn-pricing-price"><span class="pn-price-amount">$%s</span><span class="pn-price-unit">%s</span></div>%s%s<a href="%s" class="%s" rel="noopener">%s</a></article>',
                esc_attr($card_class),
                $badge_html,
                $tier_html,
                esc_html($price),
                esc_html($unit),
                $desc_html,
                $list_html,
                esc_url($cta_url),
                $btn_class,
                esc_html($cta_text)
            );
        }

        return '<div class="pn-pricing-grid">' . $cards . '</div>';
    }

    // ── Private query helpers ─────────────────────────────────────

    /**
     * Fetches published posts for a given CPT, ordered by menu_order then date.
     *
     * Suppresses the query filter to avoid interference from other plugins.
     *
     * @param  string $post_type The CPT slug, e.g. PodNest_CPTs::FEATURE.
     * @return WP_Post[]
     */
    private function get_cpt_posts(string $post_type): array
    {
        return get_posts([
            'post_type'              => $post_type,
            'posts_per_page'         => -1,
            'orderby'                => ['menu_order' => 'ASC', 'date' => 'ASC'],
            'post_status'            => 'publish',
            'no_found_rows'          => true,      // Skip COUNT(*) — we don't paginate.
            'update_post_term_cache' => false,     // No taxonomies on these CPTs.
            'update_post_meta_cache' => true,      // Meta is needed for render.
        ]);
    }

    /**
     * Returns the hardcoded fallback marquee items shown when no CPT posts exist.
     *
     * @return string[]
     */
    private function fallback_marquee_items(): array
    {
        return [
            'Pod-Per-Site Isolation',
            "Let's Encrypt TLS",
            'Built-In Reverse Proxy',
            'WordPress · PHP · Node.js · .NET · Static',
            'phpMyAdmin Included',
            'Zero-Downtime SFTP',
            'Fail2Ban Integration',
            'WP-CLI Terminal',
            'IP & UA Security Rules',
            'MariaDB · Redis Per Site',
            'Open Source MIT',
            'TOTP Two-Factor Auth',
        ];
    }

    /**
     * Parses a newline-separated feature list into structured items.
     *
     * Lines prefixed with "x:" are marked unavailable; all others available.
     *
     * @param  string $raw Raw textarea value from post meta.
     * @return array<int, array{text: string, available: bool}>
     */
    private function parse_pricing_features(string $raw): array
    {
        $items = [];
        foreach (array_filter(array_map('trim', explode("\n", $raw))) as $line) {
            $unavailable = str_starts_with($line, 'x:');
            $items[] = [
                'text'      => $unavailable ? substr($line, 2) : $line,
                'available' => ! $unavailable,
            ];
        }
        return $items;
    }

    /**
     * Returns a styled "empty" paragraph for use when no posts exist yet.
     *
     * @param  string $message The message to display.
     * @return string HTML output.
     */
    private function empty_notice(string $message): string
    {
        return '<p class="pn-muted" style="padding:20px 0;font-style:italic;">' . esc_html($message) . '</p>';
    }
}
