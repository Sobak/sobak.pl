@extends('layout')

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
