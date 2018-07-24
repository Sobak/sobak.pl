@extends('layout')

@section('content')
    <article class="hentry type-page">
        <header class="entry-header">
            <h1 class="entry-title">Portfolio</h1>
        </header>

        <div class="entry-content">
            <div class="nimble-portfolio-content">
                <div class="nimble-portfolio-filter">
                    <ul class="nimble-portfolio-ul">
                        <li class="current"><a href="#" data-type="all">Wszystko</a></li>
                        @foreach (config('projects.types') as $id => $name)
                        <li><a href="#"  data-type="{{ $id }}">{{ $name }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="nimble-portfolio three">
                    <ul class="nimble-portfolio-ul">
                        @each('projects.project', $projects, 'project')
                    </ul>
                </div>
            </div>
        </div>
    </article>
@endsection

@push('footer_scripts')
    <script>
        hoverImage = new Image();
        hoverImage.src = '{{ asset('assets/images/hover.png') }}';
    </script>
@endpush
