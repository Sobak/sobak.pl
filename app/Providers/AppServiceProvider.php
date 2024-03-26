<?php

namespace App\Providers;

use App\Http\Composers\SidebarComposer;
use App\Models;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Paginator::defaultView('partials.pagination');

        View::composer('partials.sidebar', SidebarComposer::class);

        Relation::enforceMorphMap([
            'post' => Models\Post::class,
            'project' => Models\Project::class,
            'page' => Models\Page::class,
        ]);
    }

    public function register()
    {
        //
    }
}
