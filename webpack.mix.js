let mix = require('laravel-mix');

mix
    .copy('resources/assets/css/style.css', 'public/assets')
    .copyDirectory('resources/assets/fonts', 'public/assets/fonts')
    .copyDirectory('resources/assets/images', 'public/assets/images')

    .copy([
        'node_modules/jquery/dist/jquery.min.js',
        'resources/assets/js/menus.js',
        'resources/assets/js/portfolio.js',
        'resources/assets/js/skip-link-focus-fix.js'
    ], 'public/assets/js');
