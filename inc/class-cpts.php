<?php

/**
 * Custom Post Type Registration
 *
 * Registers the four content types that drive PodNest's editable
 * homepage sections:
 *
 *  - podnest_marquee : Scrolling marquee callout items.
 *  - podnest_feature : Feature / capability cards.
 *  - podnest_runtime : Runtime / site-type cards.
 *  - podnest_pricing : Support pricing tier cards.
 *
 * Meta field registration (for REST / block attribute support) lives here
 * too, since it is tied to post type declarations.
 *
 * Meta box UI and save logic is in {@see PodNest_Meta_Boxes}.
 * Block render callbacks are in {@see PodNest_Blocks}.
 *
 * @package PodNest
 * @since   1.1.0
 */

defined('ABSPATH') || exit;

/**
 * Class PodNest_CPTs
 *
 * Self-registers all hooks in the constructor. Instantiated once by
 * {@see PodNest_Theme::boot_feature_classes()}.
 */
final class PodNest_CPTs
{

    // -- CPT slug constants ----------------------------------------

    /** Post type slug for marquee/streamer items. */
    public const MARQUEE = 'podnest_marquee';

    /** Post type slug for feature / capability cards. */
    public const FEATURE = 'podnest_feature';

    /** Post type slug for runtime / site-type cards. */
    public const RUNTIME = 'podnest_runtime';

    /** Post type slug for support pricing tiers. */
    public const PRICING = 'podnest_pricing';

    /** Post type slug for Instructions. */
    public const INSTRUCTION = 'podnest_instruction';


    // -- Constructor -----------------------------------------------

    /**
     * Registers WordPress hooks managed by this class.
     */
    public function __construct()
    {
        add_action('init',       [$this, 'register_post_types']);
        add_action('init',       [$this, 'register_meta_fields']);
        add_action('admin_menu', [$this, 'register_top_level_menu']);
    }

    // -- Post type registration ------------------------------------

    /**
     * Registers all four custom post types.
     *
     * Each CPT is defined via a private helper that returns the args array,
     * keeping this method a clean composition root.
     *
     * @return void
     */
    public function register_post_types(): void
    {
        register_post_type(self::MARQUEE,       $this->marquee_args());
        register_post_type(self::FEATURE,       $this->feature_args());
        register_post_type(self::RUNTIME,       $this->runtime_args());
        register_post_type(self::PRICING,       $this->pricing_args());
        register_post_type(self::INSTRUCTION,   $this->instruction_args());
    }

    /**
     * Returns the post type args for Marquee Items.
     *
     * Marquee items are not publicly accessible (no front-end URLs); they
     * exist solely to populate the scrolling strip on the front page.
     * Menu order (drag-sort) controls display order.
     *
     * @return array<string, mixed>
     */
    private function marquee_args(): array
    {
        return [
            'labels'        => $this->labels('Marquee Items', 'Marquee Item', 'Marquee'),
            'public'        => false,
            'show_ui'       => true,
            'show_in_menu'  => 'podnest-content',
            'show_in_rest'  => true,
            'supports'      => ['title', 'page-attributes'],
            'menu_icon'     => 'dashicons-arrow-right-alt',
            'rewrite'       => false,
            'has_archive'   => false,
        ];
    }

    /**
     * Returns the post type args for Features.
     *
     * Features are public so individual feature pages can be linked from
     * the features grid. The 'editor' support enables the block editor for
     * adding richer long-form descriptions.
     *
     * @return array<string, mixed>
     */
    private function feature_args(): array
    {
        return [
            'labels'        => $this->labels('Features', 'Feature', 'Features'),
            'public'        => false,
            'has_archive'   => false,
            'show_ui'       => true,
            'show_in_menu'  => 'podnest-content',
            'show_in_rest'  => true,
            'supports'      => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'],
            'rewrite'       => false,
            'menu_icon'     => 'dashicons-star-filled',
        ];
    }

    /**
     * Returns the post type args for Runtimes.
     *
     * Like Features, Runtimes are public with individual pages and a
     * block-editor-enabled long description. Version strings are stored
     * in post meta (see register_meta_fields).
     *
     * @return array<string, mixed>
     */
    private function runtime_args(): array
    {
        return [
            'labels'        => $this->labels('Runtimes', 'Runtime', 'Runtimes'),
            'public'        => false,
            'has_archive'   => false,
            'show_ui'       => true,
            'show_in_menu'  => 'podnest-content',
            'show_in_rest'  => true,
            'supports'      => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'],
            'rewrite'       => false,
            'menu_icon'     => 'dashicons-desktop',
        ];
    }

    /**
     * Returns the post type args for Pricing Tiers.
     *
     * Pricing is not public (no individual URLs) — all pricing is rendered
     * on the front page pricing section or via the pricing-table block.
     *
     * @return array<string, mixed>
     */
    private function pricing_args(): array
    {
        return [
            'labels'        => $this->labels('Pricing Tiers', 'Pricing Tier', 'Pricing'),
            'public'        => false,
            'show_ui'       => true,
            'show_in_menu'  => 'podnest-content',
            'show_in_rest'  => true,
            'supports'      => ['title', 'editor', 'page-attributes'],
            'rewrite'       => false,
            'has_archive'   => false,
            'menu_icon'     => 'dashicons-money-alt',
        ];
    }

