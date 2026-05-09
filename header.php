<?php

/**
 * Site Header Template
 *
 * Outputs the skip link, fixed navigation bar, and mobile nav panel.
 * Also opens the #main-content wrapper that footer.php closes.
 *
 * @package PodNest
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <a class="skip-link screen-reader-text" href="#main-content">
        <?php esc_html_e('Skip to content', 'podnest'); ?>
    </a>

    <!-- -- Fixed navigation bar ------------------------------------ -->
    <header id="site-header" role="banner" aria-label="<?php esc_attr_e('Site header', 'podnest'); ?>">
        <div class="pn-container">
            <div class="pn-nav-inner">

                <!-- Logo + brand word mark -->
                <a href="<?php echo esc_url(home_url('/')); ?>"
                    class="pn-nav-logo"
                    rel="home"
                    aria-label="<?php echo esc_attr(get_bloginfo('name') . ' — ' . __('Home', 'podnest')); ?>">
                    <img src="https://c.pnst.us/logos/podnest.svg" alt="PodNest ~ Secure. Manage. Deploy" width="80" height="80" class="pn-nav-logo-img" loading="eager">
                    <span class="pn-brand-word" aria-hidden="true">
                        <span class="pn-brand-pod">POD</span><span class="pn-brand-nest">NEST</span>
                    </span>
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
                    <?php else : ?>
                        <!-- Fallback menu when no menu is assigned in the admin -->
                        <ul class="pn-nav-links" role="list">
                            <li><a href="<?php echo esc_url(home_url('/#features')); ?>" class="pn-nav-link"><?php esc_html_e('Features', 'podnest'); ?></a></li>
                            <li><a href="<?php echo esc_url(home_url('/#how-it-works')); ?>" class="pn-nav-link"><?php esc_html_e('How It Works', 'podnest'); ?></a></li>
                            <li><a href="<?php echo esc_url(home_url('/#site-types')); ?>" class="pn-nav-link"><?php esc_html_e('Site Types', 'podnest'); ?></a></li>
                            <li><a href="<?php echo esc_url(home_url('/#support')); ?>" class="pn-nav-link"><?php esc_html_e('Support', 'podnest'); ?></a></li>
                            <li><a href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/blog/')); ?>" class="pn-nav-link"><?php esc_html_e('Blog', 'podnest'); ?></a></li>
                            <li class="pn-nav-cta"><a href="<?php echo podnest_opt_url('social_github', 'https://github.com/kpirnie/podnest'); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('GitHub', 'podnest'); ?></a></li>
                        </ul>
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

    <!-- -- Main content wrapper — closed in footer.php ------------- -->
    <div id="main-content">