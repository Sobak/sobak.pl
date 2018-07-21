<?php

namespace App\Utils\CommonMark;

use League\CommonMark\Inline\Element\AbstractWebResource;

trait LinksAssets
{
    protected function isInternalAsset(AbstractWebResource $link)
    {
        return starts_with($link->getUrl(), '../assets/');
    }

    protected function convertToAssetLink(string $url): string
    {
        $file = substr($url, strlen('../assets/'));

        return asset("assets/images/$file");
    }
}
