<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Redirect;
use Illuminate\Support\Facades\Request;

class PageController extends Controller
{
    public function show($page)
    {
        $page = Page::where('slug', $page)->first();

        if ($page === null) {
            return $this->checkForAlias();
        }

        return view('page', [
            'page' => $page,
            'title' => page_title($page->title),
        ]);
    }

    protected function checkForAlias()
    {
        $sourceUrl = trim(Request::getRequestUri(), '/');

        $redirect = Redirect::where('source_url', $sourceUrl)->first();

        if ($redirect !== null) {
            return redirect($redirect->target_url, $redirect->http_code);
        }

        abort(404);

        return false;
    }
}
