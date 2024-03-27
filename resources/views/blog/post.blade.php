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
        @if ($single && $post->language !== app()->getLocale())
            <div class="box box-warning">
            @if ($translation = $post->getTranslation(app()->getLocale()))
                {!! __('blog.post.translation_available', ['url' => route('post', $translation), 'title' => $translation->title]) !!}
            @else
                {{ __('blog.post.translation_unavailable') }}
            @endif
            </div>
        @endif

        @if (config('content.show_scheduled') && $post->created_at > now())
            <div class="box box-warning">
                <p>
                    {{ __('blog.post.scheduled') }} {{ localized_date($post->created_at, true) }}.
                </p>
            </div>
        @endif

        @if ($post->project !== null)
            <div class="box box-info">
                <p>
                    {{ __('blog.post.project_linked', [
                        'project_name' => $post->project()->first()->title,
                        'url' => $post->project()->first()->slug,
                    ]) }}
                </p>
            </div>
        @endif

        {!! !$single && $post->excerpt ? $post->excerpt : $post->content !!}

        @if (!$single && $post->excerpt)
            <p>
                <a href="{{ route('post', $post) }}" class="more-link">
                    {{ __('blog.post.read_more') }}
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
