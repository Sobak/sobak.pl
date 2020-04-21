<?php

namespace App\Utils\CommonMark;

use League\CommonMark\Inline\Element\AbstractWebResource;
use Illuminate\Support\Str;

trait LinksAssets
{
    protected function isInternalAsset(AbstractWebResource $link)
    {
        return Str::startsWith($link->getUrl(), '../assets/');
    }

    protected function convertToAssetLink(string $url): string
    {
        $file = substr($url, strlen('../assets/'));

        return asset("assets/images/$file");
    }
}
