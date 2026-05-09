<?php

/**
 * Main Theme Bootstrap
 *
 * Single entry point that declares theme support, registers nav menus,
 * image sizes, and instantiates every feature class. Uses the singleton
 * pattern so the constructor fires exactly once regardless of include order.
 *
 * @package PodNest
 * @since   1.1.0
 */

defined('ABSPATH') || exit;

/**
 * Class PodNest_Theme
 *
 * Orchestrates theme setup. Feature classes are injected by functions.php
 * before this singleton is booted, so by the time after_setup_theme fires
 * all dependencies are already loaded.
 */
final class PodNest_Theme
{

    // -- Singleton ------------------------------------------------

    /** @var self|null Singleton instance. */
    private static ?self $instance = null;

    /**
     * Returns (and lazily creates) the singleton instance.
     *
     * Called once from functions.php after all classes are loaded.
     *
     * @return self
     */
    public static function instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /** Private constructor — use {@see PodNest_Theme::instance()} instead. */
    private function __construct()
    {
        $this->register_hooks();
        $this->boot_feature_classes();
    }

    /** Prevent external cloning of the singleton. */
    private function __clone() {}

    // -- Hook registration ----------------------------------------

    /**
     * Registers WordPress hooks owned by this class.
     *
     * Image sizes and nav menus are registered on after_setup_theme so
     * WordPress has already loaded the default image sizes before we add ours.
     *
     * @return void
     */
    private function register_hooks(): void
    {
        add_action('after_setup_theme', [$this, 'setup_theme_support']);
        add_action('after_setup_theme', [$this, 'register_image_sizes']);
        add_action('after_setup_theme', [$this, 'register_nav_menus']);
        add_filter('excerpt_length',    [$this, 'excerpt_length']);
        add_filter('excerpt_more',      [$this, 'excerpt_more']);
        add_filter('use_block_editor_for_post', [$this, 'disable_gutenberg_on_front_page'], 10, 2);

        /* Remove the WP admin toolbar from all front-end views */
        add_filter('show_admin_bar', '__return_false');

        // remove comments
        add_filter('comments_open',   '__return_false', 20, 2);
        add_filter('pings_open',      '__return_false', 20, 2);
        add_filter('comments_array',  '__return_empty_array', 10, 2);
    }

    // -- Feature class bootstrapping -------------------------------

    /**
     * Instantiates each feature class.
     *
     * Every class self-registers its own hooks in its constructor, so simply
     * calling `new` is enough to wire it into WordPress.
     *
     * @return void
     */
    private function boot_feature_classes(): void
    {
        new PodNest_Assets();
        new PodNest_SEO();
        new PodNest_CPTs();
        new PodNest_Meta_Boxes();
        new PodNest_Blocks();
        new PodNest_Customizer();
    }

    // -- Theme support --------------------------------------------

    /**
     * Declares WordPress theme feature support.
     *
     * Fires on the after_setup_theme action so it runs after WP core
     * has initialised its own features.
     *
     * @return void
     */
    public function setup_theme_support(): void
    {
        load_theme_textdomain('podnest', PODNEST_DIR . '/languages');

        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('automatic-feed-links');
        add_theme_support('wp-block-styles');
        add_theme_support('align-wide');
        add_theme_support('editor-styles');
        add_theme_support('customize-selective-refresh-widgets');

        add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'script',
            'style',
        ]);

        add_theme_support('custom-logo', [
            'height'      => 64,
            'width'       => 64,
            'flex-height' => true,
            'flex-width'  => true,
        ]);

        /* Disable comments on all post types */
        add_action('init', static function () {
            foreach (get_post_types() as $type) {
                remove_post_type_support($type, 'comments');
                remove_post_type_support($type, 'trackbacks');
            }
        });

        // Global content width used by WordPress media embeds.
        if (! isset($GLOBALS['content_width'])) {
            $GLOBALS['content_width'] = 1200;
        }
    }

    /**
     * Registers custom image sizes used by the theme.
     *
     * Named sizes are referenced in get_the_post_thumbnail_url() calls
     * throughout templates so they're centralised here.
     *
     * @return void
     */
    public function register_image_sizes(): void
    {
        add_image_size('podnest-hero',    1600, 900,  true);
        add_image_size('podnest-feature', 800,  500,  true);
        add_image_size('podnest-thumb',   400,  250,  true);
        add_image_size('podnest-square',  600,  600,  true);
    }

    /**
     * Registers the nav menu locations available to this theme.
     *
     * Only the primary menu is registered; footer columns are handled
     * by widget sidebars (see PodNest_Assets::register_sidebars()).
     *
     * @return void
     */
    public function register_nav_menus(): void
    {
        register_nav_menus([
            'primary' => __('Primary Navigation', 'podnest'),
            /* Social Links — PodNest_Social_Walker maps each URL to an SVG icon */
            'social'  => __('Social Links', 'podnest'),
        ]);
    }

    // -- Excerpt filters ------------------------------------------

    /**
     * Sets the auto-excerpt word count to 28 words.
     *
     * @param  int $length Default WordPress excerpt length.
     * @return int
     */
    public function excerpt_length(int $length): int
    {
        return 28;
    }

    /**
     * Replaces the default "[…]" excerpt suffix with a styled ellipsis.
     *
     * @param  string $more Current excerpt suffix.
     * @return string
     */
    public function excerpt_more(string $more): string
    {
        return ' <span class="pn-muted">…</span>';
    }

    // -- Editor ---------------------------------------------------

    /**
     * Disables the block editor on pages that use the front-page.php template.
     *
     * The front page is built with PHP/CPT-driven sections and does not need
     * the block editor. Disabling it avoids confusing editor states.
     *
     * @param  bool    $use  Whether to use the block editor.
     * @param  WP_Post $post The post being edited.
     * @return bool
     */
    public function disable_gutenberg_on_front_page(bool $use, WP_Post $post): bool
    {
        if ('front-page.php' === get_page_template_slug($post)) {
            return false;
        }
        return $use;
    }
}
