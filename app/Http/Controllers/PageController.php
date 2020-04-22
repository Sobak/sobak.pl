<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Redirect;
use App\Models\RedirectLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function show(Request $request, $page)
    {
        $page = Page::where('slug', $page)->first();

        if ($page === null) {
            return $this->checkForAlias($request);
        }

        return view('page', [
            'page' => $page,
            'title' => page_title($page->title),
        ]);
    }

    protected function checkForAlias(Request $request)
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

        abort(404);

        return false;
    }
}
