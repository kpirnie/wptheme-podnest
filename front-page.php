<?php
/**
 * Front Page — Marketing Homepage
 *
 * All dynamic sections (marquee, features, runtimes, pricing) are rendered
 * via {@see podnest_render_block()}, which calls the server-side render
 * callbacks in {@see PodNest_Blocks}. Content is managed entirely from
 * wp-admin under PodNest Content — no template edits required.
 *
 * Static sections (hero, architecture, security, CTA) pull copy from the
 * Customizer via {@see podnest_opt()} / {@see podnest_opt_url()}.
 *
 * @package PodNest
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<!-- ── HERO ──────────────────────────────────────────────────────────────── -->
<section id="hero" aria-labelledby="hero-heading">
    <div class="pn-hero-glow" aria-hidden="true"></div>
    <div class="pn-hero-grid-lines" aria-hidden="true"></div>

    <div class="pn-container">
        <div class="pn-hero-inner">

            <div class="pn-hero-content">
                <span class="pn-hero-badge">
                    <?php echo podnest_opt( 'hero_badge_text', 'Open Source &middot; MIT License' ); ?>
                </span>
                <h1 class="pn-hero-title" id="hero-heading">
                    <?php echo esc_html( podnest_opt( 'hero_title_line1', 'Secure. Manage.' ) ); ?><br>
                    <span class="pn-gradient-text">
                        <?php echo esc_html( podnest_opt( 'hero_title_line2', 'Deploy.' ) ); ?>
                    </span>
                </h1>
                <p class="pn-hero-desc">
                    <?php echo esc_html( podnest_opt( 'hero_description',
                        'PodNest provisions and manages isolated, production-hardened website pods using Podman — no shell required after initial setup.'
                    ) ); ?>
                </p>
                <div class="pn-hero-ctas">
                    <a href="<?php echo podnest_opt_url( 'hero_cta_primary_url', 'https://github.com/kpirnie/podnest' ); ?>"
                       class="pn-btn-primary" target="_blank" rel="noopener noreferrer">
                        <?php echo esc_html( podnest_opt( 'hero_cta_primary', 'Get Started Free' ) ); ?>
                    </a>
                    <a href="<?php echo podnest_opt_url( 'hero_cta_secondary_url', '#features' ); ?>"
                       class="pn-btn-secondary">
                        <?php echo esc_html( podnest_opt( 'hero_cta_secondary', 'View Features' ) ); ?>
                    </a>
                </div>
                <div class="pn-hero-stats" aria-label="<?php esc_attr_e( 'Key stats', 'podnest' ); ?>">
                    <?php foreach ( [ ['MIT','License'], ['Go','Language'], ['Podman','Runtime'], ['v1.0','Released'] ] as [ $v, $l ] ) : ?>
                    <div class="pn-stat">
                        <span class="pn-stat-value"><?php echo esc_html( $v ); ?></span>
                        <span class="pn-stat-label"><?php echo esc_html( $l ); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div><!-- /.pn-hero-content -->

            <!-- Terminal — lines are injected by assets/js/modules/terminal.js -->
            <div class="pn-hero-terminal"
                 aria-label="<?php esc_attr_e( 'PodNest terminal session example', 'podnest' ); ?>"
                 role="img">
                <div class="pn-terminal-header" aria-hidden="true">
                    <span class="pn-dot pn-dot--red"></span>
                    <span class="pn-dot pn-dot--yellow"></span>
                    <span class="pn-dot pn-dot--green"></span>
                    <span class="pn-terminal-title">podnest serve</span>
                </div>
                <div class="pn-terminal-body">
                    <div id="pn-terminal-output" aria-live="polite">
                        <div class="pn-term-cursor" aria-hidden="true">▋</div>
                    </div>
                </div>
            </div>

        </div><!-- /.pn-hero-inner -->
    </div>
</section>

<!-- ── MARQUEE STRIP — managed via PodNest Content → Marquee ─────────── -->
<?php echo podnest_render_block( 'podnest/marquee-strip' ); ?>

<!-- ── FEATURES — managed via PodNest Content → Features ─────────────── -->
<section id="features" aria-labelledby="features-heading">
    <div class="pn-container">
        <header class="pn-section-header pn-reveal">
            <span class="pn-eyebrow"><?php esc_html_e( 'Capabilities', 'podnest' ); ?></span>
            <h2 id="features-heading"><?php esc_html_e( 'Everything You Need. Nothing You Don\'t.', 'podnest' ); ?></h2>
            <p class="pn-section-desc">
                <?php esc_html_e( 'PodNest ships with enterprise-grade tools configured and wired together out of the box. From provisioning to monitoring — covered.', 'podnest' ); ?>
            </p>
        </header>

        <?php echo podnest_render_block( 'podnest/features-grid', [ 'columns' => 3 ] ); ?>

        <?php
        /* Link to full features page if template-features.php is assigned */
        $features_pages = get_pages( [ 'meta_key' => '_wp_page_template', 'meta_value' => 'template-features.php' ] );
        if ( ! empty( $features_pages ) ) :
        ?>
        <div style="text-align:center;margin-top:40px;" class="pn-reveal">
            <a href="<?php echo esc_url( get_permalink( $features_pages[0]->ID ) ); ?>" class="pn-btn-ghost">
                <?php esc_html_e( 'All capabilities', 'podnest' ); ?> <span class="pn-arrow">→</span>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ── ARCHITECTURE — static illustrative diagram ───────────────────── -->
