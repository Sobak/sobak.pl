<?php

namespace App\Content\Parsing\CommonMark;

use Illuminate\Support\Str;
use League\CommonMark\Inline\Element\AbstractWebResource;

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
