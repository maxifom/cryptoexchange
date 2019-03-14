let mix = require('laravel-mix');

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

mix.js('resources/assets/js/depositHistory.js', 'public/js')
    .js('resources/assets/js/tradeHistory.js', 'public/js')
    .js('resources/assets/js/withdrawalHistory.js', 'public/js')
    .js('resources/assets/js/wallets.js', 'public/js')
    .js('resources/assets/js/market.js', 'public/js')
    .js('resources/assets/js/markets.js', 'public/js')
    .js('resources/assets/js/trades.js', 'public/js')
    .js('resources/assets/js/settings.js', 'public/js');

