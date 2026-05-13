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
</div><!-- /#main-content -->
<footer id="site-footer" role="contentinfo">
    <div class="pn-container">
        <div class="pn-footer-grid">
            <div class="pn-footer-brand">
                <div class="pn-footer-logo-wrap">
                    <img src="https://c.pnst.us/logos/podnest.svg" alt="PodNest ~ Secure. Manage. Deploy" width="64" height="64" class="pn-nav-logo-img" loading="eager">
                    <h2>
                        <span class="pn-brand-pod">POD</span><span class="pn-brand-nest">NEST</span>
                    </h2>
                </div>
                <p><?php echo esc_html(podnest_opt('footer_tagline', 'Hardened. Automated. Production-Ready.')); ?></p>
                <p class="pn-muted" style="font-size:0.82rem;">
                    <?php esc_html_e('Open source under the MIT license. Built with Go, running on Podman.', 'podnest'); ?>
                </p>
            </div><!-- /.pn-footer-brand -->
            <div class="pn-footer-links">
                <?php if (is_active_sidebar('footer-product')) : ?>
                    <?php dynamic_sidebar('footer-product'); ?>
                <?php else : ?>
                    <h4 class="pn-footer-widget-title"><?php esc_html_e('Product', 'podnest'); ?></h4>
                    <ul>
                        <li><a href="<?php echo esc_url(home_url('/#features')); ?>"><?php esc_html_e('Features', 'podnest'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/#site-types')); ?>"><?php esc_html_e('Site Types', 'podnest'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/#how-it-works')); ?>"><?php esc_html_e('How It Works', 'podnest'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/#security')); ?>"><?php esc_html_e('Security', 'podnest'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/#support')); ?>"><?php esc_html_e('Pricing', 'podnest'); ?></a></li>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="pn-footer-links">
                <?php if (is_active_sidebar('footer-resources')) : ?>
                    <?php dynamic_sidebar('footer-resources'); ?>
                <?php else : ?>
                    <h4 class="pn-footer-widget-title"><?php esc_html_e('Resources', 'podnest'); ?></h4>
                    <ul>
                        <li><a href="<?php echo esc_url(home_url('/blog/')); ?>"><?php esc_html_e('Blog', 'podnest'); ?></a></li>
                        <li><a href="https://github.com/kpirnie/podnest/blob/main/readme.md" rel="noopener" target="_blank"><?php esc_html_e('Documentation', 'podnest'); ?></a></li>
                        <li><a href="https://github.com/kpirnie/podnest/releases" rel="noopener" target="_blank"><?php esc_html_e('Changelog', 'podnest'); ?></a></li>
                        <li><a href="https://github.com/kpirnie/podnest/issues" rel="noopener" target="_blank"><?php esc_html_e('Issues', 'podnest'); ?></a></li>
                        <li><a href="https://github.com/kpirnie/podnest/blob/main/LICENSE" rel="noopener" target="_blank"><?php esc_html_e('MIT License', 'podnest'); ?></a></li>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="pn-footer-links">
                <?php if (is_active_sidebar('footer-company')) : ?>
                    <?php dynamic_sidebar('footer-company'); ?>
                <?php else : ?>
                    <h4 class="pn-footer-widget-title"><?php esc_html_e('Company', 'podnest'); ?></h4>
                    <ul>
                        <li><a href="https://kevinpirnie.com/" rel="noopener" target="_blank"><?php esc_html_e('About Kevin', 'podnest'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/#support')); ?>"><?php esc_html_e('Paid Support', 'podnest'); ?></a></li>
                        <li><a href="https://kevinpirnie.com/about-kevin-pirnie/lets-talk/" rel="noopener" target="_blank"><?php esc_html_e('Contact', 'podnest'); ?></a></li>
                        <?php $privacy = get_privacy_policy_url();
                        if ($privacy) : ?>
                            <li><a href="<?php echo esc_url($privacy); ?>"><?php esc_html_e('Privacy Policy', 'podnest'); ?></a></li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div><!-- /.pn-footer-grid -->
        <div class="pn-footer-bottom">
            <p class="pn-footer-copy">
                Copyright &copy; <?php echo esc_html(gmdate('Y')); ?>
                <a href="https://kevinpirnie.com/" rel="noopener" target="_blank">Kevin Pirnie</a>.
                <?php esc_html_e('All Rights Reserved.', 'podnest'); ?>
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