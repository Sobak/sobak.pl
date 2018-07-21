<li class="{{ $project->type }} bigcard">
    <h6>{{ $project->title }}</h6>

    <div class="nimble-portfolio-holder">
        <div class="nimble-portfolio-item" style="background-image: url('{{ $project->thumbnail }}');">
            <a href="{{ route('project', $project) }}">
                <div class="nimble-portfolio-rollerbg"></div>
            </a>
        </div>
        <div class="nimble-portfolio-title">
            <a href="{{ route('project', $project) }}" class="button info">Informacje →</a>
            @if ($project->url)
            <a href="{{ $project->url }}" class="button view">Zobacz projekt →</a>
            @endif
        </div>
    </div>
</li>