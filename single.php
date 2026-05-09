<?php
/**
 * Single Post Template
 *
 * Renders individual blog posts. Includes breadcrumbs, category badges,
 * featured image, paginated content, tag badges, prev/next navigation,
 * and a comments section when comments are open.
 *
 * @package PodNest
 */

defined( 'ABSPATH' ) || exit;

get_header();

/* Start the main query loop — there is always exactly one post here */
if ( have_posts() ) :
    the_post();
?>

<!-- Page hero with breadcrumb + post meta -->
<div class="pn-page-hero" style="padding-bottom:40px;">
    <div class="pn-container" style="max-width:840px;">

        <?php podnest_breadcrumbs(); ?>

        <!-- Category badges -->
        <?php $cats = get_the_category(); if ( $cats ) : ?>
        <div style="margin-bottom:14px;">
            <?php foreach ( $cats as $cat ) : ?>
            <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"
               class="pn-badge pn-badge-blue"
               style="margin-right:6px;">
                <?php echo esc_html( $cat->name ); ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <h1><?php the_title(); ?></h1>

        <!-- Post meta row: date + author + comment count -->
        <div class="pn-muted"
             style="margin-top:14px;font-family:var(--pn-font-mono);font-size:0.8rem;display:flex;gap:24px;flex-wrap:wrap;">
            <span>
                <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                    <?php echo esc_html( get_the_date() ); ?>
                </time>
            </span>
            <span><?php esc_html_e( 'By', 'podnest' ); ?> <?php the_author_posts_link(); ?></span>
            <?php if ( get_comments_number() ) : ?>
            <span><?php comments_number( '0 comments', '1 comment', '% comments' ); ?></span>
            <?php endif; ?>
        </div>

    </div>
</div><!-- /.pn-page-hero -->

<!-- Main article content -->
<div class="pn-content-area">

    <!-- Featured image — full-width above the content -->
    <?php if ( has_post_thumbnail() ) : ?>
    <figure style="margin:-20px 0 40px;">
        <?php the_post_thumbnail( 'podnest-hero', [
            'style' => 'width:100%;border-radius:var(--pn-radius-lg);',
            'alt'   => esc_attr( get_the_title() ),
        ] ); ?>
    </figure>
    <?php endif; ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <?php
        the_content();

        /* Page links for posts that are broken into multiple pages via <!--nextpage--> */
        wp_link_pages( [
            'before' => '<nav class="pn-page-links" aria-label="' . esc_attr__( 'Page navigation', 'podnest' ) . '"><span>' . __( 'Pages:', 'podnest' ) . '</span>',
            'after'  => '</nav>',
        ] );
        ?>
    </article>

    <!-- Tag badges -->
    <?php $tags = get_the_tags(); if ( $tags ) : ?>
    <div style="margin-top:32px;padding-top:24px;border-top:1px solid var(--pn-border);">
        <?php foreach ( $tags as $tag ) : ?>
        <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>"
           class="pn-badge pn-badge-blue"
           style="margin-right:6px;margin-bottom:6px;">
            <?php echo esc_html( $tag->name ); ?>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Prev / next post navigation -->
    <nav class="pn-post-nav"
         style="display:flex;justify-content:space-between;gap:20px;margin-top:40px;padding-top:32px;border-top:1px solid var(--pn-border);"
         aria-label="<?php esc_attr_e( 'Post navigation', 'podnest' ); ?>">
        <div>
            <?php $prev = get_previous_post(); if ( $prev ) : ?>
            <span class="pn-muted" style="font-size:0.72rem;font-family:var(--pn-font-mono);display:block;margin-bottom:4px;">
                ← <?php esc_html_e( 'Previous', 'podnest' ); ?>
            </span>
            <a href="<?php echo esc_url( get_permalink( $prev ) ); ?>" style="color:var(--pn-white);font-weight:600;">
                <?php echo esc_html( get_the_title( $prev ) ); ?>
            </a>
            <?php endif; ?>
        </div>
        <div style="text-align:right;">
            <?php $next = get_next_post(); if ( $next ) : ?>
            <span class="pn-muted" style="font-size:0.72rem;font-family:var(--pn-font-mono);display:block;margin-bottom:4px;">
                <?php esc_html_e( 'Next', 'podnest' ); ?> →
            </span>
            <a href="<?php echo esc_url( get_permalink( $next ) ); ?>" style="color:var(--pn-white);font-weight:600;">
                <?php echo esc_html( get_the_title( $next ) ); ?>
            </a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Comments — only rendered when comments are enabled or exist -->
    <?php if ( comments_open() || get_comments_number() ) : ?>
        <?php comments_template(); ?>
    <?php endif; ?>

</div><!-- /.pn-content-area -->

<?php
endif; // have_posts()

get_footer();
?>
