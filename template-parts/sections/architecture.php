<?php

/**
 * Architecture
 * @package PodNest
 */

defined('ABSPATH') || exit;

$nodes = [];
foreach (get_posts([
    'post_type'      => PodNest_Diagram_Nodes::POST_TYPE,
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'ASC',
]) as $node) {
    $position = (string) get_post_meta($node->ID, '_pn_position', true);
    if (in_array($position, ['internet', 'proxy', 'manager'], true) && ! isset($nodes[$position])) {
        $nodes[$position] = $node;
    }
}

$pods = get_posts([
    'post_type'      => PodNest_Architecture::POST_TYPE,
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order title',
    'order'          => 'ASC',
]);
?>
<!-- -- ARCHITECTURE — managed via PodNest Content → Architecture ------- -->
<section id="how-it-works" aria-labelledby="arch-heading" class="pn-section-alt">
    <div class="pn-container">
        <header class="pn-section-header pn-reveal">
            <span class="pn-eyebrow"><?php echo esc_html(PodNest_Section_Copy::value('architecture', 'eyebrow', 'Architecture')); ?></span>
            <h2 id="arch-heading"><?php echo esc_html(PodNest_Section_Copy::value('architecture', 'title', 'One Pod Per Site. Built for Isolation.')); ?></h2>
            <p class="pn-section-desc">
                <?php echo esc_html(PodNest_Section_Copy::value('architecture', 'description', 'Every website runs in its own isolated Podman pod, with dedicated containers, networking, and site-specific configurations. PodNest centralizes management, routing, and security while keeping individual workloads separated — reducing cross-site exposure and simplifying administration.')); ?>
            </p>
        </header>

        <div class="pn-arch-diagram pn-reveal pn-reveal-delay-2">
            <div class="pn-arch-row pn-arch-top">
                <div class="pn-arch-node pn-arch-internet">
                    <?php $internet = $nodes['internet'] ?? null; ?>
                    <?php echo esc_html($internet ? (string) get_post_meta($internet->ID, '_pn_icon', true) : '🌐'); ?>
                    <?php echo esc_html($internet ? $internet->post_title : __('Internet', 'podnest')); ?>
                </div>
            </div>
            <div class="pn-arch-arrow" aria-hidden="true">↓</div>
            <div class="pn-arch-row">
                <div class="pn-arch-node pn-arch-proxy">
                    <?php $proxy = $nodes['proxy'] ?? null; ?>
                    <span class="pn-arch-icon"><?php echo esc_html($proxy ? (string) get_post_meta($proxy->ID, '_pn_icon', true) : '🔀'); ?></span>
                    <strong><?php echo esc_html($proxy ? $proxy->post_title : __('PodNest Reverse Proxy', 'podnest')); ?></strong>
                    <small><?php echo esc_html($proxy ? (string) get_post_meta($proxy->ID, '_pn_subtitle', true) : __("Let's Encrypt TLS · HTTP/2", 'podnest')); ?></small>
                </div>
            </div>
            <div class="pn-arch-arrow" aria-hidden="true">↓</div>
            <?php if (! empty($pods)) : ?>
                <div class="pn-arch-row pn-arch-pods">
                    <?php foreach ($pods as $pod) :
                        $icon     = (string) get_post_meta($pod->ID, '_pn_icon', true);
                        $services = (string) get_post_meta($pod->ID, '_pn_services', true);
                    ?>
                        <div class="pn-arch-pod">
                            <div class="pn-arch-pod-header">
                                <?php if ($icon) : ?>
                                    <span><?php echo esc_html($icon); ?></span>
                                <?php endif; ?>
                                <strong><?php echo esc_html(get_the_title($pod)); ?></strong>
                            </div>
                            <div class="pn-arch-pod-services"><?php echo esc_html($services); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="pn-arch-row" style="margin-top: 32px;">
                <div class="pn-arch-node pn-arch-podnest">
                    <?php $manager = $nodes['manager'] ?? null; ?>
                    <span class="pn-arch-icon"><?php echo esc_html($manager ? (string) get_post_meta($manager->ID, '_pn_icon', true) : '⚙️'); ?></span>
                    <strong><?php echo esc_html($manager ? $manager->post_title : __('PodNest Manager', 'podnest')); ?></strong>
                    <small><?php echo esc_html($manager ? (string) get_post_meta($manager->ID, '_pn_subtitle', true) : __('Control Plane · Go · REST API · TOTP 2FA', 'podnest')); ?></small>
                </div>
            </div>
        </div>
    </div>
</section>
