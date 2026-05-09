/**
 * PodNest Block Editor Entry Point
 *
 * Imports each block definition module and registers all four blocks
 * with the WordPress block editor. This file is enqueued as a non-module
 * script via PodNest_Assets::enqueue_editor() because it runs in the WP
 * admin context where wp.blocks, wp.element, etc. are AMD globals.
 *
 * The block definition files themselves use ES module `import` syntax —
 * this works because they are loaded as sub-modules through this entry
 * point, which the browser resolves natively via type="module" that PHP
 * injects via the script_loader_tag filter.
 *
 * Execution is deferred until the DOM is ready via DOMContentLoaded to
 * ensure all WP globals are in scope before registerBlockType runs.
 *
 * @module editor/index
 */

import { registerMarqueeBlock }  from './blocks/marquee.js';
import { registerFeaturesBlock } from './blocks/features.js';
import { registerRuntimesBlock } from './blocks/runtimes.js';
import { registerPricingBlock }  from './blocks/pricing.js';

/**
 * Register all blocks once the document and WP globals are ready.
 */
document.addEventListener( 'DOMContentLoaded', () => {
    registerMarqueeBlock();
    registerFeaturesBlock();
    registerRuntimesBlock();
    registerPricingBlock();
} );