<section id="how-it-works" aria-labelledby="arch-heading" class="pn-section-alt">
    <div class="pn-container">
        <header class="pn-section-header pn-reveal">
            <span class="pn-eyebrow"><?php esc_html_e( 'Architecture', 'podnest' ); ?></span>
            <h2 id="arch-heading"><?php esc_html_e( 'One Pod Per Site. Total Isolation.', 'podnest' ); ?></h2>
            <p class="pn-section-desc">
                <?php esc_html_e( 'Every website runs inside its own Podman pod — a sealed group of containers sharing one network namespace. A breach in one site cannot touch any other.', 'podnest' ); ?>
            </p>
        </header>

        <div class="pn-arch-diagram pn-reveal pn-reveal-delay-2">
            <div class="pn-arch-row pn-arch-top">
                <div class="pn-arch-node pn-arch-internet">🌐 <?php esc_html_e( 'Internet', 'podnest' ); ?></div>
            </div>
            <div class="pn-arch-arrow" aria-hidden="true">↓</div>
            <div class="pn-arch-row">
                <div class="pn-arch-node pn-arch-proxy">
                    <span class="pn-arch-icon">🔀</span>
                    <strong><?php esc_html_e( 'Nginx Reverse Proxy', 'podnest' ); ?></strong>
                    <small><?php esc_html_e( "Let's Encrypt TLS · HTTP/2", 'podnest' ); ?></small>
                </div>
            </div>
            <div class="pn-arch-arrow" aria-hidden="true">↓</div>
            <div class="pn-arch-row">
                <div class="pn-arch-node pn-arch-podnest">
                    <span class="pn-arch-icon">⚙️</span>
                    <strong><?php esc_html_e( 'PodNest Manager', 'podnest' ); ?></strong>
                    <small><?php esc_html_e( 'Go · REST API · TOTP 2FA', 'podnest' ); ?></small>
                </div>
            </div>
            <div class="pn-arch-arrow" aria-hidden="true">↓</div>
            <div class="pn-arch-row pn-arch-pods">
                <?php foreach ( [
                    [ '🅆', 'WordPress', 'PHP-FPM · MariaDB · Redis · Nginx' ],
                    [ '⬡', 'Node.js',   'Node · MariaDB · Nginx'             ],
                    [ '⬛', '.NET / Static', 'Kestrel / Nginx'               ],
                ] as [ $icon, $label, $services ] ) : ?>
                <div class="pn-arch-pod">
                    <div class="pn-arch-pod-header">
                        <span><?php echo esc_html( $icon ); ?></span>
                        <strong><?php echo esc_html( $label ); ?></strong>
                    </div>
                    <div class="pn-arch-pod-services"><?php echo esc_html( $services ); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ── RUNTIMES — managed via PodNest Content → Runtimes ─────────────── -->
