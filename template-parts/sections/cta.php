<?php

/**
 * CTA
 * @package PodNest
 */

defined('ABSPATH') || exit;

$quickstart = get_posts([
    'post_type'      => PodNest_Quickstart::POST_TYPE,
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order title',
    'order'          => 'ASC',
]);
?>
<!-- -- CTA ------------------------------------------------------------- -->
<section id="cta" class="pn-cta-section" aria-labelledby="cta-heading">
    <div class="pn-container">
        <div class="pn-cta-inner pn-reveal">
            <span class="pn-eyebrow"><?php echo esc_html(PodNest_Section_Copy::value('cta', 'eyebrow', 'Get Started')); ?></span>
            <h2 id="cta-heading"><?php echo esc_html(PodNest_Section_Copy::value('cta', 'title', 'Five Minutes to Your First Pod.')); ?></h2>
            <p class="pn-lead">
                <?php echo esc_html(PodNest_Section_Copy::value('cta', 'description', 'Deploy PodNest in minutes with automated installation, containers, or a standalone binary. Manage everything from one web-based control panel.')); ?>
            </p>
            <?php if (! empty($quickstart)) : ?>
                <div class="pn-quickstart-methods">
                    <?php foreach ($quickstart as $method) :
                        $icon = (string) get_post_meta($method->ID, '_pn_icon', true);
                        $desc = has_excerpt($method) ? $method->post_excerpt : $method->post_content;
                    ?>
                        <div class="pn-quickstart-method">
                            <?php if ($icon) : ?>
                                <span class="pn-quickstart-icon" aria-hidden="true"><?php echo esc_html($icon); ?></span>
                            <?php endif; ?>
                            <div class="pn-quickstart-content">
                                <span class="pn-quickstart-label"><?php echo esc_html(get_the_title($method)); ?></span>
                                <span class="pn-quickstart-desc"><?php echo esc_html(wp_strip_all_tags($desc)); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
