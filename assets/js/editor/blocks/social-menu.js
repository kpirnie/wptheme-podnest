/**
 * Social Menu Block Definition
 *
 * Registers the `podnest/social-menu` block. The editor shows a
 * placeholder; the front end is rendered server-side by
 * PodNest_Blocks::render_social_menu().
 *
 * @module editor/blocks/social-menu
 */

import { createPreview } from '../utils/preview.js';

const { registerBlockType }              = window.wp.blocks;
const { useBlockProps, InspectorControls } = window.wp.blockEditor;
const { PanelBody, SelectControl }       = window.wp.components;
const { __ }                             = window.wp.i18n;
const el                                 = window.wp.element.createElement;
const Fragment                           = window.wp.element.Fragment;

/**
 * Registers the Social Menu block.
 *
 * @returns {void}
 */
export function registerSocialMenuBlock() {
    registerBlockType( 'podnest/social-menu', {
        title:       __( 'PodNest: Social Menu', 'podnest' ),
        description: __( 'Renders the Social Links menu as SVG icon links.', 'podnest' ),
        icon:        'share',
        category:    'podnest',
        attributes: {
            align: { type: 'string', default: 'left' },
        },

        edit( { attributes, setAttributes } ) {
            const { align } = attributes;
            return el(
                Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __( 'Layout', 'podnest' ), initialOpen: true },
                        el( SelectControl, {
                            label:    __( 'Alignment', 'podnest' ),
                            value:    align,
                            options:  [
                                { label: __( 'Left',   'podnest' ), value: 'left'   },
                                { label: __( 'Center', 'podnest' ), value: 'center' },
                            ],
                            onChange: value => setAttributes( { align: value } ),
                        } )
                    )
                ),
                createPreview(
                    useBlockProps(),
                    '🔗',
                    __( 'Social Menu', 'podnest' ),
                    __( 'Renders the Social Links nav menu as SVG icons. Assign a menu under Appearance → Menus → Social Links.', 'podnest' )
                )
            );
        },

        save: () => null,
    } );
}