/**
 * Header Scroll Behaviour
 *
 * Adds the `.pn-scrolled` class to `#site-header` once the user scrolls
 * past 40px. The CSS uses this class to apply the frosted-glass background,
 * border-bottom, and backdrop-blur — keeping the header transparent at the
 * top of the page where the hero background shows through.
 *
 * Uses `passive: true` on the scroll listener to avoid blocking the main
 * thread, which is critical for smooth 60fps scrolling on mobile.
 *
 * @module modules/header
 */

/**
 * Initialises the scroll-aware header behaviour.
 *
 * No-ops silently if `#site-header` is not present in the DOM (e.g. on
 * error pages or custom templates that omit the standard header).
 *
 * @returns {void}
 */
export function initHeader() {
    const header = document.getElementById( 'site-header' );

    if ( ! header ) {
        return;
    }

    /**
     * Toggles the `.pn-scrolled` class based on the current scroll position.
     * Extracted as a named function so it can be called immediately on init
     * (handles the case where the page loads mid-scroll, e.g. after a hash nav).
     */
    const handleScroll = () => {
        header.classList.toggle( 'pn-scrolled', window.scrollY > 40 );
    };

    /* Apply correct state immediately — don't wait for first scroll */
    handleScroll();

    /* Listen for subsequent scroll events with passive flag for performance */
    window.addEventListener( 'scroll', handleScroll, { passive: true } );
}
