<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Cookie;

class LanguageController extends Controller
{
    public function change(string $lang)
    {
        $cookie = new Cookie('current_language', $lang, now()->addYear());

        return redirect()
            ->back()
            ->withCookie($cookie);
    }
}
