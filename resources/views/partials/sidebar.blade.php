<div id="sidebar" class="widget-area" role="complementary">
    <aside class="widget">
        <h1>Kategorie</h1>
        <ul>
        @foreach ($categories as $category)
            <li>
                <a href="{{ route('category', [$category->slug]) }}">{{ $category->name }}</a>
                ({{ $category->posts_count }})
            </li>
        @endforeach
        </ul>
    </aside>
</div>
