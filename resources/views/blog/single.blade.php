@extends('layout')

@push('header_extras')
    <meta name="og:title" content="{{ $post->title }}">
    <meta name="og:type" content="article">
    <meta name="og:image" content="{{ url('avatar.png') }}">
    <meta name="og:url" content="{{ route('post', $post) }}">
@endpush

@section('content')
    @include('blog.post')
@endsection
