const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application as well as publishing vendor resources.
 |
 */

elixir(mix => {
    mix
        .sass('main.scss')
        .sass('admin.scss')
        .sass('tinymce.scss')
        .sass('epay-mobile.scss')
        .webpack('main.js')
        .webpack('admin.js')
        .webpack('media-preview.js')
        .webpack('media-upload.js')
        .webpack('vendor/masonry.js')
        .webpack('vendor/tinymce.js')
        .webpack('vendor/tinymce-smileys.js')
});
