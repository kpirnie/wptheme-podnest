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

defined('ABSPATH') || exit;

get_header();

// get the hero
get_template_part('template-parts/sections/hero');

// write out the marquee
echo podnest_render_block('podnest/marquee-strip');

// get the features
get_template_part('template-parts/sections/features');

// get the architecture
get_template_part('template-parts/sections/architecture');
?>




<!-- -- RUNTIMES — managed via PodNest Content → Runtimes --------------- -->
<section id="site-types" aria-labelledby="runtimes-heading">
    <div class="pn-container">
        <header class="pn-section-header pn-reveal">
            <span class="pn-eyebrow"><?php esc_html_e('Runtimes', 'podnest'); ?></span>
            <h2 id="runtimes-heading"><?php esc_html_e('Run Anything. Manage Everything.', 'podnest'); ?></h2>
            <p class="pn-section-desc">
                <?php esc_html_e('PodNest provisions purpose-built pods for every major server-side runtime. Pick your stack — the wiring is handled for you.', 'podnest'); ?>
            </p>
        </header>

        <?php echo podnest_render_block('podnest/runtimes-grid', ['columns' => 4]); ?>

        <?php
        $runtimes_pages = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'template-runtimes.php']);
        if (! empty($runtimes_pages)) :
        ?>
            <div style="text-align:center;margin-top:40px;" class="pn-reveal">
                <a href="<?php echo esc_url(get_permalink($runtimes_pages[0]->ID)); ?>" class="pn-btn-ghost">
                    <?php esc_html_e('Explore all runtimes', 'podnest'); ?> <span class="pn-arrow">→</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- -- SECURITY — static highlight cards ------------------------------- -->
<section id="security" aria-labelledby="security-heading" class="pn-section-alt">
    <div class="pn-container">
        <header class="pn-section-header pn-reveal">
            <span class="pn-eyebrow"><?php esc_html_e('Security', 'podnest'); ?></span>
            <h2 id="security-heading"><?php esc_html_e('Hardened at Every Layer.', 'podnest'); ?></h2>
        </header>
        <div class="pn-security-grid">
            <?php
            $security = [
                ['🔒', 'Pod Isolation',       'Each site is a sealed Podman pod. Containers in one pod cannot see traffic from another.'],
                ['🛡️', 'Fail2Ban Integration', 'Automatic IP banning for repeated auth failures, scan attempts, and bad-actor patterns.'],
                ['🌐', "Let's Encrypt TLS",   'Automatic certificate provisioning and renewal via Certbot — HTTP-01 and DNS-01 challenges.'],
                ['🔑', 'TOTP Two-Factor Auth', 'Time-based one-time passwords protect the management UI with zero external dependencies.'],
                ['🚫', 'IP & UA Rules',        'Block bad actors at the proxy layer using IP ranges, CIDR notation, and User-Agent patterns.'],
                ['📂', 'Zero-Trust SFTP',      'Per-site SFTP credentials scoped to that pod only — no shell access, no lateral movement.'],
            ];
            foreach ($security as $i => [$icon, $title, $desc]) :
                $delay = ($i % 3) + 1;
            ?>
                <article class="pn-security-card pn-reveal pn-reveal-delay-<?php echo $delay; ?>">
                    <span class="pn-security-icon" aria-hidden="true"><?php echo esc_html($icon); ?></span>
                    <h3><?php echo esc_html($title); ?></h3>
                    <p><?php echo esc_html($desc); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- -- PRICING — managed via PodNest Content → Pricing ----------------- -->
<section id="support" aria-labelledby="pricing-heading">
    <div class="pn-container">
        <header class="pn-section-header pn-reveal">
            <span class="pn-eyebrow"><?php esc_html_e('Paid Support', 'podnest'); ?></span>
            <h2 id="pricing-heading"><?php esc_html_e("PodNest is Free. Expert Help Isn't.", 'podnest'); ?></h2>
            <p class="pn-section-desc">
                <?php esc_html_e('The software is MIT-licensed and always will be. If you want hands-on setup, consulting, or priority support — that is where paid tiers come in.', 'podnest'); ?>
            </p>
        </header>

        <?php echo podnest_render_block('podnest/pricing-table'); ?>
    </div>
</section>

<!-- -- RECENT BLOG POSTS — hidden when no posts published yet ---------- -->
<?php
$recent_posts = get_posts([
    'posts_per_page'         => 3,
    'post_status'            => 'publish',
    'no_found_rows'          => true,
    'update_post_term_cache' => false,
]);

