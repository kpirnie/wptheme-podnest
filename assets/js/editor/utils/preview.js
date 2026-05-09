/**
 * Block Editor Preview Helper
 *
 * Returns a consistent styled placeholder element used by all four
 * PodNest blocks in the block editor. Because these are server-side-
 * rendered (dynamic) blocks, the editor does not show the actual
 * rendered output — it shows this placeholder instead to communicate
 * what the block is and where to manage its content.
 *
 * Uses vanilla `wp.element.createElement` (aliased as `el`) rather than
 * JSX since there is no build step.
 *
 * @module editor/utils/preview
 */

const el = window.wp.element.createElement;

/**
 * Creates a styled preview placeholder for a PodNest block.
 *
 * @param {object} blockProps - Return value of `useBlockProps()` from wp.blockEditor.
 * @param {string} icon       - Emoji or text icon displayed in the preview header.
 * @param {string} label      - Short label identifying the block (e.g. "Features Grid").
 * @param {string} description - One-sentence description of where content is managed.
 * @returns {import('@wordpress/element').WPElement} A React element.
 */
export function createPreview( blockProps, icon, label, description ) {
    return el(
        'div',
        Object.assign( {}, blockProps, {
            style: {
                background:   '#0c1530',
                border:       '1px solid #2a3f6a',
                borderRadius: '10px',
                padding:      '20px 24px',
                fontFamily:   '"JetBrains Mono", monospace',
                color:        '#dde8f5',
                userSelect:   'none',
            },
        } ),
        /* Header row: icon + label */
        el(
            'div',
            { style: { display: 'flex', alignItems: 'center', gap: '10px', marginBottom: '10px' } },
            el( 'span', { style: { fontSize: '1.4rem', lineHeight: 1 } }, icon ),
            el(
                'strong',
                {
                    style: {
                        color:         '#00D4FF',
                        fontSize:      '0.78rem',
                        letterSpacing: '0.12em',
                        textTransform: 'uppercase',
                    },
                },
                label
            )
        ),
        /* Description */
        el(
            'p',
            { style: { color: '#6b8cae', fontSize: '0.8rem', margin: 0, lineHeight: 1.5 } },
            description
        )
    );
}
