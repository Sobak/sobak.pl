@if ($paginator->hasPages())
    <nav class="navigation paging-navigation">
        <h1 class="screen-reader-text">Nawigacja wpisów</h1>
        <div class="nav-links">
            @if (PaginateRoute::hasPreviousPage())
            <div class="nav-previous">
                <a href="{{ PaginateRoute::previousPageUrl() }}">
                    <span class="meta-nav screen-reader-text">&larr;</span>
                </a>
            </div>
            @endif
            @if (PaginateRoute::hasNextPage($paginator))
                <div class="nav-next">
                    <a href="{{ PaginateRoute::nextPageUrl($paginator) }}">
                        <span class="meta-nav screen-reader-text">&rarr;</span>
                    </a>
                </div>
            @endif
        </div>
    </nav>

    <div class="pagination-container">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span>&laquo;</span>
        @else
            <a href="{{ PaginateRoute::previousPageUrl() }}" class="change-page">&laquo; <span>Poprzednia</span></a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span>{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="current">{{ $page }}</span>
                    @else
                        <a href="{{ PaginateRoute::pageUrl($page) }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if (PaginateRoute::hasNextPage($paginator))
            <a href="{{ PaginateRoute::nextPageUrl($paginator) }}" class="change-page"><span>Następna</span> &raquo;</a>
        @else
            <span>&raquo;</span>
        @endif
    </div>
@endif
