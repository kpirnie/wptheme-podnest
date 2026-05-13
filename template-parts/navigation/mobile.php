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
    <?php else : ?>
        <ul>
            <li><a href="<?php echo esc_url(home_url('/#features')); ?>"><?php esc_html_e('Features', 'podnest'); ?></a></li>
            <li><a href="<?php echo esc_url(home_url('/#how-it-works')); ?>"><?php esc_html_e('How It Works', 'podnest'); ?></a></li>
            <li><a href="<?php echo esc_url(home_url('/#site-types')); ?>"><?php esc_html_e('Site Types', 'podnest'); ?></a></li>
            <li><a href="<?php echo esc_url(home_url('/#support')); ?>"><?php esc_html_e('Support', 'podnest'); ?></a></li>
            <li><a href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/blog/')); ?>"><?php esc_html_e('Blog', 'podnest'); ?></a></li>
        </ul>
    <?php endif; ?>
    <a href="<?php echo podnest_opt_url('social_github', 'https://github.com/kpirnie/podnest'); ?>"
        class="pn-btn-primary"
        target="_blank"
        rel="noopener noreferrer">
        <?php esc_html_e('Get Started Free', 'podnest'); ?>
    </a>
</nav>