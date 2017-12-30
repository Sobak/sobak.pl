<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;

class RedirectIfAliased
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $sourceUrl = trim($request->getRequestUri(), '/');

        $redirect = Redirect::where('source_url', $sourceUrl)->first();

        if ($redirect !== null) {
            return redirect($redirect->target_url, $redirect->http_code);
        }

        return $next($request);
    }
}
