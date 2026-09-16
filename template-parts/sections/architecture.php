<?php

/**
 * Architecture 
 * @package PodNest
 */

defined('ABSPATH') || exit;
?>
<!-- -- ARCHITECTURE — static illustrative diagram --------------------- -->
<section id="how-it-works" aria-labelledby="arch-heading" class="pn-section-alt">
    <div class="pn-container">
        <header class="pn-section-header pn-reveal">
            <span class="pn-eyebrow"><?php esc_html_e('Architecture', 'podnest'); ?></span>
            <h2 id="arch-heading"><?php esc_html_e('One Pod Per Site. Total Isolation.', 'podnest'); ?></h2>
            <p class="pn-section-desc">
                <?php esc_html_e('Every website runs inside its own Podman pod — a sealed group of containers sharing one network namespace. A breach in one site cannot touch any other.', 'podnest'); ?>
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
            <div class="pn-arch-row">
                <div class="pn-arch-node pn-arch-podnest">
                    <span class="pn-arch-icon">⚙️</span>
                    <strong><?php esc_html_e('PodNest Manager', 'podnest'); ?></strong>
                    <small><?php esc_html_e('Go · REST API · TOTP 2FA', 'podnest'); ?></small>
                </div>
            </div>
            <div class="pn-arch-arrow" aria-hidden="true">↓</div>
            <div class="pn-arch-row pn-arch-pods">
                <?php foreach (
                    [
                        ['🅆', 'WordPress / PHP', 'PHP · MariaDB · Redis · Nginx'],
                        ['⬡', 'Node.js',   'Node · MariaDB · Redis · Nginx'],
                        ['⬛', '.NET / Static', '.Net · MariaDB · Redis · Nginx'],
                    ] as [$icon, $label, $services]
                ) : ?>
                    <div class="pn-arch-pod">
                        <div class="pn-arch-pod-header">
                            <span><?php echo esc_html($icon); ?></span>
                            <strong><?php echo esc_html($label); ?></strong>
                        </div>
                        <div class="pn-arch-pod-services"><?php echo esc_html($services); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>