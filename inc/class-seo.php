<?php
/**
 * SEO — Meta Tags & Structured Data
 *
 * Outputs Open Graph, Twitter Card, and JSON-LD Schema.org markup.
 * All output is suppressed when a dedicated SEO plugin (Yoast, AIOSEO,
 * RankMath) is active, because those plugins provide equivalent (and
 * richer) metadata — duplicating it would harm rather than help rankings.
 *
 * @package PodNest
 * @since   1.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class PodNest_SEO
 *
 * Self-registers hooks in the constructor. Instantiated once by
 * {@see PodNest_Theme::boot_feature_classes()}.
 */
final class PodNest_SEO {

    // ── Constructor ───────────────────────────────────────────────

    /**
     * Registers WordPress hooks for meta tags and structured data output.
     */
    public function __construct() {
        /* Meta tags at priority 2 so they land before most plugins */
        add_action( 'wp_head', [ $this, 'output_meta_tags' ], 2 );

        /* Structured data at priority 20, after meta tags */
        add_action( 'wp_head', [ $this, 'output_structured_data' ], 20 );
    }

    // ── Public output methods ─────────────────────────────────────

    /**
     * Outputs <meta> tags for description, robots, canonical, Open Graph,
     * and Twitter Card — but only when no dedicated SEO plugin is active.
     *
     * @return void
     */
    public function output_meta_tags(): void {
        if ( $this->seo_plugin_active() ) {
            return;
        }

        $meta = $this->resolve_page_meta();

        echo "\n<!-- PodNest SEO Meta -->\n";
        printf( '<meta name="description" content="%s">' . "\n",   esc_attr( $meta['description'] ) );
        printf( '<meta name="robots" content="index, follow, max-image-preview:large">' . "\n" );
        printf( '<link rel="canonical" href="%s">' . "\n",          esc_url( $meta['url'] ) );

        /* Open Graph */
        printf( '<meta property="og:type"         content="%s">' . "\n", esc_attr( $meta['type'] ) );
        printf( '<meta property="og:url"          content="%s">' . "\n", esc_url( $meta['url'] ) );
        printf( '<meta property="og:title"        content="%s">' . "\n", esc_attr( $meta['title'] ) );
        printf( '<meta property="og:description"  content="%s">' . "\n", esc_attr( $meta['description'] ) );
        printf( '<meta property="og:image"        content="%s">' . "\n", esc_url( $meta['image'] ) );
        printf( '<meta property="og:image:width"  content="1200">' . "\n" );
        printf( '<meta property="og:image:height" content="630">' . "\n" );
        printf( '<meta property="og:site_name"    content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
        printf( '<meta property="og:locale"       content="en_US">' . "\n" );

        /* Twitter / X Card */
        printf( '<meta name="twitter:card"        content="summary_large_image">' . "\n" );
        printf( '<meta name="twitter:title"       content="%s">' . "\n", esc_attr( $meta['title'] ) );
        printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $meta['description'] ) );
        printf( '<meta name="twitter:image"       content="%s">' . "\n", esc_url( $meta['image'] ) );

