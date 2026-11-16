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
            <span class="pn-eyebrow"><?php echo esc_html(PodNest_Section_Copy::value('pricing', 'eyebrow', 'Paid Support')); ?></span>
            <h2 id="pricing-heading"><?php echo esc_html(PodNest_Section_Copy::value('pricing', 'title', "PodNest is Free. Expert Help Isn't.")); ?></h2>
            <p class="pn-section-desc">
                <?php echo esc_html(PodNest_Section_Copy::value('pricing', 'description', 'The software is MIT-licensed and always will be. If you want hands-on setup, consulting, or priority support — that is where paid tiers come in.')); ?>
            </p>
        </header>

        <?php echo podnest_render_block('podnest/pricing-table'); ?>
    </div>
</section>
