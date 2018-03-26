let mix = require('laravel-mix');

mix
    .options({
        clearConsole: false,
    })

    .copy('resources/assets/css/style.css', 'public/assets/css')
    .copyDirectory('resources/assets/fonts', 'public/assets/fonts')
    .copyDirectory('resources/assets/images', 'public/assets/images')

    .scripts([
        'node_modules/jquery/dist/jquery.min.js',
        'resources/assets/js/menus.js',
        'resources/assets/js/portfolio.js',
        'resources/assets/js/skip-link-focus-fix.js',
        'resources/assets/js/twitter.js'
    ], 'public/assets/js/bundle.js');
