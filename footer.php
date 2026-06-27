<?php

/**
 * Site Footer Template
 *
 * Closes #main-content, renders the full-width footer (brand column +
 * three widget-area columns + bottom bar), outputs the scroll-to-top
 * button, and calls wp_footer().
 *
 * Widget sidebars are registered in PodNest_Assets::register_sidebars().
 * Social link URLs come from the Customizer (PodNest_Customizer).
 *
 * @package PodNest
 */
?>
</main><!-- /#main-content -->
<footer id="site-footer" role="contentinfo">
    <div class="pn-container">
        <div class="pn-footer-grid">
            <div class="pn-footer-brand">
                <div class="pn-footer-logo-wrap">
                    <img src="https://c.pdn.st/logos/podnest.svg" alt="PodNest" width="64" height="64" class="pn-nav-logo-img" loading="eager">
                    <h2>
                        <span class="pn-brand-pod">POD</span><span class="pn-brand-nest">NEST</span>
                    </h2>
                </div>
                <p><?php echo esc_html(podnest_opt('footer_tagline', 'Hardened. Automated. Production-Ready.')); ?></p>
            </div><!-- /.pn-footer-brand -->
            <div class="pn-footer-links">
                <?php if (is_active_sidebar('footer-product')) : ?>
                    <?php dynamic_sidebar('footer-product'); ?>
                <?php endif; ?>
            </div>
            <div class="pn-footer-links">
                <?php if (is_active_sidebar('footer-resources')) : ?>
                    <?php dynamic_sidebar('footer-resources'); ?>
                <?php endif; ?>
            </div>
            <div class="pn-footer-links">
                <?php if (is_active_sidebar('footer-company')) : ?>
                    <?php dynamic_sidebar('footer-company'); ?>
                <?php endif; ?>
            </div>
        </div><!-- /.pn-footer-grid -->
        <div class="pn-footer-bottom">
            <p class="pn-footer-copy">
                Copyright &copy; <?php echo esc_html(gmdate('Y')); ?>
                <a href="https://kevinpirnie.com/" rel="noopener" target="_blank">Kevin Pirnie</a>.
                <?php esc_html_e('All Rights Reserved.', 'podnest'); ?>
                <br />
                <a href="<?php echo esc_url(get_privacy_policy_url()); ?>"><?php esc_html_e('Privacy Policy', 'podnest'); ?></a> |
                <a href="<?php echo esc_url(get_privacy_policy_url()); ?>"><?php esc_html_e('Cookie Policy', 'podnest'); ?></a>
            </p>
            <nav class="pn-footer-social" aria-label="<?php esc_attr_e('Social media links', 'podnest'); ?>">
                <?php
                if (has_nav_menu('social')) {
                    wp_nav_menu([
                        'theme_location' => 'social',
                        'container'      => false,
                        'walker'         => new PodNest_Social_Walker(),
                        'fallback_cb'    => false,
                        'items_wrap'     => '%3$s', // bare <a> tags only — no <ul>
                    ]);
                } ?>
            </nav>
        </div><!-- /.pn-footer-bottom -->
    </div><!-- /.pn-container -->
</footer>
<?php podnest_scroll_top_button(); ?>
<?php wp_footer(); ?>
</body>

</html>