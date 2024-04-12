{!! $xmlVersion !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ __('app.branding.name', [], $language) }}</title>
        <link>{{ route('index') }}</link>
        <atom:link href="{{ route("feed.$feedLanguage") }}" rel="self" type="application/rss+xml" />
        <description>{{ __('app.branding.description', [], $language) }}</description>
        <lastBuildDate>{{ $buildDate }}</lastBuildDate>
        <language>{{ $language }}</language>
        <ttl>180</ttl>
        @foreach($posts as $post)
        <item>
            <title>{{ $post->title }}</title>
            <link>{{ route('post', $post) }}</link>
            <pubDate>{{ $post->created_at->format(DATE_RSS) }}</pubDate>
            @foreach ($post->categories as $category)
            <category><![CDATA[{{ $category->{'name_' . $language} }}]]></category>
            @endforeach
            <guid>{{ route('post', $post) }}</guid>
            <description><![CDATA[{!! $post->content !!}]]></description>
        </item>
        @endforeach
    </channel>
</rss>
