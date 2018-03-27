@extends('layout')

@section('content')
    <article class="hentry format-none">
        <header class="entry-header">
            <h1 class="entry-title">Strona nie została znaleziona</h1>
        </header>

        <div class="entry-content">
            <p>
                Wygląda na to że w tym miejscu nic nie ma. Użyj menu, skorzystaj z wyszukiwarki lub
                <a href="{{ route('page', ['contact']) }}">skontaktuj się ze mną</a> jeżeli sądzisz,
                coś faktycznie poszło nie tak.
            </p>
        </div>
    </article>
@endsection
