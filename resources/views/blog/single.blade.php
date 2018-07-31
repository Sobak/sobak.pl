@extends('layout')

@push('header_extras')
    <meta name="og:title" content="{{ $post->title }}">
    <meta name="og:type" content="article">
    <meta name="og:image" content="{{ url('avatar.png') }}">
    <meta name="og:url" content="{{ route('post', $post) }}">
@endpush

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
