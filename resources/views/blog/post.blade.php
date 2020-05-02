@php
$single = $single ?? false;
/** @var $post \App\Models\Post */
@endphp
<article @if ($post->language !== config('app.locale')) lang="{{ $post->language }}" @endif>
    <header class="entry-header">
        <h1 class="entry-title">
            @if ($single)
            {{ $post->title }}
            @else
            <a href="{{ route('post', $post) }}" rel="bookmark">{{ $post->title }}</a>
            @endif
        </h1>
    </header>

    <div class="entry-content">
        @if (config('content.show_scheduled') && $post->created_at > now())
            <div class="box box-warning">
                <p>
                    Publikacja tego wpisu została zaplanowana na {{ localized_date($post->created_at, true) }}.
                </p>
            </div>
        @endif

        @if ($post->project !== null)
            <div class="box box-info">
                <p>
                    Ten post przedstawia doświadczenia lub przemyślenia nabyte (bezpośrednio lub nie) podczas prac nad
                    <a href="{{ route('project', $post->project()->first()->slug) }}">projektem {{ $post->project()->first()->title }}</a>.
                    Kliknij w link aby zobaczyć go w portfolio.
                </p>
            </div>
        @endif

        {!! !$single && $post->excerpt ? $post->excerpt : $post->content !!}

        @if (!$single && $post->excerpt)
            <p>
                <a href="{{ route('post', $post) }}" class="more-link">
                    Czytaj dalej &rarr;
                </a>
            </p>
        @endif
    </div>

    <footer class="entry-meta">
        <span class="post-date">
            <a href="{{ route('post', $post) }}" title="{{ $post->created_at->format('d.m.Y H:i') }}" rel="bookmark">
                <time class="entry-date" datetime="{{ $post->created_at->format(DATE_W3C) }}">
                    {{ localized_date($post->created_at) }}
                </time>
            </a>
        </span>
        @if ($single && $post->tags->isNotEmpty())
        <span class="tags-links">
            @foreach ($post->tags as $tag)
                <a href="{{ route('tag', $tag) }}" rel="tag">{{ $tag->name }}</a>@unless($loop->last),@endunless
            @endforeach
        </span>
        @endif

    </footer>
</article>
