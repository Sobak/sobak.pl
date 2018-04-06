@extends('layout')

@section('content')
    <article class="hentry type-page">
        <header class="entry-header">
            <h1 class="entry-title">{{ $page->title }}</h1>
        </header>

        <div class="entry-content">
            {!! $page->content !!}
        </div>
    </article>
@endsection
