<?php

return [
    'root' => env('CONTENT_ROOT', storage_path('app/content')),

    'show_scheduled' => env('CONTENT_SHOW_SCHEDULED', false),

    'links' => [
        'http://m4tx.pl' => "Blog m4tx'a",
        'http://mrowqa.pl' => "Mrowqa's blog",
        'http://rynko.pl' => 'Rynko.pl',
        'http://webkrytyk.pl' => 'WebKrytyk',
    ],
];
