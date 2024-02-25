@extends('layout')

@section('site-content-extra-classes') with-page-title @endsection

@section('content')
    <header class="page-header">
        <h1 class="page-title">Tag: {{ $tag->name }}</h1>
    </header>

    @each('blog.post', $posts, 'post')

    {!! $posts->links() !!}
@endsection
