const mix = require('laravel-mix');
const del = require('del');

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

del(['public/assets/*']).then(function ($paths) {
});

mix.setPublicPath('public/');
mix.setResourceRoot('/..');

mix.sass('resources/assets/sass/app.scss', 'assets/mangapie.css')
    .js('resources/assets/js/app.js', 'assets/mangapie.js')
    .styles(
        [
            'public/assets/mangapie.css',
            'resources/assets/css/*.css',
        ],
        'public/assets/mangapie.css'
    );
