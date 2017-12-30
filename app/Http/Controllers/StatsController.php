<?php

namespace App\Http\Controllers;

use Cache;

class StatsController extends Controller
{
    public function index()
    {
        return response()->json(Cache::get('blog_stats'));
    }
}
