@extends('layout')

@section('content')
    <header class="page-header">
        <h1 class="page-title">{{ $tag->name }}</h1>
    </header>

    @each('blog.post', $posts, 'post')

    {!! $posts->links() !!}
@endsection
