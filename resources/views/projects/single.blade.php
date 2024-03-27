@extends('layout')

@php
/** @var $project \App\Models\Project */
@endphp

@push('header_extras')
    <meta name="og:title" content="{{ $project->title }}">
    <meta name="og:type" content="article">
    <meta name="og:image" content="{{ $project->thumbnail_url }}">
    <meta name="og:url" content="{{ route('project', $project) }}">
@endpush

@section('content')
    <article class="type-page">
        <header class="entry-header">
            <h1 class="entry-title">{{ $project->title }}</h1>
        </header>

        <div class="entry-content">
            @if ($project->language !== app()->getLocale())
                <div class="box box-warning">
                    @if ($translation = $project->getTranslation(app()->getLocale()))
                        {!! __('projects.translation_available', ['url' => route('project', $translation), 'title' => $translation->title]) !!}
                    @else
                        {{ __('projects.translation_unavailable') }}
                    @endif
                </div>
            @endif

            {!! $project->content !!}
        </div>
    </article>
@endsection