    /**
     * Returns the post type args for Instructions.
     *
     * Features are public so individual feature pages can be linked from
     * the features grid. The 'editor' support enables the block editor for
     * adding richer long-form descriptions.
     *
     * @return array<string, mixed>
     */
    private function instruction_args(): array
    {
        return [
            'labels'            => $this->labels('Instructions', 'Instruction', 'Instructions'),
            'public'            => true,
            'has_archive'       => false,
            'show_ui'           => true,
            'show_in_menu'      => 'podnest-content',
            'show_in_rest'      => true,
            'supports'          => ['title', 'editor', 'thumbnail', 'excerpt',],
            'rewrite'           => ['slug' => 'support/instructions', 'with_front' => false],
            'menu_icon'         => 'dashicons-star-filled',
            'capability_type'   => 'page',

        ];
    }

    /**
     * Builds a minimal labels array for register_post_type().
     *
     * Only the labels that differ from WP defaults need to be set.
     * The rest fallback automatically (New, View, Search, etc.).
     *
     * @param  string $plural    Plural label, e.g. 'Marquee Items'.
     * @param  string $singular  Singular label, e.g. 'Marquee Item'.
     * @param  string $menu_name Short label used in the admin menu.
     * @return array<string, string>
     */
    private function labels(string $plural, string $singular, string $menu_name): array
    {
        return [
            'name'          => __($plural,   'podnest'),
            'singular_name' => __($singular, 'podnest'),
            'add_new_item'  => sprintf(__('Add %s', 'podnest'), $singular),
            'edit_item'     => sprintf(__('Edit %s', 'podnest'), $singular),
            'menu_name'     => __($menu_name, 'podnest'),
        ];
    }

    // -- Meta field registration -----------------------------------

    /**
     * Registers all custom meta fields with WordPress.
     *
     * Registering via register_post_meta() (rather than just using
     * add_meta_box + update_post_meta) exposes the values in the REST
     * API and block editor context, which enables future Gutenberg
     * attribute bindings without code changes.
     *
     * @return void
     */
    public function register_meta_fields(): void
    {
        $auth = static fn() => current_user_can('edit_posts');

        /* Shared: icon emoji and learn-more link (Feature + Runtime) */
        foreach ([self::FEATURE, self::RUNTIME] as $type) {
            register_post_meta($type, '_pn_icon', [
                'show_in_rest'  => true,
                'single'        => true,
                'type'          => 'string',
                'auth_callback' => $auth,
                'description'   => 'Emoji or short text icon shown on cards.',
            ]);
            register_post_meta($type, '_pn_learn_more_url', [
                'show_in_rest'  => true,
                'single'        => true,
                'type'          => 'string',
                'auth_callback' => $auth,
                'description'   => 'Optional URL for a learn-more CTA on the card.',
            ]);
        }

        /* Runtime-only: newline-separated version strings */
        register_post_meta(self::RUNTIME, '_pn_versions', [
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => 'string',
            'auth_callback' => $auth,
            'description'   => 'Newline-separated list of supported version labels.',
        ]);

        /* Pricing fields */
        $pricing_fields = [
            '_pn_price'         => ['type' => 'string',  'description' => 'Numeric price amount (no currency symbol).'],
            '_pn_price_unit'    => ['type' => 'string',  'description' => 'Unit label, e.g. "/ hour" or "/ month".'],
            '_pn_tier_label'    => ['type' => 'string',  'description' => 'Short tier name shown above the price.'],
            '_pn_badge_text'    => ['type' => 'string',  'description' => 'Optional badge (e.g. "Most Popular").'],
            '_pn_is_featured'   => ['type' => 'boolean', 'description' => 'Whether this tier is visually highlighted.'],
            '_pn_features_list' => ['type' => 'string',  'description' => 'Newline list. Prefix "x:" for unavailable items.'],
            '_pn_cta_text'      => ['type' => 'string',  'description' => 'CTA button label.'],
            '_pn_cta_url'       => ['type' => 'string',  'description' => 'CTA button target URL.'],
        ];

        foreach ($pricing_fields as $key => $args) {
            register_post_meta(self::PRICING, $key, array_merge($args, [
                'show_in_rest'  => true,
                'single'        => true,
                'auth_callback' => $auth,
            ]));
        }
    }

    // -- Admin menu ------------------------------------------------

    /**
     * Creates a top-level "PodNest Content" admin menu.
     *
     * All four CPTs are registered with show_in_menu = 'podnest-content',
     * so they appear as sub-items automatically. The top-level page itself
     * is a null callback — it just acts as a container.
     *
     * @return void
     */
    public function register_top_level_menu(): void
    {
        add_menu_page(
            __('PodNest Content', 'podnest'),
            __('PodNest Content', 'podnest'),
            'manage_options',
            'podnest-content',
            static fn() => null,
            'dashicons-layout',
            26
        );
    }
}
