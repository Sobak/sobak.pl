@extends('layout')

@push('header_extras')
    <meta name="og:title" content="{{ $project->title }}">
    <meta name="og:type" content="article">
    <meta name="og:image" content="{{ $project->thumbnail_url }}">
    <meta name="og:url" content="{{ route('project', $project) }}">
@endpush

@section('content')
    <article class="hentry type-page">
        <header class="entry-header">
            <h1 class="entry-title">{{ $project->title }}</h1>
        </header>

        <div class="entry-content">
            {!! $project->content !!}
        </div>
    </article>
@endsection
