<?php

/**
 * Security 
 * @package PodNest
 */

defined('ABSPATH') || exit;
?>
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