<?php

/**
 * Runtimes 
 * @package PodNest
 */

defined('ABSPATH') || exit;
?>
<!-- -- RUNTIMES — managed via PodNest Content → Runtimes --------------- -->
<section id="site-types" aria-labelledby="runtimes-heading">
    <div class="pn-container">
        <header class="pn-section-header pn-reveal">
            <span class="pn-eyebrow"><?php esc_html_e('Runtimes', 'podnest'); ?></span>
            <h2 id="runtimes-heading"><?php esc_html_e('Run Anything. Manage Everything.', 'podnest'); ?></h2>
            <p class="pn-section-desc">
                <?php esc_html_e('PodNest provisions purpose-built pods for every major server-side runtime. Pick your stack — the wiring is handled for you.', 'podnest'); ?>
            </p>
        </header>

        <?php echo podnest_render_block('podnest/runtimes-grid', ['columns' => 4]); ?>

        <?php
        $runtimes_pages = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'template-runtimes.php']);
        if (! empty($runtimes_pages)) :
        ?>
            <div style="text-align:center;margin-top:40px;" class="pn-reveal">
                <a href="<?php echo esc_url(get_permalink($runtimes_pages[0]->ID)); ?>" class="pn-btn-ghost">
                    <?php esc_html_e('Explore all runtimes', 'podnest'); ?> <span class="pn-arrow">→</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>