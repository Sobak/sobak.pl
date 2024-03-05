<?php

namespace App\Content\Parsing\CommonMark;

trait LinksAssets
{
    protected function isInternalAsset(string $url): bool
    {
        return str_starts_with($url, '../assets/');
    }

    protected function convertToAssetLink(string $url): string
    {
        $file = substr($url, strlen('../assets/'));

        return asset("assets/images/$file");
    }
}
