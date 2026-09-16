<?php

/**
 * Instruction single template
 *
 * @package PodNest
 */

defined('ABSPATH') || exit;

// get the header
get_header();

if (have_posts()) :
    the_post();
?>

    <div class="pn-page-hero">
        <div class="pn-container">
            <h1><?php _e('Usage Instructions', 'podnest'); ?></h1>
            <?php
            if (function_exists('podnest_breadcrumbs')) {
                podnest_breadcrumbs();
            }
            ?>
        </div>
    </div>

    <div class="pn-container">
        <article id="post-<?php the_ID(); ?>" <?php post_class('pn-content-pad'); ?>>

            <div class="wp-block-columns is-layout-flex wp-block-columns-is-layout-flex">

                <div class="wp-block-column is-layout-flow wp-block-column-is-layout-flow" style="flex-basis:66.66%">
                    <h2 class="wp-block-heading"><?php the_title(); ?></h2>
                    <?php the_content(); ?>
                </div>

                <div class="wp-block-column is-layout-flow wp-block-column-is-layout-flow" style="flex-basis:33.33%">
                    <h2 class="wp-block-heading"><?php _e('Menu', 'podnest'); ?></h2>
                    <?php

                    // Render the synced pattern / reusable block in the sidebar.
                    // Look it up by slug so the ID isn't hard-coded.
                    $pn_nav = get_page_by_path('instruction-menu', OBJECT, 'wp_block');
                    if ($pn_nav instanceof WP_Post) {
                        echo do_blocks($pn_nav->post_content);
                    }
                    ?>
                </div>

            </div>
        </article>
    </div>

<?php
endif;

// get the footer
get_footer();
