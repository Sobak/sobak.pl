@extends('layout')

@section('content')
    <article class="type-page">
        <header class="entry-header">
            <h1 class="entry-title">{{ __('contact.title') }}</h1>
        </header>

        <div class="entry-content">
            @include('partials.box')
            <p>{{ __('contact.content') }} <span class="email-protect">@</span></p>
            <form action="{{ route('contact') }}" method="post" class="form">
                {{ csrf_field() }}
                <p class="form-input">
                    <label for="name" class="required">{{ __('contact.form.name') }}</label>
                    <input type="text" name="name" id="name" size="40" maxlength="150" value="{{ old('name') }}" required>
                    {{ form_error('name', $errors)  }}
                </p>
                <p class="form-input">
                    <label for="email" class="required">{{ __('contact.form.email') }}</label>
                    <input type="email" name="email" id="email" size="40" maxlength="200" value="{{ old('email') }}" required>
                    {{ form_error('email', $errors)  }}
                </p>
                <p class="form-input">
                    <label for="subject">{{ __('contact.form.subject') }}</label>
                    <input type="text" name="subject" id="subject" size="40" maxlength="150" value="{{ old('subject') }}">
                </p>
                <p class="form-input">
                    <label for="message" class="required">{{ __('contact.form.message') }}</label>
                    <textarea name="message" id="message" cols="45" rows="10" required>{{ old('message') }}</textarea>
                    {{ form_error('message', $errors)  }}
                </p>

                @if (config('services.google.recaptcha.key'))
                <div class="form-input">
                    <div class="g-recaptcha" data-sitekey="{{ config('services.google.recaptcha.key') }}"></div>
                    {{ form_error('g-recaptcha-response', $errors)  }}
                </div>
                @endif
                <p>
                    <button>{{ __('contact.form.submit') }}</button>
                </p>
            </form>
        </div>
    </article>
@endsection

@push('footer_extras')
    @if (config('services.google.recaptcha.key'))
        <script src="https://www.google.com/recaptcha/api.js"></script>
    @endif
@endpush
