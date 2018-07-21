@extends('layout')

@section('content')
    @include('blog.post')

    <div class="comments-disabled">
        <h3>Komentarze wyłączone</h3>

        <p>
            Możliwość komentowania na blogu została wyłączona. Zapraszam do kontaktu na
            Twitterze, Facebooku lub poprzez formularz, <a href="{{ route('contact') }}">o
            ten tutaj</a>. Do usłyszenia!
        </p>
    </div>
@endsection
