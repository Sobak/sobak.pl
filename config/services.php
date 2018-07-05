<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'google' => [
        'analytics' => env('GOOGLE_ANALYTICS_ID', false),
        'recaptcha' => [
            'key' => env('GOOGLE_RECAPTCHA_KEY'),
            'secret' => env('GOOGLE_RECAPTCHA_SECRET'),
        ]
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'twitter' => [
        'consumer_key' => env('TWITTER_CONSUMER_KEY'),
        'consumer_secret' => env('TWITTER_CONSUMER_SECRET'),
        'access_token' => env('TWITTER_ACCESS_TOKEN'),
        'access_token_secret' => env('TWITTER_ACCESS_TOKEN_SECRET'),
        'entries_count' => 5,
        'username' => env('TWITTER_USERNAME'),
    ],

];
