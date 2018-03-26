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
