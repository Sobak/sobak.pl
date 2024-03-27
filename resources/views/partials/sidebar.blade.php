<div id="sidebar" class="widget-area" role="complementary">
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
