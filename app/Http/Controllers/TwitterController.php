<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use Cache;

class TwitterController extends Controller
{
    public function entries()
    {
        $entries = Cache::remember('twitter_entries', 15, function () {
            $client = new TwitterOAuth(
                config('services.twitter.consumer_key'),
                config('services.twitter.consumer_secret'),
                config('services.twitter.access_token'),
                config('services.twitter.access_token_secret')
            );

            $entries = $client->get('statuses/user_timeline', [
                'scren_name' => config('services.twitter.username'),
                'count' => config('services.twitter.entries_count'),
                'include_rts' => true,
                'exclude_replies' => false,
            ]);

            return array_map(function ($entry) {
                return (object) [
                    'id' => $entry->id_str,
                    'relative_time' => twitter_relative_time($entry->created_at),
                    'text' => twitter_parse_status($entry->text),
                    'username' =>  $entry->user->screen_name,
                ];
            }, $entries);
        });

        return response()->json($entries);
    }
}
