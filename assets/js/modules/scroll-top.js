/**
 * Scroll-to-Top Button
 *
 * Shows the `#pn-scroll-top` button once the user has scrolled down
 * far enough (defined by SHOW_AFTER_PX), and smoothly returns to the
 * top of the page when clicked.
 *
 * The button markup is rendered by `podnest_scroll_top_button()` in
 * inc/helpers.php and placed just before </body> in footer.php.
 *
 * @module modules/scroll-top
 */

/** Show the button after this many pixels of scroll. */
const SHOW_AFTER_PX = 400;

/**
 * Initialises the scroll-to-top button behaviour.
 *
 * No-ops if the button element is not present in the DOM.
 *
 * @returns {void}
 */
export function initScrollTop() {
    const btn = document.getElementById( 'pn-scroll-top' );

    if ( ! btn ) {
        return;
    }

    /**
     * Toggles the button's visible state based on scroll position.
     * CSS transitions handle the fade-in/out via .pn-scroll-top--visible.
     */
    const handleScroll = () => {
        btn.classList.toggle( 'pn-scroll-top--visible', window.scrollY > SHOW_AFTER_PX );
    };

    /* Set initial state without waiting for first scroll */
    handleScroll();

    window.addEventListener( 'scroll', handleScroll, { passive: true } );

    /* Smooth scroll to top on click */
    btn.addEventListener( 'click', () => {
        window.scrollTo( { top: 0, behavior: 'smooth' } );
    } );
}