<section id="site-types" aria-labelledby="runtimes-heading">
    <div class="pn-container">
        <header class="pn-section-header pn-reveal">
            <span class="pn-eyebrow"><?php esc_html_e( 'Runtimes', 'podnest' ); ?></span>
            <h2 id="runtimes-heading"><?php esc_html_e( 'Run Anything. Manage Everything.', 'podnest' ); ?></h2>
            <p class="pn-section-desc">
                <?php esc_html_e( 'PodNest provisions purpose-built pods for every major server-side runtime. Pick your stack — the wiring is handled for you.', 'podnest' ); ?>
            </p>
        </header>

        <?php echo podnest_render_block( 'podnest/runtimes-grid', [ 'columns' => 4 ] ); ?>

        <?php
        $runtimes_pages = get_pages( [ 'meta_key' => '_wp_page_template', 'meta_value' => 'template-runtimes.php' ] );
        if ( ! empty( $runtimes_pages ) ) :
        ?>
        <div style="text-align:center;margin-top:40px;" class="pn-reveal">
            <a href="<?php echo esc_url( get_permalink( $runtimes_pages[0]->ID ) ); ?>" class="pn-btn-ghost">
                <?php esc_html_e( 'Explore all runtimes', 'podnest' ); ?> <span class="pn-arrow">→</span>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ── SECURITY — static highlight cards ─────────────────────────────── -->
<section id="security" aria-labelledby="security-heading" class="pn-section-alt">
    <div class="pn-container">
        <header class="pn-section-header pn-reveal">
            <span class="pn-eyebrow"><?php esc_html_e( 'Security', 'podnest' ); ?></span>
            <h2 id="security-heading"><?php esc_html_e( 'Hardened at Every Layer.', 'podnest' ); ?></h2>
        </header>
        <div class="pn-security-grid">
            <?php
            $security = [
                [ '🔒', 'Pod Isolation',       'Each site is a sealed Podman pod. Containers in one pod cannot see traffic from another.'                         ],
                [ '🛡️', 'Fail2Ban Integration', 'Automatic IP banning for repeated auth failures, scan attempts, and bad-actor patterns.'                          ],
                [ '🌐', "Let's Encrypt TLS",   'Automatic certificate provisioning and renewal via Certbot — HTTP-01 and DNS-01 challenges.'                      ],
                [ '🔑', 'TOTP Two-Factor Auth', 'Time-based one-time passwords protect the management UI with zero external dependencies.'                         ],
                [ '🚫', 'IP & UA Rules',        'Block bad actors at the proxy layer using IP ranges, CIDR notation, and User-Agent patterns.'                     ],
                [ '📂', 'Zero-Trust SFTP',      'Per-site SFTP credentials scoped to that pod only — no shell access, no lateral movement.'                       ],
            ];
            foreach ( $security as $i => [ $icon, $title, $desc ] ) :
                $delay = ( $i % 3 ) + 1;
            ?>
            <article class="pn-security-card pn-reveal pn-reveal-delay-<?php echo $delay; ?>">
                <span class="pn-security-icon" aria-hidden="true"><?php echo esc_html( $icon ); ?></span>
                <h3><?php echo esc_html( $title ); ?></h3>
                <p><?php echo esc_html( $desc ); ?></p>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── PRICING — managed via PodNest Content → Pricing ───────────────── -->
<section id="support" aria-labelledby="pricing-heading">
    <div class="pn-container">
        <header class="pn-section-header pn-reveal">
            <span class="pn-eyebrow"><?php esc_html_e( 'Paid Support', 'podnest' ); ?></span>
            <h2 id="pricing-heading"><?php esc_html_e( "PodNest is Free. Expert Help Isn't.", 'podnest' ); ?></h2>
            <p class="pn-section-desc">
                <?php esc_html_e( 'The software is MIT-licensed and always will be. If you want hands-on setup, consulting, or priority support — that is where paid tiers come in.', 'podnest' ); ?>
            </p>
        </header>

        <?php echo podnest_render_block( 'podnest/pricing-table' ); ?>
    </div>
