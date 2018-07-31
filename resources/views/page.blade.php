@extends('layout')

@push('header_extras')
    <meta name="og:title" content="{{ $page->title }}">
    <meta name="og:type" content="article">
    <meta name="og:image" content="{{ url('avatar.png') }}">
    <meta name="og:url" content="{{ route('page', $page) }}">
@endpush

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
