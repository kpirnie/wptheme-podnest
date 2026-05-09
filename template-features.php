<?php
/**
 * Template Name: Features — Detail Page
 *
 * @package PodNest
 */

get_header();

$features = get_posts( [
    'post_type'      => 'podnest_feature',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'post_status'    => 'publish',
] );
?>

<div class="pn-page-hero">
    <div class="pn-container">
        <?php podnest_breadcrumbs(); ?>
        <span class="pn-eyebrow"><?php esc_html_e( 'Capabilities', 'podnest' ); ?></span>
        <h1><?php the_title(); ?></h1>
        <?php if ( $desc = get_the_excerpt() ) : ?>
        <p class="pn-lead" style="margin-top:12px;"><?php echo esc_html( $desc ); ?></p>
        <?php else : ?>
        <p class="pn-lead" style="margin-top:12px;"><?php esc_html_e( 'Everything PodNest can do — explained in full.', 'podnest' ); ?></p>
        <?php endif; ?>
    </div>
</div>

<!-- Brief page intro content if set in editor -->
<?php if ( have_posts() ) : while ( have_posts() ) : the_post();
    if ( trim( get_the_content() ) ) : ?>
<div class="pn-content-area" style="padding-bottom:0;max-width:840px;">
    <?php the_content(); ?>
</div>
<?php endif; endwhile; endif; ?>

<!-- Feature Cards with expanded content -->
<div class="pn-container" style="padding-bottom:100px;">

    <?php if ( empty( $features ) ) : ?>
    <p class="pn-muted" style="text-align:center;padding:60px 0;"><?php esc_html_e( 'No features added yet. Add some under PodNest Content → Features.', 'podnest' ); ?></p>
    <?php else : ?>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;" class="pn-features-detail-grid">
        <?php foreach ( $features as $i => $feat ) :
            $icon    = get_post_meta( $feat->ID, '_pn_icon', true );
            $more    = get_post_meta( $feat->ID, '_pn_learn_more_url', true );
            $content = apply_filters( 'the_content', $feat->post_content );
            $excerpt = $feat->post_excerpt;
            ?>
        <article id="feature-<?php echo esc_attr( $feat->post_name ); ?>"
                 class="pn-card pn-reveal pn-reveal-delay-<?php echo ( $i % 2 ) + 1; ?>"
                 style="display:flex;flex-direction:column;">
            <div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:16px;">
                <?php if ( $icon ) : ?>
                <div class="pn-card-icon" aria-hidden="true"><?php echo esc_html( $icon ); ?></div>
                <?php endif; ?>
                <h2 style="font-size:1.25rem;margin:0;align-self:center;"><?php echo esc_html( $feat->post_title ); ?></h2>
            </div>

            <?php if ( $excerpt ) : ?>
            <p class="pn-lead" style="font-size:1rem;margin-bottom:16px;"><?php echo esc_html( $excerpt ); ?></p>
            <?php endif; ?>

            <?php if ( $content ) : ?>
            <div class="pn-feature-full-content" style="color:var(--pn-text-dim);font-size:0.9rem;line-height:1.75;flex:1;">
                <?php echo wp_kses_post( $content ); ?>
            </div>
            <?php endif; ?>

            <?php if ( $more ) : ?>
            <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--pn-border);">
                <a href="<?php echo esc_url( $more ); ?>" class="pn-btn-ghost" style="font-size:0.85rem;">
                    <?php esc_html_e( 'Learn more', 'podnest' ); ?> <span class="pn-arrow">→</span>
                </a>
            </div>
            <?php endif; ?>
        </article>
        <?php endforeach; ?>
    </div>

    <?php endif; ?>

</div>

<!-- CTA -->
<div class="pn-container" style="padding-bottom:80px;">
    <div class="pn-cta-inner pn-reveal" style="text-align:center;">
        <h2><?php esc_html_e( 'Ready to See It in Action?', 'podnest' ); ?></h2>
        <p class="pn-lead" style="max-width:560px;margin:0 auto 32px;"><?php esc_html_e( 'PodNest is free and open source. Pull the image and have a working pod manager running in five minutes.', 'podnest' ); ?></p>
        <a href="https://github.com/kpirnie/podnest" class="pn-btn-primary" target="_blank" rel="noopener"><?php esc_html_e( 'Get Started on GitHub', 'podnest' ); ?></a>
    </div>
</div>

<style>
@media (max-width: 720px) {
    .pn-features-detail-grid { grid-template-columns: 1fr !important; }
}
</style>

<?php get_footer(); ?>
