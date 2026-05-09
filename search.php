<?php
/**
 * Search Results Template
 *
 * Displays posts matching the search query. The search term is surfaced
 * in the page heading and in a screen-reader announcement via aria-live
 * (handled by the heading itself, since it contains the live text).
 *
 * @package PodNest
 */

defined( 'ABSPATH' ) || exit;

get_header();

$query = get_search_query();
?>

<div class="pn-page-hero">
    <div class="pn-container">
        <?php podnest_breadcrumbs(); ?>
        <span class="pn-eyebrow"><?php esc_html_e( 'Search Results', 'podnest' ); ?></span>
        <h1>
            <?php
            printf(
                /* translators: %s = search query */
                esc_html__( 'Results for: %s', 'podnest' ),
                '<span class="pn-gradient-text">' . esc_html( $query ) . '</span>'
            );
            ?>
        </h1>
    </div>
</div>

<div class="pn-container" style="padding-top:60px;padding-bottom:80px;">
    <?php if ( have_posts() ) : ?>

        <div class="pn-blog-grid">
            <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'pn-blog-card' ); ?>>
                <?php if ( has_post_thumbnail() ) : ?>
                <a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                    <?php the_post_thumbnail( 'podnest-thumb' ); ?>
                </a>
                <?php endif; ?>
                <div class="pn-blog-card-body">
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20, '…' ) ); ?></p>
                </div>
            </article>
            <?php endwhile; ?>
        </div>

        <?php the_posts_pagination( [
            'prev_text' => '← ' . __( 'Newer', 'podnest' ),
            'next_text' => __( 'Older', 'podnest' ) . ' →',
            'class'     => 'pn-pagination',
        ] ); ?>

    <?php else : ?>
        <div style="text-align:center;padding:80px 0;">
            <p class="pn-lead">
                <?php printf(
                    esc_html__( 'No results found for &ldquo;%s&rdquo;. Try different keywords.', 'podnest' ),
                    esc_html( $query )
                ); ?>
            </p>
            <?php get_search_form(); ?>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
