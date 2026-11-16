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
                ['🔒', 'Pod Isolation',       'Each site runs in an isolated Podman pod with dedicated containers and networking, reducing cross-site exposure.'],
                ['🛡️', 'Fail2Ban Integration', 'Automatically ban malicious IPs after repeated login failures, suspicious requests, and abusive traffic.'],
                ['🌐', "Let's Encrypt TLS" 'Automatic SSL certificate issuance and renewal, with HTTP-01, TLS-ALPN-01, and self-signed fallback.'],
                ['🔑', 'TOTP Two-Factor Auth', 'Secure management access with time-based one-time passwords and no external authentication service.'],
                ['🚫', 'IP & UA Rules',        'Block unwanted traffic using IP ranges, CIDR rules, User-Agent filtering, and geographic restrictions.'],
                ['📂', 'Zero-Trust SFTP',      'Dedicated, site-scoped SFTP access with restricted file permissions and no interactive shell.'],
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
