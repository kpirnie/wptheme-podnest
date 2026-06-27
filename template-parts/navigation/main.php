<?php

/**
 * Main Navigation
 *
 * @package PodNest
 */
defined('ABSPATH') || exit;
?>
<header id="site-header" role="banner" aria-label="<?php esc_attr_e('Site header', 'podnest'); ?>">
    <div class="pn-container">
        <div class="pn-nav-inner">

            <!-- Logo + brand word mark -->
            <a href="<?php echo esc_url(home_url('/')); ?>"
                class="pn-nav-logo"
                rel="home"
                aria-label="<?php echo esc_attr(get_bloginfo('name') . ' — ' . __('Home', 'podnest')); ?>">
                <img src="https://c.pdn.st/logos/podnest.svg" alt="PodNest ~ Secure. Manage. Deploy" width="80" height="80" class="pn-nav-logo-img" loading="eager">
                <h1 class="pn-brand-word" aria-hidden="true">
                    <span class="pn-brand-pod">POD</span><span class="pn-brand-nest">NEST</span>
                </h1>
            </a>

            <!-- Desktop primary navigation -->
            <nav id="primary-nav"
                role="navigation"
                aria-label="<?php esc_attr_e('Primary', 'podnest'); ?>">
                <?php if (has_nav_menu('primary')) : ?>
                    <?php wp_nav_menu([
                        'theme_location' => 'primary',
                        'container'      => false,
                        'menu_class'     => 'pn-nav-links',
                        'walker'         => new PodNest_Nav_Walker(),
                        'fallback_cb'    => false,
                        'items_wrap'     => '<ul id="%1$s" class="%2$s" role="list">%3$s</ul>',
                    ]); ?>
                <?php endif; ?>
            </nav>

            <!-- Hamburger toggle (mobile only — hidden via CSS on desktop) -->
            <button id="pn-hamburger"
                class="pn-nav-hamburger"
                aria-controls="pn-mobile-nav"
                aria-expanded="false"
                aria-label="<?php esc_attr_e('Toggle navigation menu', 'podnest'); ?>">
                <span></span>
                <span></span>
                <span></span>
            </button>

        </div><!-- /.pn-nav-inner -->
    </div><!-- /.pn-container -->
</header>