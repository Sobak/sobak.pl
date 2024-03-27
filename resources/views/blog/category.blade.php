@extends('layout')

@section('site-content-extra-classes') with-page-title @endsection

@section('content')
    <header class="page-header">
        <h1 class="page-title">
            {{ __('blog.category.page_title') }}:
            {{ app()->getLocale() === 'pl' ? $category->name_pl : $category->name_en }}
        </h1>
    </header>

    @each('blog.post', $posts, 'post')

    {!! $posts->links() !!}
@endsection
