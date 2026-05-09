/**
 * Scroll Reveal Animations
 *
 * Uses IntersectionObserver to add the `.pn-visible` class to elements
 * marked with `.pn-reveal` as they enter the viewport. CSS transitions
 * on `.pn-reveal` and `.pn-visible` handle the actual fade-in-up effect,
 * keeping animation logic out of JavaScript.
 *
 * Degrades gracefully in environments without IntersectionObserver
 * (old browsers, screen readers, no-JS) by immediately adding `.pn-visible`
 * to all reveal elements.
 *
 * @module modules/scroll-reveal
 */

/** Fraction of the element that must be visible before triggering. */
const THRESHOLD = 0.12;

/**
 * Number of pixels from the bottom of the viewport where the observer
 * fires early — creates a slight "peek ahead" effect.
 */
const ROOT_MARGIN = '0px 0px -40px 0px';

/**
 * Initialises scroll-triggered reveal animations for all `.pn-reveal` elements.
 *
 * Each element is unobserved after it becomes visible so the observer
 * does not keep running indefinitely.
 *
 * @returns {void}
 */
export function initScrollReveal() {
    const elements = document.querySelectorAll( '.pn-reveal' );

    if ( elements.length === 0 ) {
        return;
    }

    /* Graceful degradation: reveal everything immediately if no observer support */
    if ( ! ( 'IntersectionObserver' in window ) ) {
        elements.forEach( el => el.classList.add( 'pn-visible' ) );
        return;
    }

    const observer = new IntersectionObserver(
        entries => {
            entries.forEach( entry => {
                if ( entry.isIntersecting ) {
                    entry.target.classList.add( 'pn-visible' );
                    /* Unobserve once revealed — the animation only plays once */
                    observer.unobserve( entry.target );
                }
            } );
        },
        { threshold: THRESHOLD, rootMargin: ROOT_MARGIN }
    );

    elements.forEach( el => observer.observe( el ) );
}
