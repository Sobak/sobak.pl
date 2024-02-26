<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => 'website',

    /*
    |--------------------------------------------------------------------------
    | Database Connections (custom logic ahead!)
    |--------------------------------------------------------------------------
    |
    | My little engine uses rather distinctive logic for the databases. The main
    | database used to power the website is recreated from scratch on every site
    | deployment. The "indexer" database gets populated with information fetched
    | from the site files. Once it is built, we swap the "website" database with
    | "indexer" one, providing zero-downtime deployments - like Capistrano does!
    |
    | And then there's a connection/database called "permanent" which, like it's
    | name implies, is a regular kind of DB. It's _not_ reset at all, so it uses
    | the classical migration flow. It's not used a lot, but you should be aware.
    |
    | You should check database/migrations/README.md to learn more, and how that
    | affects the development process. No worries, I'd say it is actually simple.
    |
    */

    'connections' => [

        'website' => [
            'driver' => 'sqlite',
            'database' => database_path('website.sqlite'),
            'prefix' => '',
        ],

        'indexer' => [
            'driver' => 'sqlite',
            'database' => database_path('indexer.sqlite'),
            'prefix' => '',
        ],

        // This is a permanent database that is not recreated on every content change
        'permanent' => [
            'driver' => 'sqlite',
            'database' => database_path('permanent.sqlite'),
            'prefix' => '',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
