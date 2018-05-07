const mix = require('laravel-mix');
const fs = require('fs');

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

mix.setResourceRoot('./public/');

mix.js('resources/assets/js/app.js', 'public/assets/mangapie.js')
   .sass('resources/assets/sass/app.scss', 'public/assets');

mix.styles([ 'public/assets/*.css', 'resources/assets/css/*.css' ], 'public/assets/mangapie.css');
