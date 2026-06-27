<?php

/**
 * Mobile Navigation
 *
 * @package PodNest
 */
defined('ABSPATH') || exit;
?>
<!-- -- Mobile navigation panel (hidden until hamburger activated) -->
<nav id="pn-mobile-nav"
    class="pn-mobile-nav"
    aria-label="<?php esc_attr_e('Mobile navigation', 'podnest'); ?>">
    <?php if (has_nav_menu('primary')) : ?>
        <?php wp_nav_menu([
            'theme_location' => 'primary',
            'container'      => false,
            'fallback_cb'    => false,
        ]); ?>
    <?php endif; ?>
    <a href="<?php echo podnest_opt_url('social_github', 'https://github.com/kpirnie/podnest'); ?>"
        class="pn-btn-primary"
        target="_blank"
        rel="noopener noreferrer">
        <?php esc_html_e('Get Started Free', 'podnest'); ?>
    </a>
</nav>