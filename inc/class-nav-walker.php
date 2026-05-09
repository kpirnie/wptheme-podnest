<?php
/**
 * Custom Navigation Walker
 *
 * Overrides the default Walker_Nav_Menu to output nav links using
 * the theme's CSS class naming convention (pn-nav-link) instead of
 * WordPress's generic "menu-item" classes.
 *
 * This keeps the markup slim and ensures styling is driven entirely
 * by the theme's CSS rather than a mix of WP defaults and overrides.
 *
 * @package PodNest
 * @since   1.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class PodNest_Nav_Walker
 *
 * Used by get_nav_menu in header.php. Passed as:
 *   wp_nav_menu( [ 'walker' => new PodNest_Nav_Walker() ] )
 */
final class PodNest_Nav_Walker extends Walker_Nav_Menu {

    /**
     * Outputs the start of a nav menu item.
     *
     * Generates an <li> containing an <a> with appropriate theme classes.
     * Active state is detected from WP's 'current-menu-item' class.
     *
     * {@inheritdoc}
     *
     * @param  string   $output  Passed by reference — appended to by this method.
     * @param  WP_Post  $item    Menu item data object.
     * @param  int      $depth   Depth of menu item (0 = top-level).
     * @param  stdClass|null $args    wp_nav_menu() arguments.
     * @param  int      $id      Menu item ID.
     * @return void
     */
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ): void {
        $classes    = (array) ( $item->classes ?? [] );
        $is_active  = in_array( 'current-menu-item', $classes, true )
                   || in_array( 'current-menu-ancestor', $classes, true );

        /* Build the link attribute string safely */
        $atts = [
            'href'   => $item->url ?? '#',
            'class'  => 'pn-nav-link' . ( $is_active ? ' pn-active' : '' ),
        ];

        if ( ! empty( $item->attr_title ) ) {
            $atts['title'] = $item->attr_title;
        }
        if ( ! empty( $item->target ) ) {
            $atts['target'] = $item->target;
            /* Inject noopener for external _blank links (security best practice) */
            if ( '_blank' === $item->target ) {
                $atts['rel'] = trim( ( $item->xfn ?? '' ) . ' noopener noreferrer' );
            }
        }
        if ( ! empty( $item->xfn ) && '_blank' !== ( $item->target ?? '' ) ) {
            $atts['rel'] = $item->xfn;
        }

        /* Serialise attribute array to a string */
        $attr_string = '';
        foreach ( $atts as $k => $v ) {
            if ( '' !== $v ) {
                $attr_string .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
            }
        }

        $output .= '<li class="' . esc_attr( implode( ' ', array_filter( $classes ) ) ) . '">';
        $output .= '<a' . $attr_string . '>' . esc_html( $item->title ) . '</a>';
    }

    /**
     * Outputs the end of a nav menu item (closing </li>).
     *
     * {@inheritdoc}
     *
     * @param  string        $output Passed by reference.
     * @param  WP_Post       $item   Menu item data object (unused).
     * @param  int           $depth  Depth (unused).
     * @param  stdClass|null $args   wp_nav_menu() arguments (unused).
     * @return void
     */
    public function end_el( &$output, $item, $depth = 0, $args = null ): void {
        $output .= '</li>';
    }
}
