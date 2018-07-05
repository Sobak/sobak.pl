<?php

namespace App\Http\Controllers;

use App\Models\Post;

class FeedController extends Controller
{
    public function index()
    {
        $buildDate = date(DATE_RSS, filemtime(config('database.connections.website.database')));

        $posts = Post::latest()->limit(10)->get();

        return response()
            ->view('feed.index', [
                'buildDate' => $buildDate,
                'posts' => $posts,
            ])
            ->header('Content-Type', 'text/xml');
    }
}
