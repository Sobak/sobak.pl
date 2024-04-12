<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="stylesheet" href="{{ mix('assets/css/app.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,300italic,400italic,600italic|PT+Serif:400,400italic&amp;subset=latin,latin-ext">
    <link rel="alternate" href="{{ route('feed.all') }}" type="application/rss+xml" title="{{ __('app.rss_entries_all_title') }}">
    <link rel="alternate" href="{{ route('feed.pl') }}" type="application/rss+xml" title="{{ __('app.rss_entries_pl_title') }}">
    <link rel="alternate" href="{{ route('feed.en') }}" type="application/rss+xml" title="{{ __('app.rss_entries_en_title') }}">
    @stack('header_extras')
</head>

<body class="right-sidebar {{ implode(' ', $body_classes ?? []) }}">
    <header class="site-header">
        <div class="site-header-wrapper">
            <div class="site-branding">
                <h1 class="site-title"><a href="{{ url('/') }}" rel="home">{{ __('app.branding.name') }}</a></h1>
                <h2 class="site-description">{{ __('app.branding.description') }}</h2>
            </div>

            <div class="toggles">
                <div id="menu-toggle" class="toggle active" title="{{ __('app.menu.section_menu') }}"><span class="sr-only">{{ __('app.menu.section_menu') }}</span></div>
                <div id="social-links-toggle" class="toggle" title="{{ __('app.menu.section_social_links') }}"><span class="sr-only">{{ __('app.menu.section_social_links') }}</span></div>
                <div id="search-toggle" class="toggle" title="{{ __('app.menu.section_search') }}"><span class="sr-only">{{ __('app.menu.section_search') }}</span></div>
                <div id="language-toggle" class="toggle">
                    <a href="{{ route('language_switch', ['lang' => $oppositeLanguage]) }}" title="{{ __("app.menu.language_$oppositeLanguage") }}">
                        <img src="{{ asset("assets/images/$oppositeLanguage.svg") }}" width="50" height="50" alt="{{ $oppositeLanguage }}">
                    </a>
                </div>
            </div>
        </div>
    </header>
    <div id="menu-toggle-nav" class="panel">
        <nav class="main-navigation">
            <div class="sr-only">
                <a href="#content">{{ __('app.jump_to_content') }}</a><br>
                <a href="#sidebar">{{ __('app.jump_to_sidebar') }}</a>
            </div>

            <ul>
                <li {!! if_active(['index', 'blog', 'post', 'category', 'tag', 'search']) !!}>
                    <a href="{{ route('index') }}">{{ __('app.menu.blog') }}</a>
                </li>
                <li {!! if_active(['projects', 'project']) !!}>
                    <a href="{{ route('projects') }}">{{ __('app.menu.projects') }}</a>
                </li>
                <li {!! if_active('page:o-mnie') !!}>
                    <a href="{{ localized_page_route('o-mnie') }}">{{ __('app.menu.about_me') }}</a>
                </li>
                <li {!! if_active('contact') !!}>
                    <a href="{{ route('contact') }}">{{ __('app.menu.contact') }}</a>
                </li>
            </ul>
        </nav>
    </div>
    <div id="social-links-toggle-nav" class="panel">
        <div class="social-links">
            <ul>
                <li><a href="https://www.linkedin.com/in/msobaczewski/" title="LinkedIn"><span class="sr-only">LinkedIn</span></a></li>
                <li><a href="https://github.com/Sobak" title="GitHub"><span class="sr-only">GitHub</span></a></li>
                <li><a href="https://twitter.com/SobakPL" title="Twitter"><span class="sr-only">Twitter</span></a></li>
                <li>
                    <a href="#" class="feed" title="{{ __('app.rss_entries_tooltip') }}"><span class="sr-only">RSS</span></a>
                    <ul>
                        <li><a href="{{ route('feed.all') }}">{{ __('app.rss_entries_all_title') }}</a></li>
                        <li><a href="{{ route('feed.pl') }}">{{ __('app.rss_entries_pl_title') }}</a></li>
                        <li><a href="{{ route('feed.en') }}">{{ __('app.rss_entries_en_title') }}</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div id="search-toggle-nav" class="panel">
        <div class="search-wrapper">
            <form role="search" method="get" class="search-form" action="{{ route('search') }}">
                <label>
                    <span class="sr-only">{{ __('app.search.label') }}:</span>
                    <input type="search" name="q" placeholder="{{ __('app.search.placeholder') }}" value="{{ request()->query('q') }}" required>
                </label>
                <button>{{ __('app.search.button') }}</button>
            </form>
        </div>
    </div>
    <div id="content" class="site-content @yield('site-content-extra-classes')">
        <div class="content-area">
            <main id="main" class="site-main">

                @yield('content')

            </main>
        </div>

        @include('partials.sidebar')
    </div>

    <footer class="site-footer">
        <div class="site-info">
            <a href="https://github.com/Sobak/sobak.pl" title="{{ __('app.footer.engine_tooltip') }}">{{ __('app.footer.engine') }}</a>
            <a href="{{ localized_page_route('polityka-prywatnosci') }}">{{ __('app.footer.privacy_policy') }}</a>
            <a href="https://wordpress.com/themes/sorbet/" title="{{ __('app.footer.theme_tooltip') }}">{{ __('app.footer.theme') }}</a>
        </div>
    </footer>

    @include('partials.analytics')

    <script src="{{ mix('assets/js/bundle.js') }}"></script>

    @stack('footer_extras')

</body>
</html>
