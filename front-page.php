<?php

/**
 * Front Page — Marketing Homepage
 *
 * All dynamic sections (marquee, features, runtimes, pricing) are rendered
 * via {@see podnest_render_block()}, which calls the server-side render
 * callbacks in {@see PodNest_Blocks}. Content is managed entirely from
 * wp-admin under PodNest Content — no template edits required.
 *
 * Static sections (hero, architecture, security, CTA) pull copy from the
 * Customizer via {@see podnest_opt()} / {@see podnest_opt_url()}.
 *
 * @package PodNest
 */

defined('ABSPATH') || exit;

get_header();

// get the hero
get_template_part('template-parts/sections/hero');

// write out the marquee
echo podnest_render_block('podnest/marquee-strip');

// get the features
get_template_part('template-parts/sections/features');

// get the architecture
get_template_part('template-parts/sections/architecture');

// get the runtimes
get_template_part('template-parts/sections/runtimes');

// get the security
get_template_part('template-parts/sections/security');

// get the pricing table
get_template_part('template-parts/sections/pricing');

// get the CTA
get_template_part('template-parts/sections/cta');

// pull in the wp footer
get_footer();
