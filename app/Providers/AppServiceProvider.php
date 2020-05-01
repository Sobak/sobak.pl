<?php

namespace App\Providers;

use App\Http\Composers\SidebarComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('partials.sidebar', SidebarComposer::class);
    }

    public function register()
    {
        //
    }
}
