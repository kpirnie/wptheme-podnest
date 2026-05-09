/**
 * Hero Terminal Type Effect
 *
 * Appends styled <div> elements to `#pn-terminal-output` on a timed
 * schedule to simulate a terminal session starting up. Each line has
 * a CSS class that maps to a colour (prompt, command, info, success).
 *
 * Only runs when the terminal element is present — safe to load on all pages.
 *
 * @module modules/terminal
 */

/**
 * @typedef {Object} TerminalLine
 * @property {string} text  — Line content.
 * @property {string} cls   — Space-separated CSS class(es) to apply.
 * @property {number} delay — Milliseconds from init before this line appears.
 */

/** @type {TerminalLine[]} */
const LINES = [
    { text: '$ podnest serve --port 8080',           cls: 'pn-term-prompt pn-term-cmd', delay: 400  },
    { text: '  PodNest starting — debug=false',       cls: 'pn-term-out',                delay: 800  },
    { text: '  host gateway detected: 10.88.0.1',    cls: 'pn-term-out',                delay: 1100 },
    { text: '  proxy domain cache warmed',            cls: 'pn-term-info',               delay: 1400 },
    { text: '  global SFTP container started',        cls: 'pn-term-ok',                 delay: 1700 },
    { text: '  global Fail2Ban container started',    cls: 'pn-term-ok',                 delay: 2000 },
    { text: '  PodNest management UI :8080',          cls: 'pn-term-info',               delay: 2300 },
    { text: '  proxy HTTPS listener :443',            cls: 'pn-term-info',               delay: 2600 },
    { text: '',                                        cls: '',                            delay: 2900 },
    { text: "$ # Site 'acme-corp' provisioned",       cls: 'pn-term-prompt pn-term-cmd', delay: 3200 },
    { text: '  ✓ MariaDB container started',          cls: 'pn-term-ok',                 delay: 3600 },
    { text: '  ✓ Redis container started',            cls: 'pn-term-ok',                 delay: 3900 },
    { text: '  ✓ PHP-FPM container started',          cls: 'pn-term-ok',                 delay: 4200 },
    { text: '  ✓ Nginx container started',            cls: 'pn-term-ok',                 delay: 4500 },
    { text: '  ✓ Pod running on :8081',               cls: 'pn-term-info',               delay: 4800 },
];

/**
 * Initialises the terminal type effect on `#pn-terminal-output`.
 *
 * @returns {void}
 */
export function initTerminal() {
    const terminal = document.getElementById( 'pn-terminal-output' );

    if ( ! terminal ) {
        return;
    }

    /* Remove the initial blinking cursor placeholder if present */
    const existingCursor = terminal.querySelector( '.pn-term-cursor' );
    if ( existingCursor ) {
        existingCursor.remove();
    }

    LINES.forEach( ( { text, cls, delay } ) => {
        setTimeout( () => {
            const div = document.createElement( 'div' );
            if ( cls ) div.className = cls;
            div.textContent = text;
            terminal.appendChild( div );

            /* Auto-scroll to keep latest line in view */
            terminal.scrollTop = terminal.scrollHeight;
        }, delay );
    } );
}
