/**
 * Active Navigation Link Highlighting
 *
 * Adds the `.pn-nav-active` class to the nav link whose href matches
 * the section currently in the viewport. This gives users a visual
 * indicator of where they are on long single-page layouts.
 *
 * Only activates on pages that have both sections with IDs and nav links
 * with `href="#section-id"` patterns. Safe to run on all pages — no-ops
 * when either set of elements is absent.
 *
 * @module modules/nav-active
 */

/**
 * Initialises scroll-based active nav link highlighting.
 *
 * @returns {void}
 */
export function initNavActive() {
    /** All <section> elements with an id attribute. */
    const sections = document.querySelectorAll( 'section[id]' );

    /** Nav links that point to in-page anchors. */
    const navLinks = document.querySelectorAll( '.pn-nav-links a[href^="#"]' );

    if ( sections.length === 0 || navLinks.length === 0 ) {
        return;
    }

    /**
     * Determines which section is currently considered "active" —
     * defined as the last section whose top edge is at or above
     * 120px from the top of the viewport.
     */
    const highlightActive = () => {
        let currentId = '';

        sections.forEach( section => {
            if ( window.scrollY >= section.offsetTop - 120 ) {
                currentId = section.id;
            }
        } );

        navLinks.forEach( link => {
            const href = link.getAttribute( 'href' );
            link.classList.toggle( 'pn-nav-active', href === '#' + currentId );
        } );
    };

    /* Set correct initial state */
    highlightActive();

    window.addEventListener( 'scroll', highlightActive, { passive: true } );
}
