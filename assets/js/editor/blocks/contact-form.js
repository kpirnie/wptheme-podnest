/**
 * Contact Form Block Definition
 *
 * Registers the `podnest/contact-form` block. The editor shows a
 * placeholder; the front end is rendered server-side by
 * PodNest_Blocks::render_contact_form().
 *
 * @module editor/blocks/contact-form
 */

import { createPreview } from '../utils/preview.js';

const { registerBlockType } = window.wp.blocks;
const { useBlockProps }     = window.wp.blockEditor;
const { __ }                = window.wp.i18n;

/**
 * Registers the Contact Form block.
 *
 * @returns {void}
 */
export function registerContactFormBlock() {
    registerBlockType( 'podnest/contact-form', {
        title:       __( 'PodNest: Contact Form', 'podnest' ),
        description: __( 'Renders the PodNest AJAX contact form with reCAPTCHA support.', 'podnest' ),
        icon:        'email',
        category:    'podnest',
        attributes:  {},

        /**
         * @returns {import('@wordpress/element').WPElement}
         */
        edit() {
            return createPreview(
                useBlockProps(),
                '✉️',
                __( 'Contact Form', 'podnest' ),
                __( 'Renders the AJAX contact form. Configure reCAPTCHA under Appearance → Customize → PodNest Settings.', 'podnest' )
            );
        },

        save: () => null,
    } );
}