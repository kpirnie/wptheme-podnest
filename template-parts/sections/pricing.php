<?php

/**
 * Pricing 
 * @package PodNest
 */

defined('ABSPATH') || exit;
?>
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