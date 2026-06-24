<?php

/**
 * Default Page Template
 *
 * Used for all pages that do not have a specific page template assigned.
 * Outputs the page title via pn-page-hero and renders the block-editor
 * content in the constrained pn-content-area wrapper.
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
        <div class="pn-container nobtmpad">
            <h1><?php the_title(); ?></h1>
            <?php
            if (function_exists('podnest_breadcrumbs')) {
                podnest_breadcrumbs();
            }
            ?>
        </div>
    </div>

    <div class="pn-container">
        <article id="post-<?php the_ID(); ?>" <?php post_class('pn-content-pad'); ?>>
            <?php
            the_content();

            ?>
        </article>
    </div>

<?php
endif;

// get the footer
get_footer();
