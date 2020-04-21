<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="stylesheet" href="{{ mix('assets/css/app.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,300italic,400italic,600italic|PT+Serif:400,400italic&amp;subset=latin,latin-ext">
    <link rel="alternate" href="{{ route('feed') }}" type="application/rss+xml" title="Kanał ze wpisami">
    @stack('header_extras')
</head>

<body class="right-sidebar {{ implode(' ', $body_classes ?? []) }}">
    <header class="site-header">
        <div class="site-header-wrapper">
            <div class="site-branding">
                <h1 class="site-title"><a href="{{ url('/') }}" rel="home">{{ config('app.name') }}</a></h1>
                <h2 class="site-description">{{ config('app.description') }}</h2>
            </div>

            <div class="toggles">
                <div id="menu-toggle" class="toggle active" title="Menu"><span class="sr-only">Menu</span></div>
                <div id="social-links-toggle" class="toggle" title="Social media"><span class="sr-only">Social media</span></div>
                <div id="search-toggle" class="toggle" title="Szukaj"><span class="sr-only">Szukaj</span></div>
            </div>
        </div>
    </header>
    <div id="menu-toggle-nav" class="panel">
        <nav class="main-navigation">
            <div class="sr-only">
                <a href="#content">Przejdź do treści</a><br>
                <a href="#sidebar">Przejdź do menu bocznego</a>
            </div>

            <ul>
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
        </nav>
    </div>
    <div id="social-links-toggle-nav" class="panel">
        <div class="social-links">
            <ul>
                <li><a href="https://www.linkedin.com/in/msobaczewski/"><span class="sr-only">LinkedIn</span></a></li>
                <li><a href="https://twitter.com/SobakPL"><span class="sr-only">Twitter</span></a></li>
                <li><a href="https://github.com/Sobak"><span class="sr-only">GitHub</span></a></li>
                <li><a href="https://facebook.com/SobakPL"><span class="sr-only">Facebook</span></a></li>
                <li><a href="{{ route('feed') }}"><span class="sr-only">RSS</span></a></li>
            </ul>
        </div>
    </div>
    <div id="search-toggle-nav" class="panel">
        <div class="search-wrapper">
            <form role="search" method="get" class="search-form" action="{{ route('search') }}">
                <label>
                    <span class="sr-only">Wyszukiwanie:</span>
                    <input type="search" name="q" placeholder="Wpisz szukany tekst…" required>
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

        @include('partials.sidebar')
    </div>

    <footer class="site-footer">
        <div class="site-info">
            <a href="https://github.com/Sobak/homepage" title="Napędzane silnikiem Perception">Perception</a>
            <a href="http://wordpress.com/themes/sorbet/" title="Motyw Sorbet od Automattic">Sorbet</a>
            <a href="{{ route('page', ['polityka-prywatnosci']) }}" title="Zapoznaj się z polityką prywatności">Polityka prywatności</a>
        </div>
    </footer>

@include('partials.analytics')

<script src="{{ mix('assets/js/bundle.js') }}"></script>

@if (cache()->has('twitter_entries') === false)
<script>
    $(function () {
        $.get('{{ route('twitter.entries') }}', function (data) {
            twitterWidget(data, '#twitter-widget-tweets');
        });
    });
</script>
@endif

@stack('footer_extras')

</body>
</html>
