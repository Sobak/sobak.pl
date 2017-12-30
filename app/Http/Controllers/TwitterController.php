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

            return $client->get('statuses/user_timeline', [
                'scren_name' => 'SobakPL',
                'count' => 5,
                'include_rts' => true,
                'exclude_replies' => false,
            ]);
        });

        return response()->json($entries);
    }
}