        echo "<!-- / PodNest SEO Meta -->\n\n";
    }

    /**
     * Outputs JSON-LD structured data appropriate to the current page type:
     *  - Front page  : SoftwareApplication + Organization schemas
     *  - Single post : Article schema
     *
     * @return void
     */
    public function output_structured_data(): void {
        if ( $this->seo_plugin_active() ) {
            return;
        }

        if ( is_front_page() || is_home() ) {
            $this->output_schema( $this->software_application_schema() );
            $this->output_schema( $this->organization_schema() );
            return;
        }

        if ( is_singular( 'post' ) ) {
            $this->output_schema( $this->article_schema() );
        }
    }

    // ── Schema builders ───────────────────────────────────────────

    /**
     * Builds the Schema.org SoftwareApplication object for the front page.
     *
     * @return array<string, mixed>
     */
    private function software_application_schema(): array {
        return [
            '@context'            => 'https://schema.org',
            '@type'               => 'SoftwareApplication',
            'name'                => 'PodNest',
            'applicationCategory' => 'DeveloperApplication',
            'operatingSystem'     => 'Linux',
            'url'                 => home_url( '/' ),
            'image'               => PODNEST_URI . '/assets/images/podnest-og.png',
            'description'         => 'PodNest provisions and manages isolated, production-hardened website pods using Podman. Manage WordPress, PHP, Node.js, .NET, and Static HTML from a single web UI.',
            'author'              => $this->person_schema(),
            'publisher'           => $this->publisher_schema(),
            'softwareVersion'     => PODNEST_VERSION,
            'license'             => 'https://opensource.org/licenses/MIT',
            'codeRepository'      => 'https://github.com/kpirnie/podnest',
            'offers'              => [
                '@type'         => 'Offer',
                'price'         => '0',
                'priceCurrency' => 'USD',
                'availability'  => 'https://schema.org/InStock',
            ],
            'featureList' => 'Pod-per-site isolation, Nginx reverse proxy, Let\'s Encrypt TLS, SFTP access, phpMyAdmin, WP-CLI, MariaDB, Redis, Fail2Ban, IP/UA security rules, TOTP 2FA',
        ];
    }

    /**
     * Builds the Schema.org Organization object.
     *
     * @return array<string, mixed>
     */
    private function organization_schema(): array {
        return [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => get_bloginfo( 'name' ),
            'url'      => home_url( '/' ),
            'logo'     => [
                '@type' => 'ImageObject',
                'url'   => PODNEST_URI . '/assets/images/podnest-og.png',
            ],
            'founder'  => $this->person_schema(),
            'sameAs'   => [
                'https://github.com/kpirnie',
                'https://kevinpirnie.com/',
            ],
        ];
    }

    /**
     * Builds the Schema.org Article object for the current single post.
     *
     * @return array<string, mixed>
     */
    private function article_schema(): array {
        global $post;

        $image = has_post_thumbnail()
            ? get_the_post_thumbnail_url( $post, 'podnest-hero' )
            : PODNEST_URI . '/assets/images/podnest-og.png';

        return [
            '@context'         => 'https://schema.org',
            '@type'            => 'Article',
            'headline'         => wp_strip_all_tags( get_the_title() ),
            'description'      => wp_trim_words( wp_strip_all_tags( get_the_excerpt() ?: get_the_content() ), 30, '…' ),
            'url'              => get_permalink(),
            'datePublished'    => get_the_date( 'c' ),
            'dateModified'     => get_the_modified_date( 'c' ),
            'author'           => [
                '@type' => 'Person',
                'name'  => get_the_author(),
                'url'   => get_author_posts_url( (int) get_the_author_meta( 'ID' ) ),
            ],
            'publisher'        => $this->publisher_schema(),
            'image'            => [ '@type' => 'ImageObject', 'url' => $image ],
            'mainEntityOfPage' => [ '@type' => 'WebPage', '@id' => get_permalink() ],
        ];
    }

    // ── Shared schema fragments ───────────────────────────────────

    /**
     * Returns a reusable Schema.org Person object for Kevin Pirnie.
     *
     * @return array<string, string>
     */
    private function person_schema(): array {
        return [
            '@type' => 'Person',
            'name'  => 'Kevin Pirnie',
            'url'   => 'https://kevinpirnie.com/',
        ];
    }

    /**
     * Returns a reusable Schema.org Organization publisher fragment.
     *
     * @return array<string, mixed>
     */
    private function publisher_schema(): array {
        return [
            '@type' => 'Organization',
            'name'  => get_bloginfo( 'name' ),
            'logo'  => [
                '@type' => 'ImageObject',
                'url'   => PODNEST_URI . '/assets/images/podnest-og.png',
            ],
        ];
    }

    // ── Utilities ─────────────────────────────────────────────────

    /**
     * Returns true when a known SEO plugin is active.
     *
     * Checked by class/constant existence rather than plugin file paths
     * so it works regardless of how or where the plugin is installed.
     *
     * @return bool
     */
    private function seo_plugin_active(): bool {
        return defined( 'WPSEO_VERSION' )    // Yoast SEO
            || defined( 'AIOSEO_VERSION' )   // All-in-One SEO
            || class_exists( 'RankMath' );   // RankMath
    }

    /**
     * Resolves and sanitises all per-page meta values into a flat array.
     *
     * Centralising this logic keeps output_meta_tags() readable and makes
     * the values easy to unit-test in isolation.
     *
     * @return array{title: string, description: string, url: string, image: string, type: string}
     */
    private function resolve_page_meta(): array {
        global $post;

        $site_name   = get_bloginfo( 'name' );
        $default_img = PODNEST_URI . '/assets/images/podnest-og.png';
        $default_desc = 'PodNest provisions and manages isolated, production-hardened website pods using Podman — no shell required after initial setup.';

        if ( is_singular() && ! empty( $post ) ) {
            $title = mb_strimwidth( wp_strip_all_tags( get_the_title() ), 0, 70, '…' );
            $desc  = mb_strimwidth(
                wp_strip_all_tags( has_excerpt( $post ) ? get_the_excerpt() : wp_trim_words( get_the_content(), 30 ) ),
                0, 160, '…'
            );
            return [
                'title'       => $title,
                'description' => $desc,
                'url'         => get_permalink(),
                'image'       => has_post_thumbnail() ? get_the_post_thumbnail_url( $post, 'podnest-hero' ) : $default_img,
                'type'        => 'article',
            ];
        }

        if ( is_front_page() || is_home() ) {
            return [
                'title'       => mb_strimwidth( $site_name . ' — Secure. Manage. Deploy.', 0, 70, '…' ),
                'description' => mb_strimwidth( get_bloginfo( 'description' ) ?: $default_desc, 0, 160, '…' ),
                'url'         => home_url( '/' ),
                'image'       => $default_img,
                'type'        => 'website',
            ];
        }

        /* Fallback for archives, search, etc. */
        $current_url = ( is_ssl() ? 'https' : 'http' ) . '://'
            . sanitize_text_field( $_SERVER['HTTP_HOST'] ?? '' )
            . sanitize_text_field( $_SERVER['REQUEST_URI'] ?? '/' );

        return [
            'title'       => mb_strimwidth( wp_title( '—', false, 'right' ) . $site_name, 0, 70, '…' ),
            'description' => mb_strimwidth( get_bloginfo( 'description' ) ?: $default_desc, 0, 160, '…' ),
            'url'         => esc_url_raw( $current_url ),
            'image'       => $default_img,
            'type'        => 'website',
        ];
    }

    /**
     * Encodes a schema array as JSON-LD and echoes the <script> tag.
     *
     * @param  array<string, mixed> $schema Structured data to encode.
     * @return void
     */
    private function output_schema( array $schema ): void {
        printf(
            '<script type="application/ld+json">%s</script>' . "\n",
            wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT )
        );
    }
}
