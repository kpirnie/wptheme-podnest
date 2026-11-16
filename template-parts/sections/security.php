<?php

/**
 * Security
 * @package PodNest
 */

defined('ABSPATH') || exit;

$security = get_posts([
    'post_type'      => PodNest_CPTs::SECURITY,
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order title',
    'order'          => 'ASC',
]);
?>
<!-- -- SECURITY — managed via PodNest Content → Security --------------- -->
<section id="security" aria-labelledby="security-heading" class="pn-section-alt">
    <div class="pn-container">
        <header class="pn-section-header pn-reveal">
            <span class="pn-eyebrow"><?php echo esc_html(PodNest_Section_Copy::value('security', 'eyebrow', 'Security')); ?></span>
            <h2 id="security-heading"><?php echo esc_html(PodNest_Section_Copy::value('security', 'title', 'Hardened at Every Layer.')); ?></h2>
        </header>

        <?php if (! empty($security)) : ?>
            <div class="pn-security-grid">
                <?php foreach ($security as $i => $item) :
                    $icon  = (string) get_post_meta($item->ID, '_pn_icon', true);
                    $desc  = has_excerpt($item) ? $item->post_excerpt : $item->post_content;
                    $delay = ($i % 3) + 1;
                ?>
                    <article class="pn-security-card pn-reveal pn-reveal-delay-<?php echo esc_attr($delay); ?>">
                        <?php if ($icon) : ?>
                            <span class="pn-security-icon" aria-hidden="true"><?php echo esc_html($icon); ?></span>
                        <?php endif; ?>
                        <h3><?php echo esc_html(get_the_title($item)); ?></h3>
                        <p><?php echo esc_html(wp_strip_all_tags($desc)); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
