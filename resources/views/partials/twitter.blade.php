@foreach ($entries as $entry)
<li>
    <p class="tweet-text">{!! twitter_parse_status($entry->text) !!}</p>
    <p class="tweet-meta">
        <a href="https://twitter.com/{{ $entry->username }}/statuses/{{ $entry->id }}">
            {{ twitter_relative_time($entry->created_at) }}
        </a>
    </p>
</li>
@endforeach
