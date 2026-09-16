<?php

/**
 * Hero 
 * @package PodNest
 */

defined('ABSPATH') || exit;
?>

<!-- -- HERO ---------------------------------------------------------------- -->
<section id="hero" aria-labelledby="hero-heading">
    <div class="pn-hero-glow" aria-hidden="true"></div>
    <div class="pn-hero-grid-lines" aria-hidden="true"></div>

    <div class="pn-container">
        <div class="pn-hero-inner">

            <div class="pn-hero-content">
                <span class="pn-hero-badge">
                    <?php echo podnest_opt('hero_badge_text', 'Open Source &middot; MIT Licensed &middot; Production Ready'); ?>
                </span>
                <h1 class="pn-hero-title" id="hero-heading">
                    <?php echo esc_html(podnest_opt('hero_title_line1', 'Secure. Manage.')); ?><br>
                    <span class="pn-gradient-text">
                        <?php echo esc_html(podnest_opt('hero_title_line2', 'Deploy.')); ?>
                    </span>
                </h1>
                <p class="pn-hero-desc">
                    <?php echo esc_html(podnest_opt(
                        'hero_description',
                        'PodNest provisions and manages isolated, performant, hardened website pods using Podman — one server, many sites, zero shared fate.'
                    )); ?>
                </p>
                <div class="pn-hero-ctas">
                    <a href="<?php echo podnest_opt_url('hero_cta_primary_url', 'https://github.com/kpirnie/podnest'); ?>"
                        class="pn-btn-primary" target="_blank" rel="noopener noreferrer">
                        <?php echo esc_html(podnest_opt('hero_cta_primary', 'Get Started Free')); ?>
                    </a>
                    <a href="<?php echo podnest_opt_url('hero_cta_secondary_url', '#features'); ?>"
                        class="pn-btn-secondary">
                        <?php echo esc_html(podnest_opt('hero_cta_secondary', 'View Features')); ?>
                    </a>
                </div>
                <div class="pn-hero-stats" aria-label="<?php esc_attr_e('Key stats', 'podnest'); ?>">
                    <?php foreach ([['MIT', 'License'], ['Go', 'Language'], ['Podman', 'Runtime'], [podnest_latest_version(), 'Released']] as [$v, $l]) : ?>
                        <div class="pn-stat">
                            <span class="pn-stat-value"><?php echo esc_html($v); ?></span>
                            <span class="pn-stat-label"><?php echo esc_html($l); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div><!-- /.pn-hero-content -->

            <!-- Terminal — lines are injected by assets/js/modules/terminal.js -->
            <div class="pn-hero-terminal"
                aria-label="<?php esc_attr_e('PodNest terminal session example', 'podnest'); ?>"
                role="img">
                <div class="pn-terminal-header" aria-hidden="true">
                    <span class="pn-dot pn-dot--red"></span>
                    <span class="pn-dot pn-dot--yellow"></span>
                    <span class="pn-dot pn-dot--green"></span>
                    <span class="pn-terminal-title">podnest serve</span>
                </div>
                <div class="pn-terminal-body">
                    <div id="pn-terminal-output" aria-live="polite">
                    </div>
                </div>
            </div>

        </div><!-- /.pn-hero-inner -->
    </div>
</section>