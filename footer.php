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

<!-- ════════════════════════════════════════════════════════
     SITE FOOTER
═════════════════════════════════════════════════════════ -->
<footer id="site-footer" role="contentinfo">
    <div class="pn-container">

        <!-- Four-column grid: brand | product | resources | company -->
        <div class="pn-footer-grid">

            <!-- -- Brand column ------------------------------ -->
            <div class="pn-footer-brand">

                <!-- Logo + brand word mark inline -->
                <div class="pn-footer-logo-wrap">
                    <img src="https://c.pnst.us/logos/podnest.svg" alt="PodNest ~ Secure. Manage. Deploy" width="64" height="64" class="pn-nav-logo-img" loading="eager">
                    <h3>
                        <span class="pn-brand-pod">POD</span><span class="pn-brand-nest">NEST</span>
                    </h3>
                </div>

                <p><?php echo esc_html(podnest_opt('footer_tagline', 'Hardened. Automated. Production-Ready.')); ?></p>

                <p class="pn-muted" style="font-size:0.82rem;">
                    <?php esc_html_e('Open source under the MIT license. Built with Go, running on Podman.', 'podnest'); ?>
                </p>

                <!-- GitHub star CTA -->
                <a href="<?php echo podnest_opt_url('social_github', 'https://github.com/kpirnie/podnest'); ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="pn-btn-secondary"
                    style="display:inline-flex;margin-top:8px;padding:9px 18px;font-size:0.82rem;">
                    <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true" style="flex-shrink:0">
                        <path d="M12 0C5.37 0 0 5.37 0 12c0 5.3 3.44 9.8 8.2 11.38.6.11.82-.26.82-.58v-2.04c-3.34.73-4.04-1.6-4.04-1.6-.54-1.37-1.33-1.74-1.33-1.74-1.08-.74.08-.73.08-.73 1.2.09 1.83 1.23 1.83 1.23 1.07 1.83 2.8 1.3 3.48 1 .1-.78.42-1.3.76-1.6-2.67-.3-5.47-1.33-5.47-5.93 0-1.31.47-2.38 1.24-3.22-.13-.3-.54-1.52.12-3.18 0 0 1-.32 3.3 1.23a11.5 11.5 0 0 1 3-.4c1.02.01 2.04.14 3 .4 2.28-1.55 3.29-1.23 3.29-1.23.66 1.66.25 2.88.12 3.18.77.84 1.23 1.91 1.23 3.22 0 4.61-2.8 5.63-5.48 5.92.43.37.82 1.1.82 2.22v3.29c0 .32.22.7.83.58C20.56 21.8 24 17.3 24 12 24 5.37 18.63 0 12 0z" />
                    </svg>
                    <?php esc_html_e('Star on GitHub', 'podnest'); ?>
                </a>

            </div><!-- /.pn-footer-brand -->

            <!-- -- Product column — widget sidebar ----------- -->
            <div class="pn-footer-links">
                <?php if (is_active_sidebar('footer-product')) : ?>
                    <?php dynamic_sidebar('footer-product'); ?>
                <?php else : ?>
                    <!-- Fallback static links when no widget is assigned -->
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

            <!-- -- Resources column — widget sidebar --------- -->
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

            <!-- -- Company column — widget sidebar ----------- -->
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

        <!-- -- Bottom bar: copyright + social icons ------------ -->
        <div class="pn-footer-bottom">

            <p class="pn-footer-copy">
                &copy; <?php echo esc_html(gmdate('Y')); ?>
                <?php echo esc_html(get_bloginfo('name')); ?>.
                <?php esc_html_e('Built by', 'podnest'); ?>
                <a href="https://kevinpirnie.com/" rel="noopener" target="_blank">Kevin Pirnie</a>.
                <?php esc_html_e('MIT License.', 'podnest'); ?>
            </p>

            <!-- Social icon row: GitHub, Discord, X/Twitter, Facebook -->
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
                } else {
                    /* Fallback to Customizer values until menu is assigned */
                    foreach (podnest_social_links() as $social) :
                        $url = podnest_opt_url($social['mod_key'], $social['default']);
                        if (! $url) continue;
                ?>
                        <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer"
                            aria-label="<?php echo esc_attr($social['label']); ?>" title="<?php echo esc_attr($social['label']); ?>">
                            <?php echo $social['icon']; ?>
                        </a>
                    <?php endforeach; ?>
                <?php } ?>
            </nav>

        </div><!-- /.pn-footer-bottom -->

    </div><!-- /.pn-container -->
</footer>

<!-- -- Scroll-to-top button (shown/hidden via JS) -------------- -->
<?php podnest_scroll_top_button(); ?>

<?php wp_footer(); ?>
</body>

</html>