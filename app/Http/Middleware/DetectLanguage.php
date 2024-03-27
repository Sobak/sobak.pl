<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Content\Multilingual\LanguageDetector;
use Closure;
use Illuminate\Http\Request;

class DetectLanguage
{
    public function handle(Request $request, Closure $next)
    {
        app()->setLocale(LanguageDetector::detect($request));

        return $next($request);
    }
}
