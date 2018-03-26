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
            @foreach (shuffle_assoc(config('content.links')) as $url => $name)
                <li><a href="{!! $url !!}">{{ $name }}</a></li>
            @endforeach
            </ul>
        </div>
    </aside>
    <aside class="widget">
        <h1 class="widget-title">Kategorie</h1>
        <ul>
        @foreach (\App\Models\Category::withCount('posts')->orderBy('name')->get() as $category)
            <li>
                <a href="{{ route('category', [$category->slug]) }}">{{ $category->name }}</a>
                ({{ $category->posts_count }})
            </li>
        @endforeach
        </ul>
    </aside>
</div>