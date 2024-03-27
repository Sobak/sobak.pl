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
        $orderBy = app()->getLocale() === 'pl' ? 'name_pl' : 'name_en';

        $view->with([
            'categories' => Category::withCount('posts')->orderBy($orderBy)->get(),
        ]);
    }
}
