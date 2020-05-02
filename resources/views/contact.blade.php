@extends('layout')

@section('content')
    <article class="type-page">
        <header class="entry-header">
            <h1 class="entry-title">Kontakt</h1>
        </header>

        <div class="entry-content">
            @include('partials.box')
            <p>
                Masz jakąś sprawę? Chcesz się ze mną skontaktować? Użyj, proszę,
                poniższego formularza. Jeżeli z preferujesz kontakt poprzez email,
                napisz na <span class="email-protect">@</span>
            </p>
            <form action="{{ route('contact') }}" method="post" class="form">
                {{ csrf_field() }}
                <p class="form-input">
                    <label for="name" class="required">Podpis</label>
                    <input type="text" name="name" id="name" size="40" value="{{ old('name') }}" required>
                    {{ form_error('name', $errors)  }}
                </p>
                <p class="form-input">
                    <label for="email" class="required">Email</label>
                    <input type="email" name="email" id="email" size="40" value="{{ old('email') }}" required>
                    {{ form_error('email', $errors)  }}
                </p>
                <p class="form-input">
                    <label for="subject">Temat</label>
                    <input type="text" name="subject" id="subject" size="40"  value="{{ old('subject') }}">
                </p>
                <p class="form-input">
                    <label for="message" class="required">Wiadomość</label>
                    <textarea name="message" id="message" cols="45" rows="10" required>{{ old('message') }}</textarea>
                    {{ form_error('message', $errors)  }}
                </p>

                <div class="form-input">
                    <div class="g-recaptcha" data-sitekey="{{ config('services.google.recaptcha.key') }}"></div>
                    {{ form_error('g-recaptcha-response', $errors)  }}
                </div>
                <p>
                    <input type="submit" value="Wyślij">
                </p>
            </form>
        </div>
    </article>
@endsection

@push('footer_extras')
    <script src="https://www.google.com/recaptcha/api.js"></script>
@endpush
