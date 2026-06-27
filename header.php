<?php

/**
 * Site Header Template
 *
 * @package PodNest
 */
defined('ABSPATH') || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php wp_head(); ?>
    <link rel="icon" type="image/svg+xml" href="https://c.pdn.st/logos/podnest.svg" />
    <link rel="alternate icon" type="image/svg+xml" href="https://c.pdn.st/logos/podnest.svg" />
    <link rel="apple-touch-icon" href="https://c.pdn.st/logos/podnest.png" />
    <link rel="icon" href="https://c.pdn.st/logos/podnest.ico" sizes="any" />
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <a class="skip-link screen-reader-text" href="#main-content">
        <?php esc_html_e('Skip to content', 'podnest'); ?>
    </a>
    <?php
    // main navigation
    get_template_part('template-parts/navigation/main');

    // mobile navigation
    get_template_part('template-parts/navigation/mobile');
    ?>
    <main id="main-content">