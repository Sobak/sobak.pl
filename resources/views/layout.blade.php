<!DOCTYPE html>
<html lang="{{ $language or config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title or config('app.name') }}</title>
    <link rel="stylesheet" href="{{ mix('assets/css/app.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,300italic,400italic,600italic&amp;subset=latin,latin-ext">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=PT+Serif:400,700,400italic,700italic&amp;subset=latin,latin-ext">
</head>

<body class="right-sidebar {{ implode(' ', $body_classes ?? []) }}">
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
            <a class="screen-reader-text" href="#content">Przejdź do treści</a>

            <div class="menu-main-container">
                <ul id="menu-main">
                    <li {!! if_active(['index', 'blog', 'post', 'category', 'tag', 'search']) !!}>
                        <a href="{{ route('index') }}">Blog</a>
                    </li>
                    <li {!! if_active(['projects', 'project']) !!}>
                        <a href="{{ route('projects') }}">Portfolio</a>
                    </li>
                    <li {!! if_active('page:o-mnie') !!}>
                        <a href="{{ route('page', ['o-mnie']) }}">O mnie</a>
                    </li>
                    <li {!! if_active('contact') !!}>
                        <a href="{{ route('contact') }}">Kontakt</a>
                    </li>
                </ul>
            </div>
        </nav><!-- #site-navigation -->
    </div>
    <div id="social-links-toggle-nav" class="panel">
        <div class="social-links">
            <ul id="menu-social">
                <li><a href="https://www.linkedin.com/in/msobaczewski/"><span class="screen-reader-text">LinkedIn</span></a></li>
                <li><a href="https://twitter.com/SobakPL"><span class="screen-reader-text">Twitter</span></a></li>
                <li><a href="https://github.com/Sobak"><span class="screen-reader-text">GitHub</span></a></li>
                <li><a href="https://facebook.com/SobakPL"><span class="screen-reader-text">Facebook</span></a></li>
                <li><a href="http://sobak.pl/feed/"><span class="screen-reader-text">RSS</span></a></li>
            </ul>
        </div>
    </div>
    <div id="search-toggle-nav" class="panel">
        <div class="search-wrapper">
            <form role="search" method="get" class="search-form" action="{{ route('search') }}">
                <label>
                    <span class="screen-reader-text">Wyszukiwanie:</span>
                    <input type="search" name="search" placeholder="Wpisz szukany tekst…">
                </label>
                <input type="submit" value="Szukaj">
            </form>
        </div>
    </div>
    <div id="content" class="site-content">
        <div class="content-area">
            <main id="main" class="site-main">

                @yield('content')

            </main>
        </div>

        @include ('partials.sidebar')
    </div>

    <footer class="site-footer" role="contentinfo">
        <div class="site-info">
            <a href="https://github.com/Sobak/homepage" title="Napędzane silnikiem Perception">Perception</a>
            <span class="sep"> | </span>
            <a href="http://wordpress.com/themes/sorbet/" title="Motyw Sorbet od Automattic">Sorbet</a>
            <span class="sep"> | </span>
            <a href="{{ route('page', ['polityka-prywatnosci']) }}" title="Zapoznaj się z polityką prywatności">Polityka prywatności</a>
        </div><!-- .site-info -->
    </footer>

@include('partials.analytics')

<script src="{{ mix('assets/js/bundle.js') }}"></script>
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
