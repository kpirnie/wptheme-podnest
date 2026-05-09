/**
 * Pricing Table Block Definition
 *
 * Registers the `podnest/pricing-table` block. No configurable attributes —
 * all settings live in the Pricing Tier CPT meta boxes.
 * Server-side rendered by PodNest_Blocks::render_pricing().
 *
 * @module editor/blocks/pricing
 */

import { createPreview } from '../utils/preview.js';

const { registerBlockType } = window.wp.blocks;
const { useBlockProps }     = window.wp.blockEditor;
const { __ }               = window.wp.i18n;

/**
 * Registers the Pricing Table block.
 *
 * @returns {void}
 */
export function registerPricingBlock() {
    registerBlockType( 'podnest/pricing-table', {
        title:       __( 'PodNest: Pricing Table', 'podnest' ),
        description: __( 'Support pricing tiers managed via the Pricing Tiers CPT.', 'podnest' ),
        icon:        'money-alt',
        category:    'podnest',
        attributes:  {},

        /**
         * @returns {import('@wordpress/element').WPElement}
         */
        edit() {
            return createPreview(
                useBlockProps(),
                '💰',
                __( 'Pricing Table', 'podnest' ),
                __( 'Renders published Pricing Tier posts as cards. Manage under PodNest Content → Pricing.', 'podnest' )
            );
        },

        save: () => null,
    } );
}
