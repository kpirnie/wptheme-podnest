<?php

/**
 * Architecture
 * @package PodNest
 */

defined('ABSPATH') || exit;

$pods = get_posts([
    'post_type'      => PodNest_Architecture::POST_TYPE,
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order title',
    'order'          => 'ASC',
]);
?>
<!-- -- ARCHITECTURE — managed via PodNest Content → Architecture ------- -->
<section id="how-it-works" aria-labelledby="arch-heading" class="pn-section-alt">
    <div class="pn-container">
        <header class="pn-section-header pn-reveal">
            <span class="pn-eyebrow"><?php esc_html_e('Architecture', 'podnest'); ?></span>
            <h2 id="arch-heading"><?php esc_html_e('One Pod Per Site. Built for Isolation.', 'podnest'); ?></h2>
            <p class="pn-section-desc">
                <?php esc_html_e('Every website runs in its own isolated Podman pod, with dedicated containers, networking, and site-specific configurations. PodNest centralizes management, routing, and security while keeping individual workloads separated — reducing cross-site exposure and simplifying administration.', 'podnest'); ?>
            </p>
        </header>

        <div class="pn-arch-diagram pn-reveal pn-reveal-delay-2">
            <div class="pn-arch-row pn-arch-top">
                <div class="pn-arch-node pn-arch-internet">🌐 <?php esc_html_e('Internet', 'podnest'); ?></div>
            </div>
            <div class="pn-arch-arrow" aria-hidden="true">↓</div>
            <div class="pn-arch-row">
                <div class="pn-arch-node pn-arch-proxy">
                    <span class="pn-arch-icon">🔀</span>
                    <strong><?php esc_html_e('PodNest Reverse Proxy', 'podnest'); ?></strong>
                    <small><?php esc_html_e("Let's Encrypt TLS · HTTP/2", 'podnest'); ?></small>
                </div>
            </div>
            <div class="pn-arch-arrow" aria-hidden="true">↓</div>
            <?php if (! empty($pods)) : ?>
                <div class="pn-arch-row pn-arch-pods">
                    <?php foreach ($pods as $pod) :
                        $icon     = (string) get_post_meta($pod->ID, '_pn_icon', true);
                        $services = (string) get_post_meta($pod->ID, '_pn_services', true);
                    ?>
                        <div class="pn-arch-pod">
                            <div class="pn-arch-pod-header">
                                <?php if ($icon) : ?>
                                    <span><?php echo esc_html($icon); ?></span>
                                <?php endif; ?>
                                <strong><?php echo esc_html(get_the_title($pod)); ?></strong>
                            </div>
                            <div class="pn-arch-pod-services"><?php echo esc_html($services); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="pn-arch-row" style="margin-top: 32px;">
                <div class="pn-arch-node pn-arch-podnest">
                    <span class="pn-arch-icon">⚙️</span>
                    <strong><?php esc_html_e('PodNest Manager', 'podnest'); ?></strong>
                    <small><?php esc_html_e('Control Plane · Go · REST API · TOTP 2FA', 'podnest'); ?></small>
                </div>
            </div>
        </div>
    </div>
</section>
