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

defined('ABSPATH') || exit;

/**
 * Class PodNest_SEO
 *
 * Self-registers hooks in the constructor. Instantiated once by
 * {@see PodNest_Theme::boot_feature_classes()}.
 */
final class PodNest_SEO
{

    // -- Constructor -----------------------------------------------

    /**
     * Registers WordPress hooks for meta tags and structured data output.
     */
    public function __construct()
    {

        /* Structured data at priority 20, after meta tags */
        add_action('wp_head', [$this, 'output_structured_data'], 20);

        /* Fall back to the page excerpt when Yoast has no meta description */
        add_filter('wpseo_metadesc', [$this, 'yoast_metadesc_fallback']);

        /* Schema is owned by the theme — suppress Yoast's JSON-LD graph
           so we don't ship duplicate Organization nodes */
        add_filter('wpseo_json_ld_output', '__return_empty_array');
    }

    // -- Public output methods -------------------------------------

    /**
     * Outputs JSON-LD structured data appropriate to the current page type:
     *  - Front page  : SoftwareApplication + Organization schemas
     *  - Single post : Article schema
     *
     * @return void
     */
    public function output_structured_data(): void
    {

        if (is_front_page() || is_home()) {
            $this->output_schema($this->software_application_schema());
            $this->output_schema($this->organization_schema());
            return;
        }

        if (is_page()) {
            $this->output_schema($this->page_schema());
            return;
        }

        if (is_singular('podnest_instruction')) {
            $this->output_schema($this->instruction_schema());
        }
    }

    // -- Schema builders -------------------------------------------

    /**
     * Builds the Schema.org SoftwareApplication object for the front page.
     *
     * @return array<string, mixed>
     */
    private function software_application_schema(): array
    {
        /* Most-recent commit date drives dateModified (already cached) */
        $latest   = PodNest_Changelog::commits(1);
        $modified = ! empty($latest[0]['date']) ? $latest[0]['date'] : '';

        // setup the schema arrary
        $schema = [
            '@context'            => 'https://schema.org',
            '@type'               => 'SoftwareApplication',
            'name'                => 'PodNest',
            'applicationCategory' => 'DeveloperApplication',
            'operatingSystem'     => 'Linux',
            'url'                 => home_url('/'),
            'image'               => 'https://c.pdn.st/logos/podnest.svg',
            'screenshot'          => 'https://podnest.us/wp-content/uploads/2026/06/podnest-dashboard.png',
            'description'         => 'PodNest provisions and manages isolated, performant, hardened website pods using Podman — one server, many sites, zero shared fate.',
            'author'              => $this->person_schema(),
            'maintainer'          => $this->person_schema(),
            'publisher'           => $this->publisher_schema(),
            'softwareVersion'     => ltrim(podnest_latest_version(), 'v'),
            'license'             => 'https://opensource.org/licenses/MIT',
            'copyrightHolder'     => $this->person_schema(),
            'copyrightYear'       => '2026',
            'featureList'         => $this->feature_list(),
            'isAccessibleForFree' => true,
            'runtimePlatform'     => 'Go; Podman; JavaScript; HTML; CSS',
            'softwareRequirements' => 'Podman (rootless-capable); Linux host',
            'softwareHelp'        => home_url('/support/instructions/'),
            'downloadUrl'         => 'https://github.com/kpirnie/podnest/releases',
            'installUrl'          => 'https://github.com/kpirnie/podnest/releases',
            'releaseNotes'        => 'https://github.com/kpirnie/podnest/releases',
            'discussionUrl'       => 'https://github.com/kpirnie/podnest/issues',
            'keywords'            => 'self-hosted, Podman, container hosting, WordPress hosting, .Net hosting, Node.JS hosting, web hosting, reverse proxy, site management, rootless containers',
            'datePublished'       => '2026-05-08T12:00:00Z',
        ];

        // if we do have a real modified date...
        if ('' !== $modified) {
            $schema['dateModified'] = $modified;
        }

        return $schema;
    }

