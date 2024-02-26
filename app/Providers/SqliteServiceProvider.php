<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SqliteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ((file_exists($path = config('database.connections.persistent.database'))) === false) {
            touch($path);
        }
    }
}
