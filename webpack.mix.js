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

// Configure Vue
mix.vue({
    version: 2,
    extractStyles: false,
    globalStyles: false
});

// Configure Webpack
mix.webpackConfig({
    resolve: {
        extensions: ['.js', '.vue', '.json'],
        alias: {
            '@': __dirname + '/resources/js/backend/'
        },
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            }
        ]
    }
});

// Configure URL processing in SASS
mix.options({
    processCssUrls: false,
});

// Backend
mix.js('resources/js/backend/app.js', 'public/assets/backend/js');
mix.sass('resources/sass/backend/app.scss', 'public/assets/backend/css', {
    sassOptions: {
        outputStyle: 'compressed',
    }
});

// Frontend
mix.js('resources/js/frontend/app.js', 'public/assets/js').version();
mix.js('resources/js/frontend/maps.js', 'public/assets/js').version();
mix.sass('resources/sass/frontend/app.scss', 'public/assets/css', {
    sassOptions: {
        outputStyle: 'compressed',
    }
}).version();

// Frontend - Busu
mix.sass('resources/sass/frontend-busu/busu.scss', 'public/assets/css', {
    sassOptions: {
        outputStyle: 'compressed',
    }
}).version();
