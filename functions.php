<?php

/**
 * PodNest Theme Bootstrap
 *
 * This file is intentionally minimal. All feature logic lives in
 * dedicated classes under /inc. This keeps functions.php a clean
 * composition root rather than a dumping ground.
 *
 * Load order:
 *  1. Constants
 *  2. Helpers  (procedural functions required by templates)
 *  3. Classes  (autoloaded via podnest_load_class())
 *  4. Theme    (singleton that wires everything together)
 *
 * @package PodNest
 * @version 1.1.0
 * @author  Kevin Pirnie <iam@kevinpirnie.com>
 */

defined('ABSPATH') || exit;

/* -- 1. Constants ---------------------------------------------- */

/** Theme semantic version — used as cache-bust key for all enqueued assets. */
define('PODNEST_VERSION', '1.1.0');

/** Absolute filesystem path to the theme root (no trailing slash). */
define('PODNEST_DIR', get_template_directory());

/** Public URI to the theme root (no trailing slash). */
define('PODNEST_URI', get_template_directory_uri());

/* -- 2. Helpers ------------------------------------------------ */

require_once PODNEST_DIR . '/inc/helpers.php';

/* -- 3. Class autoloader --------------------------------------- */

/**
 * Loads a theme class file from /inc.
 *
 * Convention: class PodNest_Foo lives in inc/class-foo.php.
 * Keeps all requires centralised and easy to audit.
 *
 * @param string $class_name Class suffix without the PodNest_ prefix, e.g. 'Assets'.
 * @return void
 */
function podnest_load_class(string $class_name): void
{
    $file = PODNEST_DIR . '/inc/class-' . strtolower(str_replace('_', '-', $class_name)) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
}

/*
 * Ordered load list — Nav_Walker and Breadcrumbs load first because
 * other classes (and templates) may instantiate them directly.
 */
$podnest_classes = [
    'Nav_Walker',
    'Social_Walker',
    'Breadcrumbs',
    'Customizer',
    'Assets',
    'SEO',
    'CPTs',
    'Meta_Boxes',
    'Blocks',
    'Theme',        // Singleton; hooks after_setup_theme — load last.
];

foreach ($podnest_classes as $class) {
    podnest_load_class($class);
}

/* -- 4. Boot the theme singleton ------------------------------- */

PodNest_Theme::instance();
