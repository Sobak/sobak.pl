<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Content Path
    |--------------------------------------------------------------------------
    |
    | This variable specifies root path for content storage which is then
    | going to be used by the indexer.
    |
    */

    'path' => env('CONTENT_ROOT', storage_path('app/content')),

    /*
    |--------------------------------------------------------------------------
    | Show Scheduled posts?
    |--------------------------------------------------------------------------
    |
    | Using this setting you can enforce showing posts scheduled for the future.
    | This can be helpful on the local environment to not be surprised by planned
    | content. However, for the convenience this setting has been separated from
    | app.env
    |
    */

    'show_scheduled' => env('CONTENT_SHOW_SCHEDULED', false),

    /*
    |--------------------------------------------------------------------------
    | 3rd Party Website Links
    |--------------------------------------------------------------------------
    |
    | An array of links to promoted 3rd party websites displayed on page
    | sidebar. The order of elements is irrelevant - all elements are shuffled
    | each page load.
    |
    */

    'links' => [
        'http://m4tx.pl' => "Blog m4tx'a",
        'http://mrowqa.pl' => "Mrowqa's blog",
        'http://rynko.pl' => 'Rynko.pl',
        'http://webkrytyk.pl' => 'WebKrytyk',
    ],
];
