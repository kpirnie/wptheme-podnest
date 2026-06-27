<?php

/**
 * Features
 * @package PodNest
 */

defined('ABSPATH') || exit;
?>

<!-- -- FEATURES — managed via PodNest Content → Features --------------- -->
<section id="features" aria-labelledby="features-heading">
    <div class="pn-container pn-features">
        <header class="pn-section-header pn-reveal">
            <span class="pn-eyebrow"><?php esc_html_e('Capabilities', 'podnest'); ?></span>
            <h2 id="features-heading"><?php esc_html_e('A Complete Stack in Every Pod', 'podnest'); ?></h2>
            <p class="pn-section-desc">
                <?php esc_html_e('PodNest ships with enterprise-grade tools configured and wired together out of the box. From provisioning to monitoring — covered.', 'podnest'); ?>
            </p>
        </header>

        <?php echo podnest_render_block('podnest/features-grid', ['columns' => 3]); ?>

        <?php
        /* Link to full features page if template-features.php is assigned */
        $features_pages = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'template-features.php']);
        if (! empty($features_pages)) :
        ?>
            <div style="text-align:center;margin-top:40px;" class="pn-reveal">
                <a href="<?php echo esc_url(get_permalink($features_pages[0]->ID)); ?>" class="pn-btn-ghost">
                    <?php esc_html_e('All capabilities', 'podnest'); ?> <span class="pn-arrow">→</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>