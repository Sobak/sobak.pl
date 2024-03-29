<?php

declare(strict_types=1);

namespace App\Content\Indexing\Indexers;

use App\Models\Redirect;

trait CreatesRedirects
{
    private function createMultipleRedirects($redirects, $httpCode = 301): void
    {
        $rows = [];
        foreach ($redirects as $from => $to) {
            $rows[] = [
                'source_url' => $from,
                'target_url' => $to,
                'http_code' => $httpCode,
            ];
        }

        Redirect::insert($rows);
    }

    private function createRedirect($from, $to, $httpCode = 301): Redirect
    {
        $redirect = Redirect::where('source_url', $from)->first();

        if ($redirect === null) {
            $redirect = Redirect::create([
                'source_url' => $from,
                'target_url' => $to,
                'http_code' => $httpCode,
            ]);
        }

        return $redirect;
    }
}
