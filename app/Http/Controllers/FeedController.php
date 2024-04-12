<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function all(): Response
    {
        return $this->buildFeedResponseForLanguage(null);
    }

    public function polish(): Response
    {
        return $this->buildFeedResponseForLanguage('pl');
    }

    public function english(): Response
    {
        return $this->buildFeedResponseForLanguage('en');
    }

    private function buildFeedResponseForLanguage(?string $language): Response
    {
        $buildDate = date(DATE_RSS, filemtime(config('database.connections.website.database')));

        $posts = Post::query()
            ->when($language, function (Builder $query, string $language) {
                $query->where('language', '=', $language);
            })
            ->latest()
            ->limit(10)
            ->get();

        return response()
            ->view('feed.index', [
                'buildDate' => $buildDate,
                'feedLanguage' => $language ?? 'all',
                'language' => $language ?? app()->getLocale(),
                'posts' => $posts,
                'xmlVersion' => '<?xml version="1.0" encoding="UTF-8"?>',
            ])
            ->header('Content-Type', 'text/xml');
    }
}
