<?php

declare(strict_types=1);

namespace App\Http\Composers;

use Illuminate\View\View;

class LayoutComposer
{
    public function compose(View $view): void
    {
        $view->with([
            'oppositeLanguage' => app()->getLocale() === 'pl' ? 'en' : 'pl',
        ]);
    }
}
