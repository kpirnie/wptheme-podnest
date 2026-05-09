<?php
/**
 * 404 — Not Found Template
 *
 * @package PodNest
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="pn-404-section">
    <div class="pn-container" style="text-align:center;padding:120px 0;">

        <div class="pn-gradient-text" style="font-size:8rem;font-family:var(--pn-font-brand);font-weight:900;line-height:1;">
            404
        </div>

        <h1 style="font-size:1.8rem;margin:16px 0 12px;">
            <?php esc_html_e( 'Page Not Found', 'podnest' ); ?>
        </h1>

        <p class="pn-lead" style="max-width:480px;margin:0 auto 36px;">
            <?php esc_html_e( 'The page you are looking for does not exist, was moved, or has been removed.', 'podnest' ); ?>
        </p>

        <div class="pn-hero-ctas" style="justify-content:center;gap:16px;">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="pn-btn-primary">
                ← <?php esc_html_e( 'Back to Home', 'podnest' ); ?>
            </a>
            <a href="<?php echo esc_url( home_url( '/#features' ) ); ?>" class="pn-btn-secondary">
                <?php esc_html_e( 'View Features', 'podnest' ); ?>
            </a>
        </div>

    </div>
</div>

<?php get_footer(); ?>
