<?php

namespace App\Utils\CommonMark;

use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;
use League\CommonMark\Util\ConfigurationAwareInterface;
use League\CommonMark\Util\ConfigurationInterface;
use League\CommonMark\Util\RegexHelper;
use Illuminate\Support\Str;

class LinkRenderer implements InlineRendererInterface, ConfigurationAwareInterface
{
    use LinksAssets;

    /** @var ConfigurationInterface */
    protected $config;

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

        $attrs = $inline->getData('attributes', []);

        $forbidUnsafeLinks = !$this->config->get('allow_unsafe_links');
        if (!($forbidUnsafeLinks && RegexHelper::isLinkPotentiallyUnsafe($inline->getUrl()))) {
            $attrs['href'] = $inline->getUrl();
        }

        if (isset($inline->data['title'])) {
            $attrs['title'] = $inline->data['title'];
        }

        if (isset($attrs['target']) && $attrs['target'] === '_blank' && !isset($attrs['rel'])) {
            $attrs['rel'] = 'noopener noreferrer';
        }

        return new HtmlElement('a', $attrs, $htmlRenderer->renderInlines($inline->children()));
    }

    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->config = $configuration;
    }

    protected function isBlogLink(Link $link)
    {
        return Str::startsWith($link->getUrl(), 'blog://');
    }

    protected function convertToBlogPostLink(string $url): string
    {
        $slug = substr($url, strlen('blog://'));

        return route('post', [$slug]);
    }
}
