<?php

return [

    'live_validation' => false,

    'rules' => [
        'GOOGLE_ANALYTICS_ID' => 'required',
        'GOOGLE_RECAPTCHA_KEY' => 'required',
        'GOOGLE_RECAPTCHA_SECRET' => 'required',
        'MAIL_USERNAME' => 'required',
        'MAIL_PASSWORD' => 'required',
        'MAIL_TO_ADDRESS' => 'required|email',
    ],

];
