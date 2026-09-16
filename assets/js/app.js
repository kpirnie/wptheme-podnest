/**
 * PodNest Theme — Frontend Application Entry Point
 *
 * Imports and initialises every UI module. Because this file is loaded
 * as type="module" (injected by PodNest_Assets::add_module_type), each
 * import path is resolved relative to this file's location by the browser's
 * native ES module loader — no bundler required.
 *
 * Module responsibilities:
 *  header      — Adds .pn-scrolled class to the fixed header on scroll.
 *  mobile-nav  — Wires the hamburger button ↔ slide-down mobile nav.
 *  scroll-reveal — IntersectionObserver-driven fade-in-up animations.
 *  terminal    — Types out the hero terminal lines with timed delays.
 *  marquee     — Duplicates the marquee track for a seamless loop.
 *  scroll-top  — Shows / hides the back-to-top button; handles click.
 *  smooth-scroll — Intercepts anchor clicks for smooth native scrolling.
 *  nav-active  — Highlights the active nav link as the user scrolls.
 *
 * @module app
 */

import { initContactForm } from './modules/contact-form.js';
import { initHeader } from './modules/header.js';
import { initInstructionsMenu } from './modules/instructions-menu.js';
import { initMarquee } from './modules/marquee.js';
import { initMobileNav } from './modules/mobile-nav.js';
import { initNavActive } from './modules/nav-active.js';
import { initScrollReveal } from './modules/scroll-reveal.js';
import { initScrollTop } from './modules/scroll-top.js';
import { initSmoothScroll } from './modules/smooth-scroll.js';
import { initTerminal } from './modules/terminal.js';

/**
 * Bootstrap all UI modules once the DOM is fully parsed.
 *
 * DOMContentLoaded fires before images and stylesheets finish loading,
 * so this is the right hook for DOM manipulation — no need to wait for
 * the full load event.
 */
document.addEventListener( 'DOMContentLoaded', () => {
    initHeader();
    initMobileNav();
    initScrollReveal();
    initTerminal();
    initMarquee();
    initScrollTop();
    initSmoothScroll();
    initNavActive();
    initContactForm();
    initInstructionsMenu();
} );
