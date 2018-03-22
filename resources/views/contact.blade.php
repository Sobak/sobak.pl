@extends('layout')

@section('content')
    <article class="page type-page status-publish format-standard hentry">
        <header class="entry-header">
            <h1 class="entry-title">Kontakt</h1>
        </header>

        <div class="entry-content">
            <p>
                Masz jakąś sprawę? Chcesz się ze mną skontaktować? Użyj, proszę,
                poniższego formularza. Jeżeli z preferujesz kontakt poprzez email,
                napisz na <span class="email-protect">@</span>
            </p>
            <form action="{{ route('contact') }}" method="post" class="form" novalidate>
                <p class="form-input">
                    <label for="name">Podpis <span class="required">*</span></label>
                    <input type="text" name="name" id="name" size="40" required>
                </p>
                <p class="form-input">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" name="email" id="email" size="40" required>
                </p>
                <p class="form-input">
                    <label for="subject">Temat</label>
                    <input type="text" name="subject" id="subject" size="40">
                </p>
                <p class="form-input">
                    <label for="message">Wiadomość</label>
                    <textarea name="message" id="message" cols="45" rows="10" required></textarea>
                </p>
                <p>
                    <input type="submit" value="Wyślij">
                </p>
            </form>
        </div>
    </article>
@endsection
