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
                <?php esc_html_e('Deploy PodNest in minutes with automated installation, containers, or a standalone binary. Manage everything from one web-based control panel.', 'podnest'); ?>
            </p>
            <div class="pn-quickstart-methods">
                <div class="pn-quickstart-method">
                    <span class="pn-quickstart-icon">🚀</span>
                    <div class="pn-quickstart-content">
                        <span class="pn-quickstart-label"><?php esc_html_e('pdnctl (Recommended)', 'podnest'); ?></span>
                        <span class="pn-quickstart-desc"><?php esc_html_e('Automated rootless installation, updates, service management, and upgrades.', 'podnest'); ?></span>
                    </div>
                </div>
                <div class="pn-quickstart-method">
                    <span class="pn-quickstart-icon">🐳</span>
                    <div class="pn-quickstart-content">
                        <span class="pn-quickstart-label"><?php esc_html_e('Container', 'podnest'); ?></span>
                        <span class="pn-quickstart-desc"><?php esc_html_e('Deploy the pre-built Podman image without compiling anything.', 'podnest'); ?></span>
                    </div>
                </div>
                <div class="pn-quickstart-method">
                    <span class="pn-quickstart-icon">⚙️</span>
                    <div class="pn-quickstart-content">
                        <span class="pn-quickstart-label"><?php esc_html_e('systemd', 'podnest'); ?></span>
                        <span class="pn-quickstart-desc"><?php esc_html_e('Run PodNest as a managed service with automatic restarts.', 'podnest'); ?></span>
                    </div>
                </div>
                <div class="pn-quickstart-method">
                    <span class="pn-quickstart-icon">🔧</span>
                    <div class="pn-quickstart-content">
                        <span class="pn-quickstart-label"><?php esc_html_e('From Source', 'podnest'); ?></span>
                        <span class="pn-quickstart-desc"><?php esc_html_e('Compile the Go binary for complete deployment flexibility.', 'podnest'); ?></span>
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