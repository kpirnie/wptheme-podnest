/**
 * Features Grid Block Definition
 *
 * Registers the `podnest/features-grid` block with a configurable
 * columns attribute (2–4). The editor shows a placeholder; the
 * front end is rendered server-side by PodNest_Blocks::render_features().
 *
 * @module editor/blocks/features
 */

import { createPreview } from '../utils/preview.js';

const { registerBlockType } = window.wp.blocks;
const { useBlockProps, InspectorControls } = window.wp.blockEditor;
const { PanelBody, RangeControl } = window.wp.components;
const { __ }  = window.wp.i18n;
const el      = window.wp.element.createElement;
const Fragment = window.wp.element.Fragment;

/**
 * Registers the Features Grid block.
 *
 * @returns {void}
 */
export function registerFeaturesBlock() {
    registerBlockType( 'podnest/features-grid', {
        title:       __( 'PodNest: Features Grid', 'podnest' ),
        description: __( 'A responsive grid of feature cards managed via the Features CPT.', 'podnest' ),
        icon:        'star-filled',
        category:    'podnest',
        attributes: {
            /** Number of columns to display (2–4). */
            columns: { type: 'number', default: 3 },
        },

        /**
         * Editor render function. Wraps the preview with an InspectorControls
         * panel so the user can adjust the column count from the sidebar.
         *
         * @param {object} props         - Block props.
         * @param {object} props.attributes   - Current attribute values.
         * @param {Function} props.setAttributes - Attribute setter.
         * @returns {import('@wordpress/element').WPElement}
         */
        edit( { attributes, setAttributes } ) {
            const { columns } = attributes;
            return el(
                Fragment,
                null,
                /* Sidebar inspector panel */
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
                            max:      4,
                        } )
                    )
                ),
                /* Block preview */
                createPreview(
                    useBlockProps(),
                    '⭐',
                    /* translators: %d = column count */
                    sprintf( __( 'Features Grid (%d columns)', 'podnest' ), columns ),
                    __( 'Renders published Feature posts as cards. Manage under PodNest Content → Features.', 'podnest' )
                )
            );
        },

        save: () => null,
    } );
}

/**
 * Minimal sprintf — formats a string with %d/%s placeholders.
 * Avoids importing a full library for a single use.
 *
 * @param {string}    fmt  - Format string.
 * @param {...*}      args - Replacement values.
 * @returns {string}
 */
function sprintf( fmt, ...args ) {
    let i = 0;
    return fmt.replace( /%[ds]/g, () => String( args[ i++ ] ?? '' ) );
}
