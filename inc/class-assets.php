<?php

/**
 * Asset Management
 *
 * Responsible for:
 *  - Enqueuing frontend CSS and JS
 *  - Enqueuing block editor assets
 *  - Adding type="module" to ES module scripts
 *  - Registering widget sidebars
 *  - Preload / preconnect hints in <head>
 *  - Stripping WordPress junk from <head>
 *
 * @package PodNest
 * @since   1.1.0
 */

defined('ABSPATH') || exit;

/**
 * Class PodNest_Assets
 *
 * Self-registers all hooks in the constructor. Instantiated once by
 * {@see PodNest_Theme::boot_feature_classes()}.
 */
final class PodNest_Assets
{

    /**
     * Handles that should be loaded as ES modules (type="module").
     *
     * The script_loader_tag filter matches on these strings and adds
     * the type attribute before the closing >.
     *
     * @var string[]
     */
    private const MODULE_HANDLES = ['podnest-app'];

    /** True when the compiled IIFE bundle is loaded — skips type="module" injection */
    private bool $using_built_js = false;

    // ── Constructor ───────────────────────────────────────────────

    /**
     * Registers all WordPress hooks managed by this class.
     */
    public function __construct()
    {
        add_action('wp_enqueue_scripts',        [$this, 'enqueue_frontend']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor']);
        add_filter('script_loader_tag',          [$this, 'add_module_type'], 10, 3);
        add_action('wp_head',                    [$this, 'output_preload_hints'], 1);
        add_action('widgets_init',               [$this, 'register_sidebars']);
        add_action('init',                       [$this, 'cleanup_head_junk']);
        add_filter('the_generator',              '__return_empty_string');
    }

    // ── Frontend assets ───────────────────────────────────────────

    /**
     * Enqueues the main theme stylesheet and the ES module app entry point.
     *
     * The app.js is registered with an empty dependency array — module
     * loading order is handled by ES import statements within the file.
     * type="module" is injected via {@see add_module_type()}.
     *
     * @return void
     */
    public function enqueue_frontend(): void
    {
        $built_css = file_exists(PODNEST_DIR . '/assets/css/podnest.css');
        $this->using_built_js = file_exists(PODNEST_DIR . '/assets/js/podnest.js');

        wp_enqueue_style(
            'podnest-theme',
            PODNEST_URI . '/assets/css/' . ($built_css ? 'podnest.css' : 'theme.css'),
            [],
            PODNEST_VERSION
        );

        wp_enqueue_script(
            'podnest-app',
            PODNEST_URI . '/assets/js/' . ($this->using_built_js ? 'podnest.js' : 'app.js'),
            [],
            PODNEST_VERSION,
            true
        );
    }

    /**
     * Enqueues the block editor registration script.
     *
     * This is a traditional (non-module) script because it must interact
     * with the WP block editor globals (wp.blocks, wp.element, etc.) that
     * are already available as AMD-style globals in the editor context.
     *
     * @return void
     */
    public function enqueue_editor(): void
    {
        wp_enqueue_script(
            'podnest-blocks-editor',
            PODNEST_URI . '/assets/js/editor/podnest-editor.js',
            ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n'],
            PODNEST_VERSION,
            false
        );
    }

    // ── Script module type ────────────────────────────────────────

    /**
     * Injects type="module" into script tags for registered module handles.
     *
     * WordPress outputs <script src="…"> by default. ES modules require
     * type="module" to enable import/export syntax. This filter surgically
     * adds the attribute without breaking other scripts.
     *
     * @param  string $tag    Full <script> HTML tag.
     * @param  string $handle Script handle registered with wp_enqueue_script.
     * @param  string $src    Source URL (unused but required by filter signature).
     * @return string         Modified tag, or original if not a module handle.
     */
    public function add_module_type(string $tag, string $handle, string $src): string
    {
        /* IIFE bundle loaded — no module injection needed */
        if ($this->using_built_js) {
            return $tag;
        }
        return $tag;
    }

    // ── <head> performance hints ──────────────────────────────────

    /**
     * Outputs preconnect and preload hints early in <head>.
     *
     * Preconnecting to Google Fonts domains before the stylesheet loads
     * eliminates the extra DNS + TLS handshake round-trips, shaving ~100–300ms
     * off first-paint on cold connections.
     *
     * The stylesheet itself is loaded non-render-blocking via preload + onload,
     * with a <noscript> fallback for JS-disabled environments.
     *
     * @return void
     */
    public function output_preload_hints(): void
    {
        $fonts_url = 'https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap';
?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preload" as="style" href="<?php echo esc_url($fonts_url); ?>"
            onload="this.onload=null;this.rel='stylesheet'">
        <noscript>
            <link rel="stylesheet" href="<?php echo esc_url($fonts_url); ?>">
        </noscript>
<?php
    }

    // ── Widget sidebars ───────────────────────────────────────────

    /**
     * Registers all widget sidebars used by the theme.
     *
     * Footer columns are widget areas so site editors can drag in nav menus,
     * text widgets, recent posts, etc., without touching template files.
     * Shared wrapper markup is extracted to avoid repetition.
     *
     * @return void
     */
    public function register_sidebars(): void
    {
        /** Shared before/after widget wrapper — matches the footer card style. */
        $footer_wrap = [
            'before_widget' => '<div id="%1$s" class="widget %2$s pn-footer-widget-item">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="pn-footer-widget-title">',
            'after_title'   => '</h4>',
        ];

        register_sidebar(array_merge($footer_wrap, [
            'name'        => __('Footer — Product', 'podnest'),
            'id'          => 'footer-product',
            'description' => __('Widget area for the Product column in the footer.', 'podnest'),
        ]));

        register_sidebar(array_merge($footer_wrap, [
            'name'        => __('Footer — Resources', 'podnest'),
            'id'          => 'footer-resources',
            'description' => __('Widget area for the Resources column in the footer.', 'podnest'),
        ]));

        register_sidebar(array_merge($footer_wrap, [
            'name'        => __('Footer — Company', 'podnest'),
            'id'          => 'footer-company',
            'description' => __('Widget area for the Company column in the footer.', 'podnest'),
        ]));

        register_sidebar([
            'name'          => __('Blog Sidebar', 'podnest'),
            'id'            => 'sidebar-blog',
            'description'   => __('Appears in the sidebar on blog archive and single post pages.', 'podnest'),
            'before_widget' => '<div id="%1$s" class="widget %2$s pn-widget">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="pn-widget-title">',
            'after_title'   => '</h4>',
        ]);
    }

    // ── <head> cleanup ────────────────────────────────────────────

    /**
     * Removes WordPress-generated noise from the <head> that has no value
     * for a modern SaaS marketing site.
     *
     * Items removed and why:
     *  - wp_generator     → Exposes WP version; security hygiene.
     *  - wlwmanifest_link → Windows Live Writer link; nobody uses it.
     *  - rsd_link         → Really Simple Discovery; deprecated.
     *  - wp_shortlink     → Extra link tag; not needed here.
     *  - emoji detection  → ~12 KB of JS/CSS for a feature we don't use.
     *
     * @return void
     */
    public function cleanup_head_junk(): void
    {
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wp_shortlink_wp_head', 10);

        /* Strip emoji-related scripts and styles */
        remove_action('wp_head',             'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles',     'print_emoji_styles');
        remove_action('admin_print_styles',  'print_emoji_styles');
        remove_filter('the_content_feed',    'wp_staticize_emoji');
        remove_filter('comment_text_rss',    'wp_staticize_emoji');
        remove_filter('wp_mail',             'wp_staticize_emoji_for_email');

        /* Remove emoji from TinyMCE editor as well */
        add_filter('tiny_mce_plugins', static function (array $plugins): array {
            return array_diff($plugins, ['wpemoji']);
        });

        /* Remove emoji DNS prefetch hint from resource hints */
        add_filter('wp_resource_hints', static function (array $hints, string $type): array {
            if ('dns-prefetch' === $type) {
                return array_filter(
                    $hints,
                    static fn($r) => ! str_contains(($r['href'] ?? $r), 'emoji')
                );
            }
            return $hints;
        }, 10, 2);
    }
}
