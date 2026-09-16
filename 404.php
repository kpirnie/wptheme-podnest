<?php

/**
 * 404 — Not Found Template
 *
 * @package PodNest
 */

defined('ABSPATH') || exit;

// get the header
get_header();
?>

<div class="pn-404-section">
    <div class="pn-container pn-404-container">

        <div class="pn-gradient-text pn-404">
            404
        </div>

        <h1 class="pn-not-found">
            <?php esc_html_e('Page Not Found', 'podnest'); ?>
        </h1>

        <p class="pn-lead pn-404-lead">
            <?php esc_html_e('The page you are looking for does not exist, was moved, or has been removed.', 'podnest'); ?>
        </p>

        <div class="pn-hero-ctas pn-404-cta">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="pn-btn-primary">
                ← <?php esc_html_e('Back to Home', 'podnest'); ?>
            </a>
            <a href="<?php echo esc_url(home_url('/#features')); ?>" class="pn-btn-secondary">
                <?php esc_html_e('View Features', 'podnest'); ?>
            </a>
        </div>

    </div>
</div>

<?php

// get teh footer
get_footer();
