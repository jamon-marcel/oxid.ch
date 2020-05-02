const mix = require('laravel-mix');

mix.webpackConfig({
    resolve: {
        extensions: ['.js', '.vue', '.json'],
        alias: {
            //'vue$': 'vue/dist/vue.esm.js',
            '@': __dirname + '/resources/js/backend/'
        },
    },
});

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

// Backend
mix.js('resources/js/backend/app.js', 'public/assets/backend/js');
mix.sass('resources/sass/backend/app.scss', 'public/assets/backend/css').options({processCssUrls: false});

// Frontend
mix.js('resources/js/frontend/app.js', 'public/assets/js').version();
mix.js('resources/js/frontend/maps.js', 'public/assets/js').version();
mix.sass('resources/sass/frontend/app.scss', 'public/assets/css').options({processCssUrls: false}).version();
