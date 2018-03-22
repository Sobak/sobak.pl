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
            <div role="form" class="wpcf7" id="wpcf7-f1011-p661-o1" dir="ltr">
                <div class="screen-reader-response"></div>
                <form action="{{ route('contact') }}" method="post" novalidate>
                    <p>
                        Imię<br />
                        <input type="text" name="your-name" size="40" aria-required="true" aria-invalid="false">
                    </p>
                    <p>
                        Adres email (wymagane)<br />
                        <input type="email" name="your-email" size="40" aria-required="true" aria-invalid="false">
                    </p>
                    <p>
                        Temat<br />
                        <input type="text" name="your-subject" size="40" aria-invalid="false">
                    </p>
                    <p>
                        Treść wiadomości<br />
                        <textarea name="your-message" cols="40" rows="10" aria-invalid="false"></textarea>
                    </p>
                    <p>
                        Przepisz kod z obrazka:<br />
                        <img width="72" height="24" alt="captcha" src="http://sobak.pl/wp-content/uploads/wpcf7_captcha/4127110757.png"><br />
                        <input type="text" name="captcha-126" size="40" autocomplete="off" aria-invalid="false">
                    </p>
                    <p>
                        <input type="submit" value="Wyślij">
                    </p>
                </form>
            </div>
        </div>
    </article>
@endsection
