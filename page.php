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
        <div class="pn-container">
            <h1><?php the_title(); ?></h1>
        </div>
    </div>

    <div class="pn-content-area">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <?php
            the_content();

            ?>
        </article>
    </div>

<?php
endif;

// get the footer
get_footer();
