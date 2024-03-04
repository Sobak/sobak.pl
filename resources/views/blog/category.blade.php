@extends('layout')

@section('site-content-extra-classes') with-page-title @endsection

@section('content')
    <header class="page-header">
        <h1 class="page-title">{{ __('blog.category.page_title') }}: {{ $category->name }}</h1>
    </header>

    @each('blog.post', $posts, 'post')

    {!! $posts->links() !!}
@endsection
