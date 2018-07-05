<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ config('app.name') }}</title>
        <link>{{ route('index') }}</link>
        <atom:link href="{{ route('feed') }}" rel="self" type="application/rss+xml" />
        <description>{{ config('app.description') }}</description>
        <lastBuildDate>{{ $buildDate }}</lastBuildDate>
        <language>pl</language>
        <ttl>180</ttl>
        @foreach($posts as $post)
        <item>
            <title>{{ $post->title }}</title>
            <link>{{ route('post', $post) }}</link>
            <pubDate>{{ $post->created_at->format(DATE_RSS) }}</pubDate>
            @foreach ($post->categories as $category)
            <category><![CDATA[{{ $category->name }}]]></category>
            @endforeach
            <guid>{{ route('post', $post) }}</guid>
            <description><![CDATA[{!! $post->content !!}]]></description>
        </item>
        @endforeach
    </channel>
</rss>