if (! empty($recent_posts)) :
?>
    <section id="blog" aria-labelledby="blog-heading" class="pn-section-alt">
        <div class="pn-container">
            <header class="pn-section-header pn-reveal">
                <span class="pn-eyebrow"><?php esc_html_e('From the Blog', 'podnest'); ?></span>
                <h2 id="blog-heading"><?php esc_html_e('Latest Posts', 'podnest'); ?></h2>
            </header>
            <div class="pn-blog-grid">
                <?php foreach ($recent_posts as $i => $bp) :
                    $thumb = get_the_post_thumbnail_url($bp->ID, 'podnest-thumb');
                ?>
                    <article class="pn-blog-card pn-reveal pn-reveal-delay-<?php echo $i + 1; ?>">
                        <?php if ($thumb) : ?>
                            <a href="<?php echo esc_url(get_permalink($bp->ID)); ?>" tabindex="-1" aria-hidden="true">
                                <img src="<?php echo esc_url($thumb); ?>"
                                    alt="" loading="lazy" width="400" height="250">
                            </a>
                        <?php endif; ?>
                        <div class="pn-blog-card-body">
                            <time datetime="<?php echo esc_attr(get_the_date('c', $bp->ID)); ?>" class="pn-blog-date">
                                <?php echo esc_html(get_the_date('', $bp->ID)); ?>
                            </time>
                            <h3><a href="<?php echo esc_url(get_permalink($bp->ID)); ?>"><?php echo esc_html(get_the_title($bp->ID)); ?></a></h3>
                            <p><?php echo esc_html(wp_trim_words(get_the_excerpt($bp), 20, '…')); ?></p>
                        </div>
                    </article>
                <?php endforeach;
                wp_reset_postdata(); ?>
            </div>
            <div style="text-align:center;margin-top:40px;" class="pn-reveal">
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/blog/')); ?>"
                    class="pn-btn-ghost">
                    <?php esc_html_e('All posts', 'podnest'); ?> <span class="pn-arrow">→</span>
                </a>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- -- CTA ------------------------------------------------------------- -->
<section id="cta" class="pn-cta-section" aria-labelledby="cta-heading">
    <div class="pn-container">
        <div class="pn-cta-inner pn-reveal">
            <span class="pn-eyebrow"><?php esc_html_e('Get Started', 'podnest'); ?></span>
            <h2 id="cta-heading"><?php esc_html_e('Five Minutes to Your First Pod.', 'podnest'); ?></h2>
            <p class="pn-lead">
                <?php esc_html_e('Provision and manage isolated, production-ready site pods from a single web-based management UI — no shell required after initial setup.', 'podnest'); ?>
            </p>
            <div class="pn-quickstart-methods">
                <div class="pn-quickstart-method">
                    <span class="pn-quickstart-icon">🐳</span>
                    <div class="pn-quickstart-content">
                        <span class="pn-quickstart-label"><?php esc_html_e('Container', 'podnest'); ?></span>
                        <span class="pn-quickstart-desc"><?php esc_html_e('Run the pre-built image directly with Podman. No compilation required.', 'podnest'); ?></span>
                    </div>
                </div>
                <div class="pn-quickstart-method">
                    <span class="pn-quickstart-icon">🗂️</span>
                    <div class="pn-quickstart-content">
                        <span class="pn-quickstart-label"><?php esc_html_e('Docker Compose', 'podnest'); ?></span>
                        <span class="pn-quickstart-desc"><?php esc_html_e('Use the included compose file for a one-command deployment with persistent volumes.', 'podnest'); ?></span>
                    </div>
                </div>
                <div class="pn-quickstart-method">
                    <span class="pn-quickstart-icon">⚙️</span>
                    <div class="pn-quickstart-content">
                        <span class="pn-quickstart-label"><?php esc_html_e('systemd', 'podnest'); ?></span>
                        <span class="pn-quickstart-desc"><?php esc_html_e('Run as a systemd service for production deployments — socket ordering and auto-restart handled for you.', 'podnest'); ?></span>
                    </div>
                </div>
                <div class="pn-quickstart-method">
                    <span class="pn-quickstart-icon">🔧</span>
                    <div class="pn-quickstart-content">
                        <span class="pn-quickstart-label"><?php esc_html_e('From Source', 'podnest'); ?></span>
                        <span class="pn-quickstart-desc"><?php esc_html_e('Build the Go binary directly. CGO required. Full control over the binary and deployment path.', 'podnest'); ?></span>
                    </div>
                </div>
            </div>
            <div class="pn-hero-ctas">
                <a href="<?php echo podnest_opt_url('social_github', 'https://github.com/kpirnie/podnest'); ?>"
                    class="pn-btn-primary" target="_blank" rel="noopener noreferrer">
                    <?php esc_html_e('View on GitHub', 'podnest'); ?>
                </a>
                <a href="<?php echo podnest_opt_url('support_contact_url', '#support'); ?>"
                    class="pn-btn-secondary">
                    <?php esc_html_e('Talk to the Author', 'podnest'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>