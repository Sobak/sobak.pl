@extends('layout')

@section('content')
    @each('blog.post', $posts, 'post')

    {!! $posts->links() !!}
@endsection
