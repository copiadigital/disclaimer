const mix = require('laravel-mix');
require('laravel-mix-copy-watched');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Sage application. By default, we are compiling the Sass file
 | for your application, as well as bundling up your JS files.
 |
 */

mix
  .setPublicPath('./public');

mix
  .sass('resources/styles/disclaimer.scss', 'styles')
  .minify('public/styles/disclaimer.css');

  mix
  .copyWatched('resources/scripts/**', 'public/scripts')
  .minify('public/scripts/disclaimer.js');

mix
  .sourceMaps(false, 'source-map');