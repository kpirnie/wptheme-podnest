/**
 * Marquee Track Duplication
 *
 * The CSS marquee animation (`pn-marquee` keyframe) moves the track by
 * -50% translateX. For this to loop seamlessly the track must contain
 * exactly two identical sets of items so the second half is visually
 * identical to the first half when the first half scrolls off-screen.
 *
 * This module duplicates whatever HTML is already in `.pn-marquee-track`
 * (populated server-side from CPT posts or PHP fallback) so the loop
 * always works regardless of how many items are present.
 *
 * @module modules/marquee
 */

/**
 * Duplicates the marquee track content for a seamless CSS loop.
 *
 * @returns {void}
 */
export function initMarquee() {
    const track = document.querySelector( '.pn-marquee-track' );

    if ( ! track ) {
        return;
    }

    /*
     * Clone the current inner HTML and append it, giving us
     * [original items][clone items]. The CSS animation moves -50%,
     * which lands exactly at the start of the clone — seamless loop.
     */
    track.insertAdjacentHTML( 'beforeend', track.innerHTML );
}
