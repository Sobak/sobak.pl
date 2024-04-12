<div id="sidebar" class="widget-area" role="complementary">
    <aside class="widget">
        <h1>{{ __('app.sidebar.blog_language') }}</h1>
        <ul class="language-selector">
            <li {!! if_active('index') !!}>
                <a href="{{ route('index') }}">{{ __('app.sidebar.blog_language_all') }}</a>
            </li>
            <li {!! if_active('index.polish') !!}>
                <a href="{{ route('index.polish') }}">{{ __('app.sidebar.blog_language_pl') }}</a>
            </li>
            <li {!! if_active('index.english') !!}>
                <a href="{{ route('index.english') }}">{{ __('app.sidebar.blog_language_en') }}</a>
            </li>
        </ul>
    </aside>

    <aside class="widget">
        <h1>{{ __('app.sidebar.categories') }}</h1>
        <ul>
        @foreach ($categories as $category)
            <li>
                <a href="{{ route('category', [$category->slug]) }}">
                    {{ app()->getLocale() === 'pl' ? $category->name_pl : $category->name_en }}
                </a>
                ({{ $category->posts_count }})
            </li>
        @endforeach
        </ul>
    </aside>
</div>
