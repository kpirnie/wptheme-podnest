<?php

/**
 * CTA 
 * @package PodNest
 */

defined('ABSPATH') || exit;
?>
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

                <a href="<?php echo podnest_opt_url('support_contact_url', '/support/contact/'); ?>"
                    class="pn-btn-secondary">
                    <?php esc_html_e('Talk to the Author', 'podnest'); ?>
                </a>
            </div>
        </div>
    </div>
</section>