@foreach ($entries as $entry)
<li>
    <p class="twiget-tweet">{!! twitter_parse_status($entry->text) !!}</p>
    <p class="twiget-meta">
        <a href="http://twitter.com/{{ $entry->user->screen_name }}/statuses/{{ $entry->id_str }}">
            {{ twitter_relative_time($entry->created_at) }}
        </a>
    </p>
</li>
@endforeach
