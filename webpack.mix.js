let mix = require('laravel-mix');

mix
    .options({
        clearConsole: false,
        processCssUrls: false,
    })

    .copyDirectory('resources/assets/fonts', 'public/assets/fonts')
    .copyDirectory('resources/assets/images', 'public/assets/images')

    .sass('resources/assets/sass/app.scss', 'public/assets/css')

    .scripts([
        'node_modules/jquery/dist/jquery.min.js',
        'resources/assets/js/menus.js',
        'resources/assets/js/portfolio.js',
        'resources/assets/js/skip-link-focus-fix.js'
    ], 'public/assets/js/bundle.js')

    .version();
