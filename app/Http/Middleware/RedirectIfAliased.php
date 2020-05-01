<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use App\Models\RedirectLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RedirectIfAliased
{
    public function handle(Request $request, Closure $next)
    {
        $sourceUrl = Str::before(trim($request->getRequestUri(), '/'), '?');

        $redirect = Redirect::where('source_url', $sourceUrl)->first();

        if ($redirect !== null) {
            $redirectLog = new RedirectLog();
            $redirectLog->source_url = $sourceUrl;
            $redirectLog->target_url = $redirect->target_url;
            $redirectLog->ip = $request->getClientIp();
            $redirectLog->user_agent = $request->userAgent();
            $redirectLog->save();

            return redirect($redirect->target_url, $redirect->http_code);
        }

        return $next($request);
    }
}