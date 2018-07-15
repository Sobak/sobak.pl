<div class="widget-area" role="complementary">
    <aside class="widget">
        <h1>Mikroblog</h1>
        <ul id="twitter-widget-tweets" class="twitter-feed">
            @if ($twitterEntries)
                @include('partials.twitter', ['entries' => $twitterEntries])
            @else
                <li><img src="{{ asset('assets/images/ajax-loader.gif') }}" width="16" height="16" alt="">Ładowanie tweetów...</li>
            @endif
        </ul>
    </aside>

    <aside class="widget">
        <h1>Linki</h1>
        <ul>
        @foreach ($externalLinks as $url => $name)
            <li><a href="{!! $url !!}">{{ $name }}</a></li>
        @endforeach
        </ul>
    </aside>
    <aside class="widget">
        <h1>Kategorie</h1>
        <ul>
        @foreach ($categories as $category)
            <li>
                <a href="{{ route('category', [$category->slug]) }}">{{ $category->name }}</a>
                ({{ $category->posts_count }})
            </li>
        @endforeach
        </ul>
    </aside>
</div>
