/**
 * Mobile Navigation Toggle
 *
 * Wires the hamburger button (#pn-hamburger) to show/hide the
 * slide-down mobile nav panel (#pn-mobile-nav).
 *
 * Accessibility considerations:
 *  - `aria-expanded` on the button reflects open/closed state.
 *  - `aria-controls` on the button references the nav panel ID.
 *  - Clicking any link inside the panel closes it (single-page nav).
 *  - The Escape key closes the panel when it is open.
 *
 * @module modules/mobile-nav
 */

/**
 * Initialises the mobile nav toggle.
 *
 * No-ops if either the hamburger button or the nav panel is absent.
 *
 * @returns {void}
 */
export function initMobileNav() {
    const hamburger = document.getElementById( 'pn-hamburger' );
    const mobileNav = document.getElementById( 'pn-mobile-nav' );

    if ( ! hamburger || ! mobileNav ) {
        return;
    }

    /**
     * Opens or closes the mobile nav panel.
     *
     * @param {boolean} [forceClose=false] When true, always closes.
     */
    const toggle = ( forceClose = false ) => {
        const isOpen = ! forceClose && ! mobileNav.classList.contains( 'open' );

        mobileNav.classList.toggle( 'open', isOpen );
        hamburger.classList.toggle( 'active', isOpen );
        hamburger.setAttribute( 'aria-expanded', String( isOpen ) );

        /* Prevent body scroll while nav is open on mobile */
        document.body.style.overflow = isOpen ? 'hidden' : '';
    };

    /* Primary toggle — hamburger click */
    hamburger.addEventListener( 'click', () => toggle() );

    /* Auto-close when any nav link is clicked */
    mobileNav.querySelectorAll( 'a' ).forEach( link => {
        link.addEventListener( 'click', () => toggle( true ) );
    } );

    /* Close on Escape key */
    document.addEventListener( 'keydown', event => {
        if ( event.key === 'Escape' && mobileNav.classList.contains( 'open' ) ) {
            toggle( true );
            hamburger.focus(); // Return focus to trigger for a11y
        }
    } );

    /* Close when clicking the overlay (outside the nav panel) */
    document.addEventListener( 'click', event => {
        const target = /** @type {Node} */ ( event.target );
        if (
            mobileNav.classList.contains( 'open' )
            && ! mobileNav.contains( target )
            && target !== hamburger
            && ! hamburger.contains( target )
        ) {
            toggle( true );
        }
    } );
}
