<?php

namespace App\Http\Composers;

use App\Models\Category;
use Illuminate\View\View;

class SidebarComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with([
            'categories' => Category::withCount('posts')->orderBy('name')->get(),
            'externalLinks' => shuffle_assoc(config('content.links')),
            'twitterEntries' => cache('twitter_entries'),
        ]);
    }
}
