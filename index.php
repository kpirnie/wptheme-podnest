<?php
/**
 * Index / Blog Archive Fallback
 *
 * WordPress uses this file when a more specific template is not found.
 * In practice, front-page.php handles the homepage and archive.php
 * handles post archives, so this is a safety net.
 *
 * @package PodNest
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="pn-page-hero">
    <div class="pn-container">
        <h1>
            <?php
            if ( is_home() && ! is_front_page() ) {
                echo esc_html( get_the_title( get_option( 'page_for_posts' ) ) ?: __( 'Blog', 'podnest' ) );
            } else {
                bloginfo( 'name' );
            }
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
                    <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" class="pn-blog-date">
                        <?php echo esc_html( get_the_date() ); ?>
                    </time>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22, '…' ) ); ?></p>
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
        <p class="pn-muted"><?php esc_html_e( 'No posts found.', 'podnest' ); ?></p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
