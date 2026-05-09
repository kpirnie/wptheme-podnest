/**
 * Smooth Scroll for In-Page Anchor Links
 *
 * Intercepts clicks on `<a href="#...">` links and scrolls smoothly
 * to the target element, accounting for the fixed header height so
 * the target is not obscured.
 *
 * Falls back gracefully: if the target element does not exist, the
 * default browser anchor behaviour is not prevented.
 *
 * @module modules/smooth-scroll
 */

/**
 * Initialises smooth scrolling for all in-page anchor links.
 *
 * @returns {void}
 */
export function initSmoothScroll() {
    document.querySelectorAll( 'a[href^="#"]' ).forEach( anchor => {
        anchor.addEventListener( 'click', event => {
            const href   = anchor.getAttribute( 'href' );
            const target = href ? document.querySelector( href ) : null;

            if ( ! target ) {
                /* No matching element — let the browser handle it */
                return;
            }

            event.preventDefault();

            /* Read the actual header height at click time (can change on resize) */
            const header = document.getElementById( 'site-header' );
            const offset = ( header ? header.offsetHeight : 80 ) + 16;

            const top = target.getBoundingClientRect().top + window.pageYOffset - offset;

            window.scrollTo( { top, behavior: 'smooth' } );
        } );
    } );
}
