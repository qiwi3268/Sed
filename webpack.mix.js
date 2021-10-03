const mix = require('laravel-mix');
const path = require('path');
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

mix.ts('resources/js/main.ts', 'public/dist/js/app.js')
   .vue()
   .version();


mix.webpackConfig({
   resolve: {
      extensions: ['.js', '.json', '.vue', '.ts', '.scss'],
      alias: {
         '@': path.join(__dirname, './resources/js')
      }
   }
});
