@foreach ($entries as $entry)
<li>
    <p class="tweet-text">{!! $entry->text !!}</p>
    <p class="tweet-meta">
        <a href="https://twitter.com/{{ $entry->username }}/statuses/{{ $entry->id }}">
            {{ $entry->relative_time }}
        </a>
    </p>
</li>
@endforeach
