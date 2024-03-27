@extends('layout')

@php
/** @var $page \App\Models\Page */
@endphp
@push('header_extras')
    <meta name="og:title" content="{{ $page->title }}">
    <meta name="og:type" content="article">
    <meta name="og:image" content="{{ url('avatar.png') }}">
    <meta name="og:url" content="{{ route('page', $page) }}">
@endpush

@section('content')
    <article class="type-page">
        <header class="entry-header">
            <h1 class="entry-title">{{ $page->title }}</h1>
        </header>

        <div class="entry-content">
            @if ($page->language !== app()->getLocale())
                <div class="box box-warning">
                    @if ($translation = $page->getTranslation(app()->getLocale()))
                        {!! __('pages.translation_available', ['url' => route('page', $translation), 'title' => $translation->title]) !!}
                    @else
                        {{ __('pages.translation_unavailable') }}
                    @endif
                </div>
            @endif

            {!! $page->content !!}
        </div>
    </article>
@endsection
