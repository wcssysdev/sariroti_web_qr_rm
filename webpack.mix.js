const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.styles([
	'public/assets/plugins/global/plugins.bundle.css',
	'public/assets/plugins/custom/prismjs/prismjs.bundle.css',
	'public/assets/css/style.bundle.css',
], 
'public/assets/mix/mix_global.css').version();

mix.scripts([
	'public/assets/plugins/global/plugins.bundle.js',
	'public/assets/plugins/custom/prismjs/prismjs.bundle.js',
	'public/assets/js/scripts.bundle.js'
],
'public/assets/mix/mix_global.js').version();