@php
/** @var $project \App\Models\Project */
@endphp

<li class="{{ $project->type }}">
    <h6>{{ $project->title }}</h6>

    <div class="image" style="background-image: url('{{ $project->thumbnail_url }}');">
        <a href="{{ route('project', $project) }}">
            <div class="rollerbg"></div>
        </a>
    </div>
    <div class="meta">
        <a href="{{ route('project', $project) }}" class="button info">Informacje →</a>
        @if ($project->url)
        <a href="{{ $project->url }}" class="button view">Zobacz projekt →</a>
        @endif
    </div>
</li>