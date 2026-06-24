/**
 * Changelog Block Definition
 *
 * Registers the `podnest/changelog` block. Shows the latest N commit
 * messages from the PodNest repo, or all of them. Server-side rendered
 * by PodNest_Blocks::render_changelog().
 *
 * @module editor/blocks/changelog
 */

import { createPreview } from '../utils/preview.js';

const { registerBlockType } = window.wp.blocks;
const { useBlockProps, InspectorControls } = window.wp.blockEditor;
const { PanelBody, RangeControl, ToggleControl } = window.wp.components;
const { __ }  = window.wp.i18n;
const el      = window.wp.element.createElement;
const Fragment = window.wp.element.Fragment;

/**
 * Registers the Changelog block.
 *
 * @returns {void}
 */
export function registerChangelogBlock() {
    registerBlockType( 'podnest/changelog', {
        title:       __( 'PodNest: Changelog', 'podnest' ),
        description: __( 'Latest commit messages pulled from the PodNest repository.', 'podnest' ),
        icon:        'list-view',
        category:    'podnest',
        attributes: {
            count:   { type: 'number',  default: 5 },
            showAll: { type: 'boolean', default: false },
        },

        /**
         * @param {object}   props
         * @param {object}   props.attributes
         * @param {Function} props.setAttributes
         * @returns {import('@wordpress/element').WPElement}
         */
        edit( { attributes, setAttributes } ) {
            const { count, showAll } = attributes;

            const controls = [
                el( ToggleControl, {
                    key:      'showall',
                    label:    __( 'Show all commits', 'podnest' ),
                    checked:  showAll,
                    onChange: value => setAttributes( { showAll: value } ),
                } ),
            ];

            /* Hide the count slider when showing everything */
            if ( ! showAll ) {
                controls.push( el( RangeControl, {
                    key:      'count',
                    label:    __( 'Number of commits', 'podnest' ),
                    value:    count,
                    onChange: value => setAttributes( { count: value } ),
                    min:      1,
                    max:      50,
                } ) );
            }

            return el(
                Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el( PanelBody, { title: __( 'Changelog Settings', 'podnest' ), initialOpen: true }, controls )
                ),
                createPreview(
                    useBlockProps(),
                    '📝',
                    showAll ? 'Changelog (all commits)' : `Changelog (latest ${ count })`,
                    __( 'Pulls commit messages from the PodNest repo. Cached daily.', 'podnest' )
                )
            );
        },

        save: () => null,
    } );
}