</section>

<!-- ── RECENT BLOG POSTS — hidden when no posts published yet ────────── -->
<?php
$recent_posts = get_posts( [
    'posts_per_page'         => 3,
    'post_status'            => 'publish',
    'no_found_rows'          => true,
    'update_post_term_cache' => false,
] );

if ( ! empty( $recent_posts ) ) :
?>
<section id="blog" aria-labelledby="blog-heading" class="pn-section-alt">
    <div class="pn-container">
        <header class="pn-section-header pn-reveal">
            <span class="pn-eyebrow"><?php esc_html_e( 'From the Blog', 'podnest' ); ?></span>
            <h2 id="blog-heading"><?php esc_html_e( 'Latest Posts', 'podnest' ); ?></h2>
        </header>
        <div class="pn-blog-grid">
            <?php foreach ( $recent_posts as $i => $bp ) :
                $thumb = get_the_post_thumbnail_url( $bp->ID, 'podnest-thumb' );
            ?>
            <article class="pn-blog-card pn-reveal pn-reveal-delay-<?php echo $i + 1; ?>">
                <?php if ( $thumb ) : ?>
                <a href="<?php echo esc_url( get_permalink( $bp->ID ) ); ?>" tabindex="-1" aria-hidden="true">
                    <img src="<?php echo esc_url( $thumb ); ?>"
                         alt="" loading="lazy" width="400" height="250">
                </a>
                <?php endif; ?>
                <div class="pn-blog-card-body">
                    <time datetime="<?php echo esc_attr( get_the_date( 'c', $bp->ID ) ); ?>" class="pn-blog-date">
                        <?php echo esc_html( get_the_date( '', $bp->ID ) ); ?>
                    </time>
                    <h3><a href="<?php echo esc_url( get_permalink( $bp->ID ) ); ?>"><?php echo esc_html( get_the_title( $bp->ID ) ); ?></a></h3>
                    <p><?php echo esc_html( wp_trim_words( get_the_excerpt( $bp ), 20, '…' ) ); ?></p>
                </div>
            </article>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
        <div style="text-align:center;margin-top:40px;" class="pn-reveal">
            <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/blog/' ) ); ?>"
               class="pn-btn-ghost">
                <?php esc_html_e( 'All posts', 'podnest' ); ?> <span class="pn-arrow">→</span>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── CTA ───────────────────────────────────────────────────────────── -->
<section id="cta" class="pn-cta-section" aria-labelledby="cta-heading">
    <div class="pn-container">
        <div class="pn-cta-inner pn-reveal">
            <span class="pn-eyebrow" style="color:var(--pn-cyan);"><?php esc_html_e( 'Get Started', 'podnest' ); ?></span>
            <h2 id="cta-heading"><?php esc_html_e( 'Five Minutes to Your First Pod.', 'podnest' ); ?></h2>
            <p class="pn-lead" style="max-width:600px;margin:0 auto 36px;">
                <?php esc_html_e( 'Pull the container image, run the binary, open the UI. No shell scripting. No tangled configs.', 'podnest' ); ?>
            </p>
            <div class="pn-quickstart">
                <code>podman pull ghcr.io/kpirnie/podnest:latest &amp;&amp; podnest serve</code>
            </div>
            <div class="pn-hero-ctas" style="justify-content:center;margin-top:32px;">
                <a href="<?php echo podnest_opt_url( 'social_github', 'https://github.com/kpirnie/podnest' ); ?>"
                   class="pn-btn-primary" target="_blank" rel="noopener noreferrer">
                    <?php esc_html_e( 'View on GitHub', 'podnest' ); ?>
                </a>
                <a href="<?php echo podnest_opt_url( 'support_contact_url', '#support' ); ?>"
                   class="pn-btn-secondary">
                    <?php esc_html_e( 'Talk to the Author', 'podnest' ); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
