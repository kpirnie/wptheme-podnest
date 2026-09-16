/**
 * PodNest build — CSS minify + JS bundle/minify
 * Output: assets/css/podnest.css  assets/js/podnest.js
 * Usage:  npm install && npm run build
 */

import * as esbuild from 'esbuild';

const watch = process.argv.includes( '--watch' );

/* Minify-only — theme.css has no @import, no bundling needed */
const css = {
    entryPoints: [ 'assets/css/theme.css' ],
    outfile:     'assets/css/podnest.css',
    bundle:      false,
    minify:      true,
    loader:      { '.css': 'css' },
};

/* Bundle all ES modules from app.js into a single IIFE */
const js = {
    entryPoints: [ 'assets/js/app.js' ],
    outfile:     'assets/js/podnest.js',
    bundle:      true,
    minify:      true,
    format:      'iife',
    target:      [ 'es2017' ],
};

const editorJs = {
    entryPoints: [ 'assets/js/editor/index.js' ],
    outfile:     'assets/js/editor/podnest-editor.js',
    bundle:      true,
    minify:      true,
    format:      'iife',
    target:      [ 'es2017' ],
};

if ( watch ) {
    const ctxs = await Promise.all( [ esbuild.context( css ), esbuild.context( js ) ] );
    await Promise.all( ctxs.map( c => c.watch() ) );
    console.log( 'Watching…' );
} else {
    await Promise.all( [ esbuild.build( css ), esbuild.build( js ), esbuild.build( editorJs ) ] );
    console.log( '✓ assets/css/podnest.css' );
    console.log( '✓ assets/js/podnest.js' );
    console.log( '✓ assets/js/editor/podnest-editor.js' );
}