    /**
     * Builds the Schema.org Organization object.
     *
     * @return array<string, mixed>
     */
    private function organization_schema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => 'Kevin Pirnie',
            'slogan' => 'Expert WordPress, Development, & DevOps Solutions',
            'url'      => home_url('/'),
            'logo'     => [
                '@type' => 'ImageObject',
                'url'   => 'https://c.pdn.st/logos/podnest.svg',
            ],
            'founder'  => $this->toned_down_person_schema(),
            'address' => 'Feeding Hills, MA 01030 - United States',
            'email' => 'info@podne.st',
            'telephone' => '+1-405-757-4678 (757-HOST)',
            'sameAs'   => [
                'https://github.com/kpirnie',
                'https://kevinpirnie.com/',
            ],
        ];
    }

    /**
     * Builds the Schema.org WebPage object for a standard Page.
     *
     * @return array<string, mixed>
     */
    private function page_schema(): array
    {
        global $post;

        $image = has_post_thumbnail()
            ? get_the_post_thumbnail_url($post, 'podnest-hero')
            : 'https://c.pdn.st/pn/podnest-social-wall.jpg';

        return [
            '@context'      => 'https://schema.org',
            '@type'         => 'WebPage',
            'name'          => wp_strip_all_tags(get_the_title()),
            'description'   => wp_trim_words(wp_strip_all_tags(has_excerpt() ? get_the_excerpt() : get_the_content()), 30, '...'),
            'url'           => get_permalink(),
            'datePublished' => get_the_date('c'),
            'dateModified'  => get_the_modified_date('c'),
            'publisher'     => $this->publisher_schema(),
            'image'         => ['@type' => 'ImageObject', 'url' => $image],
            'isPartOf'      => ['@type' => 'WebSite', 'name' => get_bloginfo('name'), 'url' => home_url('/')],
        ];
    }

    /**
     * Builds the Schema.org TechArticle object for a single instruction.
     *
     * Instructions are documentation-style content, so TechArticle is a
     * better semantic fit than a generic WebPage.
     *
     * @return array<string, mixed>
     */
    private function instruction_schema(): array
    {
        global $post;

        $image = has_post_thumbnail()
            ? get_the_post_thumbnail_url($post, 'podnest-hero')
            : 'https://c.pdn.st/pn/podnest-social-wall.jpg';

        // get the author and publisher nodes
        $publisher = $this->publisher_schema();

        return [
            '@context'         => 'https://schema.org',
            '@type'            => 'TechArticle',
            'headline'         => wp_strip_all_tags(get_the_title()),
            'description'      => wp_trim_words(wp_strip_all_tags(has_excerpt() ? get_the_excerpt() : get_the_content()), 30, '...'),
            'url'              => get_permalink(),
            'datePublished'    => get_the_date('c'),
            'dateModified'     => get_the_modified_date('c'),
            'author'           => $this->toned_down_person_schema(),
            'publisher'        => $publisher,
            'image'            => ['@type' => 'ImageObject', 'url' => $image],
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => get_permalink()],
        ];
    }    

    // -- Shared schema fragments -----------------------------------

    /**
     * Returns a reusable Schema.org Person object for Kevin Pirnie.
     *
     * @return array<string, string>
     */
    private function person_schema(): array
    {
        return [
            '@type' => 'Person',
            'name'  => 'Kevin Pirnie',
            'givenName' => 'Kevin',
            'familyName' => 'Pirnie',
            'address' => 'Feeding Hills, MA 01030 - United States',
            'email' => 'iam@kevinpirnie.com',
            'telephone' => '+1-405-757-4678 (757-HOST)',
            'jobTitle' => 'DevOps Support Lead, WordPress/Hosting',
            'pronouns' => 'he/him/his',
            'url'   => 'https://kevinpirnie.com/',
            'image' => 'https://c.pdn.st/logos/kevinpirnie-logo-color.svg',
            'skills' => [
                'WordPress development',
                'WordPress theme development',
                'WordPress plugin development',
                'Gutenberg block development',
                'WP-CLI',
                'WordPress REST API',
                'PHP',
                'Go',
                'JavaScript',
                'SQL',
                'HTML',
                'CSS',
                'Bash',
                '.NET',
                'Linux server administration',
                'Windows Server administration',
                'Podman',
                'Docker',
                'rootless containers',
                'container hardening',
                'nginx',
                'reverse proxy configuration',
                'Varnish',
                'Redis',
                'MariaDB',
                'MySQL',
                'Microsoft SQL Server',
                'CI/CD',
                'GitHub Actions',
                'multi-architecture container builds',
                'Fail2Ban',
                'SFTP',
                'restic backups',
                'AWS',
                'Google Cloud Platform',
                'WP Engine',
                'Kinsta',
                'Cloudways',
                'Pantheon',
                'cPanel',
                'WHM',
                'ServerPilot',
                'LAMP',
                'LEMP',
                'IIS',
                'web application security',
                'Web Application Firewall',
                'OWASP Core Rule Set',
                'HTTP security headers',
                'Content Security Policy',
                'HSTS',
                'CSRF protection',
                'TLS',
                "Let's Encrypt",
                'vulnerability patching',
                'audit logging',
                'REST API design',
                'SOAP',
                'JSON-LD',
                'Schema.org structured data',
                'Model Context Protocol',
                'OAuth',
                'OIDC',
                'technical SEO',
                'AI agent readiness',
            ],
        ];
    }

    /**
     * Returns a reusable Schema.org Organization publisher fragment.
     *
     * @return array<string, mixed>
     */
    private function publisher_schema(): array
    {
        // get the existing Organization schema
        $org = $this->organization_schema();

        // remove the context and return the rest
        unset($org['@context']);
        return $org;
    }

    // -- Utilities -------------------------------------------------


    // toned down Person
    private function toned_down_person_schema(): array
    {
        // hold hte person
        $person = $this->person_schema();

        // unset some nodes for the author
        unset(
            $person['address'],
            $person['email'],
            $person['telephone'],
            $person['pronouns'],
            $person['skills']
        );

        return $person;
    }

    /**
     * Encodes a schema array as JSON-LD and echoes the <script> tag.
     *
     * @param  array<string, mixed> $schema Structured data to encode.
     * @return void
     */
    private function output_schema(array $schema): void
    {
        printf(
            "\n" . '<script type="application/ld+json">%s</script>' . "\n",
            wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * Supplies the page excerpt as the Yoast meta description when Yoast
     * has none set. Only fires for singular pages that have an excerpt.
     *
     * @param  string $desc The meta description resolved by Yoast.
     * @return string
     */
    public function yoast_metadesc_fallback(string $desc): string
    {
        if ('' === trim($desc) && is_singular('page') && has_excerpt()) {
            return mb_strimwidth(wp_strip_all_tags(get_the_excerpt()), 0, 160, '…');
        }
        return $desc;
    }

    /**
     * Builds featureList from published Features CPT titles (lowercased),
     * falling back to a static list when none are published so the node
     * is never emitted empty.
     *
     * @return string
     */
    private function feature_list(): string
    {
        $ids = get_posts([
            'post_type'   => PodNest_CPTs::FEATURE,
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby'     => 'menu_order title',
            'order'       => 'ASC',
            'fields'      => 'ids',
        ]);

        if (empty($ids)) {
            return 'pod-per-site isolation, nginx reverse proxy, let\'s encrypt tls, sftp access, phpmyadmin, wp-cli, mariadb, redis, fail2ban, ip/ua security rules, totp 2fa';
        }

        $names = array_map(
            static fn(int $id): string => strtolower(wp_strip_all_tags(get_the_title($id))),
            $ids
        );

        return implode(', ', $names);
    }
}
