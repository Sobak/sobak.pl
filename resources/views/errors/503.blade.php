<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ mix('assets/css/app.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,300italic,400italic,600italic|PT+Serif:400,400italic&amp;subset=latin,latin-ext">
</head>

<body>
<header class="site-header">
    <div class="site-header-wrapper">
        <div class="site-branding">
            <h1 class="site-title"><a href="{{ url('/') }}" rel="home">{{ config('app.name') }}</a></h1>
            <h2 class="site-description">{{ config('site.description') }}</h2>
        </div>
    </div>
</header>
<div id="social-links-toggle-nav" class="panel" style="display: block;">
    <div class="social-links">
        <ul>
            <li><a href="https://www.linkedin.com/in/msobaczewski/"><span class="sr-only">LinkedIn</span></a></li>
            <li><a href="https://twitter.com/SobakPL"><span class="sr-only">Twitter</span></a></li>
            <li><a href="https://github.com/Sobak"><span class="sr-only">GitHub</span></a></li>
            <li><a href="https://facebook.com/SobakPL"><span class="sr-only">Facebook</span></a></li>
        </ul>
    </div>
</div>
<div id="content" class="site-content">
    <div class="content-area">
        <main id="main" class="site-main">
            <article class="hentry type-none">
                <header class="entry-header">
                    <h1 class="entry-title">Przerwa techniczna</h1>
                </header>

                <div class="entry-content">
                    <p>
                        Z przyczyn technicznych blog nie jest obecnie dostępny. Spróbuj za kilka minut.
                    </p>
                </div>
            </article>
        </main>
    </div>
</div>

<footer class="site-footer">
    <div class="site-info">
        <a href="https://github.com/Sobak/homepage" title="Napędzane silnikiem Perception">Powered by Perception</a>
    </div>
</footer>

@include('partials.analytics')

</body>
</html>
