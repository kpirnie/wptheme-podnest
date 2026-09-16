/**
 * Instructions Menu — Mobile Collapse
 *
 * Turns the sidebar "Instructions" navigation into a hamburger-style
 * toggle on narrow viewports, mirroring the main nav's mobile behavior.
 * Targets the nav by its aria-label so it works on both the intro Page
 * and single instruction templates without markup changes.
 *
 * @module modules/instructions-menu
 */

/**
 * Initialises the collapsible instructions menu.
 *
 * No-ops if the instructions nav is not present on the page.
 *
 * @returns {void}
 */
export function initInstructionsMenu() {
    const nav = document.querySelector( 'nav[aria-label="Instructions"]' );

    if ( ! nav ) {
        return;
    }

    /* Scope mobile styling to the containing column */
    const wrap = nav.closest( '.wp-block-column' ) || nav.parentElement;
    wrap.classList.add( 'pn-docs-menu' );

    if ( ! nav.id ) {
        nav.id = 'pn-docs-nav';
    }

    /* Build and inject the toggle button before the nav */
    const toggle = document.createElement( 'button' );
    toggle.type = 'button';
    toggle.className = 'pn-docs-menu-toggle';
    toggle.setAttribute( 'aria-expanded', 'false' );
    toggle.setAttribute( 'aria-controls', nav.id );
    toggle.innerHTML =
        '<span>Menu</span>'
        + '<span class="pn-docs-menu-burger" aria-hidden="true">'
        + '<span></span><span></span><span></span></span>';

    nav.parentNode.insertBefore( toggle, nav );

    /* Toggle open/closed state */
    toggle.addEventListener( 'click', () => {
        const isOpen = wrap.classList.toggle( 'pn-open' );
        toggle.classList.toggle( 'active', isOpen );
        toggle.setAttribute( 'aria-expanded', String( isOpen ) );
    } );
}