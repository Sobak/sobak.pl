<?php

namespace App\Utils\CommonMark;

use League\CommonMark\ElementRendererInterface;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Renderer\LinkRenderer as ParentLinkRenderer;

class LinkRenderer extends ParentLinkRenderer
{
    use LinksAssets;

    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer)
    {
        if (!($inline instanceof Link)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . get_class($inline));
        }

        if ($this->isBlogLink($inline)) {
            $url = $this->convertToBlogPostLink($inline->getUrl());

            $inline->setUrl($url);
        }

        if ($this->isInternalAsset($inline)) {
            $url = $this->convertToAssetLink($inline->getUrl());

            $inline->setUrl($url);
        }

        return parent::render($inline, $htmlRenderer);
    }

    protected function isBlogLink(Link $link)
    {
        return starts_with($link->getUrl(), 'blog://');
    }

    protected function convertToBlogPostLink(string $url): string
    {
        $slug = substr($url, strlen('blog://'));

        return route('post', [$slug]);
    }
}
