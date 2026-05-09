<?php
/**
 * Template Name: Runtimes — Detail Page
 *
 * @package PodNest
 */

get_header();

$runtimes = get_posts( [
    'post_type'      => 'podnest_runtime',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'post_status'    => 'publish',
] );
?>

<div class="pn-page-hero">
    <div class="pn-container">
        <?php podnest_breadcrumbs(); ?>
        <span class="pn-eyebrow"><?php esc_html_e( 'Supported Runtimes', 'podnest' ); ?></span>
        <h1><?php the_title(); ?></h1>
        <?php if ( $desc = get_the_excerpt() ) : ?>
        <p class="pn-lead" style="margin-top:12px;"><?php echo esc_html( $desc ); ?></p>
        <?php else : ?>
        <p class="pn-lead" style="margin-top:12px;"><?php esc_html_e( 'Every runtime PodNest supports — what\'s included, version options, and how it works.', 'podnest' ); ?></p>
        <?php endif; ?>
    </div>
</div>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post();
    if ( trim( get_the_content() ) ) : ?>
<div class="pn-content-area" style="padding-bottom:0;max-width:840px;">
    <?php the_content(); ?>
</div>
<?php endif; endwhile; endif; ?>

<!-- Runtime Detail Cards -->
<div class="pn-container" style="padding-bottom:100px;">

    <?php if ( empty( $runtimes ) ) : ?>
    <p class="pn-muted" style="text-align:center;padding:60px 0;"><?php esc_html_e( 'No runtimes added yet. Add some under PodNest Content → Runtimes.', 'podnest' ); ?></p>
    <?php else : ?>

    <div style="display:flex;flex-direction:column;gap:48px;">
        <?php foreach ( $runtimes as $i => $rt ) :
            $icon     = get_post_meta( $rt->ID, '_pn_icon', true );
            $versions = get_post_meta( $rt->ID, '_pn_versions', true );
            $more_url = get_post_meta( $rt->ID, '_pn_learn_more_url', true );
            $vlist    = $versions ? array_filter( array_map( 'trim', explode( "\n", $versions ) ) ) : [];
            $content  = apply_filters( 'the_content', $rt->post_content );
            $is_even  = $i % 2 === 1;
            ?>
        <div id="runtime-<?php echo esc_attr( $rt->post_name ); ?>"
             class="pn-reveal"
             style="display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:start;<?php echo $is_even ? 'direction:rtl;' : ''; ?>">

            <!-- Info panel -->
            <div style="<?php echo $is_even ? 'direction:ltr;' : ''; ?>">
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px;">
                    <?php if ( $icon ) : ?>
                    <span style="font-size:2.5rem;" aria-hidden="true"><?php echo esc_html( $icon ); ?></span>
                    <?php endif; ?>
                    <h2 style="font-size:1.8rem;margin:0;"><?php echo esc_html( $rt->post_title ); ?></h2>
                </div>

                <?php if ( $rt->post_excerpt ) : ?>
                <p class="pn-lead" style="font-size:1rem;margin-bottom:20px;"><?php echo esc_html( $rt->post_excerpt ); ?></p>
                <?php endif; ?>

                <?php if ( $vlist ) : ?>
                <div style="margin-bottom:20px;">
                    <p class="pn-muted" style="font-family:var(--pn-font-mono);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:10px;"><?php esc_html_e( 'Available Versions', 'podnest' ); ?></p>
                    <div class="pn-site-type-versions" style="justify-content:flex-start;">
                        <?php foreach ( $vlist as $v ) : ?>
                        <span class="pn-version-chip"><?php echo esc_html( $v ); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( $more_url ) : ?>
                <a href="<?php echo esc_url( $more_url ); ?>" class="pn-btn-secondary"><?php esc_html_e( 'External Docs', 'podnest' ); ?></a>
                <?php endif; ?>
            </div>

            <!-- Content panel -->
            <div class="pn-card" style="<?php echo $is_even ? 'direction:ltr;' : ''; ?>">
                <?php if ( $content ) : ?>
                <div style="color:var(--pn-text-dim);font-size:0.9rem;line-height:1.8;">
                    <?php echo wp_kses_post( $content ); ?>
                </div>
                <?php else : ?>
                <p class="pn-muted" style="margin:0;font-size:0.88rem;"><?php esc_html_e( 'Add content to this runtime post to display it here.', 'podnest' ); ?></p>
                <?php endif; ?>
            </div>

        </div>

        <?php if ( $i < count( $runtimes ) - 1 ) : ?>
        <hr style="border:none;border-top:1px solid var(--pn-border);">
        <?php endif; ?>

        <?php endforeach; ?>
    </div>

    <?php endif; ?>
</div>

<style>
@media (max-width: 860px) {
    #site-content .pn-container > div > div[style*="grid-template-columns"] {
        display: flex !important;
        flex-direction: column !important;
    }
}
</style>

<?php get_footer(); ?>
