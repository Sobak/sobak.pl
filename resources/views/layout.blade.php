<!DOCTYPE html>
{{-- @todo determine post language? --}}
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title or config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
    <link rel='stylesheet' id='sorbet-source-sans-pro-css'  href='http://fonts.googleapis.com/css?family=Source+Sans+Pro%3A300%2C400%2C700%2C300italic%2C400italic%2C700italic&#038;subset=latin%2Clatin-ext&#038;ver=4.9.1' type='text/css' media='all' />
    <link rel='stylesheet' id='sorbet-pt-serif-css'  href='http://fonts.googleapis.com/css?family=PT+Serif%3A400%2C700%2C400italic%2C700italic&#038;subset=latin%2Clatin-ext&#038;ver=4.9.1' type='text/css' media='all' />
</head>

<body class="right-sidebar {{ body_class() }}">
<div id="page" class="hfeed site">
    <header id="masthead" class="site-header" role="banner">
        <div class="site-header-wrapper">
            <div class="site-branding">
                <h1 class="site-title"><a href="{{ url('/') }}" rel="home">{{ config('app.name') }}</a></h1>
                <h2 class="site-description">{{ config('app.description') }}</h2>
            </div>

            <div class="toggles">
                <div id="menu-toggle" class="toggle active" title="Menu"><span class="screen-reader-text">Menu</span></div>
                <div id="social-links-toggle" class="toggle" title="Serwisy społecznościowe"><span class="screen-reader-text">Serwisy społecznościowe</span></div>
                <div id="search-toggle" class="toggle" title="Szukaj"><span class="screen-reader-text">Szukaj</span></div>
            </div>
        </div>
    </header><!-- #masthead -->
    <div id="menu-toggle-nav" class="panel">
        <nav id="site-navigation" class="main-navigation" role="navigation">
            <a class="skip-link screen-reader-text" href="#content">Przejdź do treści</a>

            <div class="menu-main-container">
                <ul id="menu-main" class="menu">
                    <li id="menu-item-1464" class="menu-item menu-item-type-custom menu-item-object-custom current-menu-item current_page_item menu-item-home menu-item-1464"><a href="{{ route('index') }}">Blog</a></li>
                    <li id="menu-item-1481" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1481"><a href="{{ route('projects') }}">Portfolio</a></li>
                    <li id="menu-item-1466" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1466"><a href="http://sobak.pl/o-mnie/">O mnie</a></li>
                    <li id="menu-item-1467" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1467"><a href="http://sobak.pl/kontakt/">Kontakt</a></li>
                </ul>
            </div>
        </nav><!-- #site-navigation -->
    </div>
    <div id="social-links-toggle-nav" class="panel">
        <div class="social-links">
            <ul id="menu-social" class="menu"><li id="menu-item-1470" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1470"><a href="http://github.com/Sobak"><span class="screen-reader-text">GitHub</span></a></li>
                <li id="menu-item-1468" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1468"><a href="http://twitter.com/SobakPL"><span class="screen-reader-text">Twitter</span></a></li>
                <li id="menu-item-1469" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1469"><a href="http://facebook.com/SobakPL"><span class="screen-reader-text">Facebook</span></a></li>
                <li id="menu-item-1650" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1650"><a href="http://sobak.pl/feed/"><span class="screen-reader-text">RSS</span></a></li>
            </ul>
        </div>
    </div>
    <div id="search-toggle-nav" class="panel">
        <div class="search-wrapper">
            <div class="search-wrapper">
                <form role="search" method="get" class="search-form" action="http://sobak.pl/">
                    <label>
                        <span class="screen-reader-text">Wyszukiwanie:</span>
                        <input type="search" class="search-field" placeholder="Wpisz szukany tekst&hellip;" value="" name="s">
                    </label>
                    <input type="submit" class="search-submit" value="Szukaj">
                </form>
            </div>
        </div>
    </div>
    <div id="content" class="site-content">
        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">

                @yield('content')

            </main>
        </div>
        <div id="secondary" class="widget-area" role="complementary">
            <aside id="twiget-widget-2" class="widget twiget-widget">
                <h1 class="widget-title">Mikroblog</h1>
                <div class="twiget-feed">
                    <ul id="twitter-widget-tweets" class="tweet-wrap">
                        <li><img src="{{ asset('assets/images/ajax-loader.gif') }}" width="16" height="16" alt="">Ładowanie tweetów...</li>
                    </ul>
                </div><!-- .twiget-feed -->
            </aside>

            <aside id="nav_menu-2" class="widget widget_nav_menu">
                <h1 class="widget-title">Linki</h1>
                <div class="menu-links-container">
                    <ul id="menu-links" class="menu">
                        <li id="menu-item-1472" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1472"><a href="http://m4tx.pl">Blog m4tx&#8217;a</a></li>
                        <li id="menu-item-1473" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1473"><a href="http://mrowqa.pl/">Mrowqa&#8217;s Blog</a></li>
                        <li id="menu-item-1474" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1474"><a href="http://rynko.pl">Rynko.pl</a></li>
                        <li id="menu-item-1475" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1475"><a href="http://webkrytyk.pl">WebKrytyk</a></li>
                    </ul>
                </div>
            </aside>
            <aside id="categories-3" class="widget widget_categories">
                <h1 class="widget-title">Kategorie</h1>
                <ul>
                    <li class="cat-item cat-item-259"><a href="http://sobak.pl/kategoria/html-css/" >HTML&amp;CSS</a> (3)</li>
                    <li class="cat-item cat-item-3"><a href="http://sobak.pl/kategoria/informacje/" >Informacje</a> (31)</li>
                    <li class="cat-item cat-item-260"><a href="http://sobak.pl/kategoria/inne/" >Inne</a> (1)</li>
                    <li class="cat-item cat-item-39"><a href="http://sobak.pl/kategoria/php/" >PHP</a> (50)</li>
                    <li class="cat-item cat-item-258"><a href="http://sobak.pl/kategoria/sql/" >SQL</a> (5)</li>
                    <li class="cat-item cat-item-4"><a href="http://sobak.pl/kategoria/technologia/" >Technologia</a> (56)</li>
                    <li class="cat-item cat-item-109"><a href="http://sobak.pl/kategoria/zycie/" >Życie</a> (34)</li>
                </ul>
            </aside>
        </div>
    </div>

    <footer id="colophon" class="site-footer" role="contentinfo">
        <div class="site-info">
            <a href="http://wordpress.org/" title="Napędzane WordPressem">WordPress</a>
            <span class="sep"> | </span>
            <a href="http://automattic.com/" title="Motyw Sorbet od Automattic">Sorbet</a>
            <span class="sep"> | </span>
            <a href="http://sobak.pl/polityka-prywatnosci/" title="Zapoznaj się z polityką prywatności">Polityka prywatności</a>
        </div><!-- .site-info -->
    </footer><!-- #colophon -->
</div><!-- #page -->

@include('partials.analytics')

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/menus.js') }}"></script>
<script src="{{ asset('assets/js/skip-link-focus-fix.js') }}"></script>
<script src="{{ asset('assets/js/twitter.js') }}"></script>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        var tweetOptions = {
            screen_name: 		'SobakPL',
            count: 				5,
        };

        $.get('{{ route('twitter.entries') }}', function (data) {
            twitterWidget(data, 'twitter-widget-tweets', tweetOptions);
        });
    });
</script>
@stack('footer_scripts')

</body>
</html>
