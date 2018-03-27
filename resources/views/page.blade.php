@extends('layout')

@section('content')
    <article class="type-page format-standard hentry">
        <header class="entry-header">
            <h1 class="entry-title">{{ $page->title }}</h1>
        </header>

        <div class="entry-content">
            {!! $page->content !!}
        </div>
    </article>
@endsection
