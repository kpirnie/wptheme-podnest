<?php
/**
 * Global Template Helper Functions
 *
 * These procedural functions are called directly from template files
 * (header.php, footer.php, front-page.php, etc.) where instantiating a
 * class would be verbose. Each function is guarded with function_exists()
 * so child themes can override them safely.
 *
 * @package PodNest
 * @since   1.1.0
 */

defined( 'ABSPATH' ) || exit;

// ── Customizer option readers ─────────────────────────────────────────────────

if ( ! function_exists( 'podnest_opt' ) ) {
    /**
     * Retrieves a Customizer theme_mod value, HTML-escaped for safe output.
     *
     * @param  string $key      The mod key without the 'podnest_' prefix.
     * @param  string $fallback Default value if the mod is not set.
     * @return string           Escaped string, safe for direct echo.
     */
    function podnest_opt( string $key, string $fallback = '' ): string {
        return esc_html( get_theme_mod( 'podnest_' . $key, $fallback ) );
    }
}

if ( ! function_exists( 'podnest_opt_url' ) ) {
    /**
     * Retrieves a Customizer URL theme_mod value, URL-escaped for safe output.
     *
     * @param  string $key      The mod key without the 'podnest_' prefix.
     * @param  string $fallback Default URL if the mod is not set.
     * @return string           Escaped URL, safe for use in href/src attributes.
     */
    function podnest_opt_url( string $key, string $fallback = '' ): string {
        return esc_url( get_theme_mod( 'podnest_' . $key, $fallback ) );
    }
}

// ── Breadcrumbs ───────────────────────────────────────────────────────────────

if ( ! function_exists( 'podnest_breadcrumbs' ) ) {
    /**
     * Renders the breadcrumb navigation for the current page.
     *
     * Delegates to PodNest_Breadcrumbs which handles all the logic.
     * Not displayed on the front page.
     *
     * @return void
     */
    function podnest_breadcrumbs(): void {
        if ( is_front_page() ) {
            return;
        }
        ( new PodNest_Breadcrumbs() )->render();
    }
}

// ── Social links ──────────────────────────────────────────────────────────────

if ( ! function_exists( 'podnest_social_links' ) ) {
    /**
     * Returns an ordered array of social link definitions.
     *
     * Each entry contains:
     *  - label   : Screen-reader accessible label.
     *  - mod_key : Customizer key (without 'podnest_' prefix) for the URL.
     *  - default : Fallback URL when the Customizer value is not set.
     *  - icon    : Inline SVG markup for the platform icon.
     *
     * @return array<int, array{label: string, mod_key: string, default: string, icon: string}>
     */
    function podnest_social_links(): array {
        return [
            [
                'label'   => __( 'GitHub', 'podnest' ),
                'mod_key' => 'social_github',
                'default' => 'https://github.com/kpirnie/podnest',
                'icon'    => '<svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.3 3.44 9.8 8.2 11.38.6.11.82-.26.82-.58v-2.04c-3.34.73-4.04-1.6-4.04-1.6-.54-1.37-1.33-1.74-1.33-1.74-1.08-.74.08-.73.08-.73 1.2.09 1.83 1.23 1.83 1.23 1.07 1.83 2.8 1.3 3.48 1 .1-.78.42-1.3.76-1.6-2.67-.3-5.47-1.33-5.47-5.93 0-1.31.47-2.38 1.24-3.22-.13-.3-.54-1.52.12-3.18 0 0 1-.32 3.3 1.23a11.5 11.5 0 0 1 3-.4c1.02.01 2.04.14 3 .4 2.28-1.55 3.29-1.23 3.29-1.23.66 1.66.25 2.88.12 3.18.77.84 1.23 1.91 1.23 3.22 0 4.61-2.8 5.63-5.48 5.92.43.37.82 1.1.82 2.22v3.29c0 .32.22.7.83.58C20.56 21.8 24 17.3 24 12 24 5.37 18.63 0 12 0z"/></svg>',
            ],
            [
                'label'   => __( 'Discord', 'podnest' ),
                'mod_key' => 'social_discord',
                'default' => '',
                'icon'    => '<svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg>',
            ],
            [
                'label'   => __( 'X / Twitter', 'podnest' ),
                'mod_key' => 'social_twitter',
                'default' => '',
                'icon'    => '<svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
            ],
            [
                'label'   => __( 'Facebook', 'podnest' ),
                'mod_key' => 'social_facebook',
                'default' => '',
                'icon'    => '<svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.428c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>',
            ],
        ];
    }
}

// ── Logo helpers ──────────────────────────────────────────────────────────────

if ( ! function_exists( 'podnest_logo_img' ) ) {
    /**
     * Returns the img tag for the theme logo, either from custom-logo or fallback.
     *
     * @param  int    $size  Width/height in pixels for the fallback SVG.
     * @param  string $class Additional CSS class(es) to add to the img tag.
     * @return string         Escaped HTML img tag.
     */
    function podnest_logo_img( int $size = 64, string $class = '' ): string {
        if ( has_custom_logo() ) {
            /* get_custom_logo() already returns escaped HTML */
            return get_custom_logo();
        }

        return sprintf(
            '<img src="%s" alt="%s" width="%d" height="%d" class="%s" loading="eager">',
            esc_url( PODNEST_URI . '/assets/images/podnest-logo.svg' ),
            esc_attr( get_bloginfo( 'name' ) ),
            $size,
            $size,
            esc_attr( $class )
        );
    }
}

// ── Scroll-to-top button ──────────────────────────────────────────────────────

if ( ! function_exists( 'podnest_scroll_top_button' ) ) {
    /**
     * Outputs the scroll-to-top button markup.
     *
     * Button is hidden by default and shown/hidden via JS. Screen-reader
     * text is provided for accessibility. Placed just before </body>.
     *
     * @return void
     */
    function podnest_scroll_top_button(): void {
        ?>
        <button id="pn-scroll-top"
                class="pn-scroll-top"
                aria-label="<?php esc_attr_e( 'Scroll to top', 'podnest' ); ?>"
                title="<?php esc_attr_e( 'Scroll to top', 'podnest' ); ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true">
                <polyline points="18 15 12 9 6 15"></polyline>
            </svg>
            <span class="screen-reader-text"><?php esc_html_e( 'Scroll to top', 'podnest' ); ?></span>
        </button>
        <?php
    }
}

// ── Block rendering helper ─────────────────────────────────────────────────────

if ( ! function_exists( 'podnest_render_block' ) ) {
    /**
     * Renders a registered WordPress block programmatically from a template.
     *
     * Uses the core render_block() function rather than do_blocks() so the
     * block's render_callback receives the attributes array directly, avoiding
     * the cost of parsing serialised block comment delimiters.
     *
     * Usage in templates:
     *   <?php echo podnest_render_block( 'podnest/features-grid', [ 'columns' => 3 ] ); ?>
     *
     * @param  string               $block_name Fully-qualified block name, e.g. 'podnest/marquee-strip'.
     * @param  array<string, mixed> $attrs      Block attribute values (matched against registered schema).
     * @return string                           Rendered HTML; empty string if the block is not registered.
     */
    function podnest_render_block( string $block_name, array $attrs = [] ): string {
        return render_block( [
            'blockName'    => $block_name,
            'attrs'        => $attrs,
            'innerBlocks'  => [],
            'innerHTML'    => '',
            'innerContent' => [],
        ] );
    }
}
