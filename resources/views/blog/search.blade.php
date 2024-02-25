@extends('layout')

@section('site-content-extra-classes') with-page-title @endsection

@section('content')
    <header class="page-header">
        <h1 class="page-title">Wyniki wyszukiwania dla: {{ $phrase }}</h1>
    </header>

    @each('blog.post', $posts, 'post', 'blog.search_empty')

    {!! $posts->links() !!}
@endsection
