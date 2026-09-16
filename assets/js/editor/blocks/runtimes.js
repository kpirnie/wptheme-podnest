/**
 * Runtimes Grid Block Definition
 *
 * Registers the `podnest/runtimes-grid` block. Configurable column
 * count (2–5). Server-side rendered by PodNest_Blocks::render_runtimes().
 *
 * @module editor/blocks/runtimes
 */

import { createPreview } from '../utils/preview.js';

const { registerBlockType } = window.wp.blocks;
const { useBlockProps, InspectorControls } = window.wp.blockEditor;
const { PanelBody, RangeControl } = window.wp.components;
const { __ }  = window.wp.i18n;
const el      = window.wp.element.createElement;
const Fragment = window.wp.element.Fragment;

/**
 * Registers the Runtimes Grid block.
 *
 * @returns {void}
 */
export function registerRuntimesBlock() {
    registerBlockType( 'podnest/runtimes-grid', {
        title:       __( 'PodNest: Runtimes Grid', 'podnest' ),
        description: __( 'A grid of supported runtime/site-type cards managed via the Runtimes CPT.', 'podnest' ),
        icon:        'desktop',
        category:    'podnest',
        attributes: {
            /** Number of columns (2–5). Default matches the 5-type layout. */
            columns: { type: 'number', default: 4 },
        },

        /**
         * @param {object}   props
         * @param {object}   props.attributes
         * @param {Function} props.setAttributes
         * @returns {import('@wordpress/element').WPElement}
         */
        edit( { attributes, setAttributes } ) {
            const { columns } = attributes;
            return el(
                Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __( 'Grid Settings', 'podnest' ), initialOpen: true },
                        el( RangeControl, {
                            label:    __( 'Columns', 'podnest' ),
                            value:    columns,
                            onChange: value => setAttributes( { columns: value } ),
                            min:      2,
                            max:      5,
                        } )
                    )
                ),
                createPreview(
                    useBlockProps(),
                    '🖥️',
                    `Runtimes Grid (${ columns } columns)`,
                    __( 'Renders published Runtime posts. Manage under PodNest Content → Runtimes.', 'podnest' )
                )
            );
        },

        save: () => null,
    } );
}
