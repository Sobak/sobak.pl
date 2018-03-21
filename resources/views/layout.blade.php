<!DOCTYPE html>
<html lang="{{ $language or config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title or config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,300italic,400italic,700italic&amp;subset=latin,latin-ext">
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=PT+Serif:400,700,400italic,700italic&amp;subset=latin,latin-ext">
</head>

<body class="right-sidebar {{ body_class() }}">
    <header class="site-header">
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
    </header>
    <div id="menu-toggle-nav" class="panel">
        <nav id="site-navigation" class="main-navigation">
            <a class="skip-link screen-reader-text" href="#content">Przejdź do treści</a>

            <div class="menu-main-container">
                <ul id="menu-main">
                    <li class="current-menu-item"><a href="{{ route('index') }}">Blog</a></li>
                    <li><a href="{{ route('projects') }}">Portfolio</a></li>
                    <li><a href="http://sobak.pl/o-mnie/">O mnie</a></li>
                    <li><a href="http://sobak.pl/kontakt/">Kontakt</a></li>
                </ul>
            </div>
        </nav><!-- #site-navigation -->
    </div>
    <div id="social-links-toggle-nav" class="panel">
        <div class="social-links">
            <ul id="menu-social">
                <li><a href="http://github.com/Sobak"><span class="screen-reader-text">GitHub</span></a></li>
                <li><a href="http://twitter.com/SobakPL"><span class="screen-reader-text">Twitter</span></a></li>
                <li><a href="http://facebook.com/SobakPL"><span class="screen-reader-text">Facebook</span></a></li>
                <li><a href="http://sobak.pl/feed/"><span class="screen-reader-text">RSS</span></a></li>
            </ul>
        </div>
    </div>
    <div id="search-toggle-nav" class="panel">
        <div class="search-wrapper">
            <div class="search-wrapper">
                <form role="search" method="get" class="search-form" action="http://sobak.pl/">
                    <label>
                        <span class="screen-reader-text">Wyszukiwanie:</span>
                        <input type="search" placeholder="Wpisz szukany tekst&hellip;" name="s">
                    </label>
                    <input type="submit" class="search-submit" value="Szukaj">
                </form>
            </div>
        </div>
    </div>
    <div id="content" class="site-content">
        <div id="primary" class="content-area">
            <main id="main" class="site-main">

                @yield('content')

            </main>
        </div>
        <div id="secondary" class="widget-area" role="complementary">
            <aside class="widget twiget-widget">
                <h1 class="widget-title">Mikroblog</h1>
                <div class="twiget-feed">
                    <ul id="twitter-widget-tweets" class="tweet-wrap">
                        <li><img src="{{ asset('assets/images/ajax-loader.gif') }}" width="16" height="16" alt="">Ładowanie tweetów...</li>
                    </ul>
                </div><!-- .twiget-feed -->
            </aside>

            <aside class="widget">
                <h1 class="widget-title">Linki</h1>
                <div class="menu-links-container">
                    <ul id="menu-links">
                        <li><a href="http://m4tx.pl">Blog m4tx'a</a></li>
                        <li><a href="http://mrowqa.pl/">Mrowqa's Blog</a></li>
                        <li><a href="http://rynko.pl">Rynko.pl</a></li>
                        <li><a href="http://webkrytyk.pl">WebKrytyk</a></li>
                    </ul>
                </div>
            </aside>
            <aside class="widget">
                <h1 class="widget-title">Kategorie</h1>
                <ul>
                    <li><a href="http://sobak.pl/kategoria/html-css/" >HTML&amp;CSS</a> (3)</li>
                    <li><a href="http://sobak.pl/kategoria/informacje/" >Informacje</a> (31)</li>
                    <li><a href="http://sobak.pl/kategoria/inne/" >Inne</a> (1)</li>
                    <li><a href="http://sobak.pl/kategoria/php/" >PHP</a> (50)</li>
                    <li><a href="http://sobak.pl/kategoria/sql/" >SQL</a> (5)</li>
                    <li><a href="http://sobak.pl/kategoria/technologia/" >Technologia</a> (56)</li>
                    <li><a href="http://sobak.pl/kategoria/zycie/" >Życie</a> (34)</li>
                </ul>
            </aside>
        </div>
    </div>

    <footer class="site-footer" role="contentinfo">
        <div class="site-info">
            <a href="http://wordpress.org/" title="Napędzane WordPressem">WordPress</a>
            <span class="sep"> | </span>
            <a href="http://automattic.com/" title="Motyw Sorbet od Automattic">Sorbet</a>
            <span class="sep"> | </span>
            <a href="http://sobak.pl/polityka-prywatnosci/" title="Zapoznaj się z polityką prywatności">Polityka prywatności</a>
        </div><!-- .site-info -->
    </footer>

@include('partials.analytics')

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/menus.js') }}"></script>
<script src="{{ asset('assets/js/skip-link-focus-fix.js') }}"></script>
<script src="{{ asset('assets/js/twitter.js') }}"></script>
<script>
    $(function () {
        $.get('{{ route('twitter.entries') }}', function (data) {
            twitterWidget(data, '#twitter-widget-tweets');
        });
    });
</script>
@stack('footer_scripts')

</body>
</html>
