@extends('layout')

@section('content')
    <article class="page type-page hentry">
        <header class="entry-header">
            <h1 class="entry-title">{{ $project->title }}</h1>
        </header>

        <div class="entry-content">
            {!! $project->content !!}
        </div>
    </article>
@endsection
