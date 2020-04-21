<?php

namespace App\Utils\CommonMark;

use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\Image;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;
use League\CommonMark\Util\ConfigurationAwareInterface;
use League\CommonMark\Util\ConfigurationInterface;
use League\CommonMark\Util\RegexHelper;
use League\CommonMark\Util\Xml;

class ImageRenderer implements InlineRendererInterface, ConfigurationAwareInterface
{
    use LinksAssets;

    /** @var ConfigurationInterface */
    protected $config;

    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer)
    {
        if (!($inline instanceof Image)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . get_class($inline));
        }

        $attrs = [];
        foreach ($inline->getData('attributes', []) as $key => $value) {
            $attrs[$key] = Xml::escape($value);
        }

        $forbidUnsafeLinks = $this->config->get('safe') || !$this->config->get('allow_unsafe_links');
        if ($forbidUnsafeLinks && RegexHelper::isLinkPotentiallyUnsafe($inline->getUrl())) {
            $attrs['src'] = '';
        } else {
            $attrs['src'] = Xml::escape($inline->getUrl());
        }

        // Conditional customization
        if ($this->isInternalAsset($inline)) {
            $attrs['src'] = $this->convertToAssetLink($inline->getUrl());
        }

        $alt = $htmlRenderer->renderInlines($inline->children());
        $alt = preg_replace('/\<[^>]*alt="([^"]*)"[^>]*\>/', '$1', $alt);
        $attrs['alt'] = preg_replace('/\<[^>]*\>/', '', $alt);

        if (isset($inline->data['title'])) {
            $attrs['title'] = Xml::escape($inline->data['title']);
        }

        // Customize HTML output in case of the internal asset
        if ($this->isInternalAsset($inline)) {
            return new HtmlElement(
                'a',
                ['href' => $this->convertToAssetLink($inline->getUrl())],
                new HtmlElement('img', $attrs, '', true)
            );
        } else {
            return new HtmlElement('img', $attrs, '', true);
        }
    }

    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->config = $configuration;
    }

    protected function convertToAssetThumbLink(string $url): string
    {
        $file = substr($url, strlen('../assets/'));

        $pathinfo = pathinfo($file);
        $fileName = $pathinfo['filename'];
        $fileExtension = $pathinfo['extension'];

        return asset("assets/images/{$fileName}_thumb.{$fileExtension}");
    }
}
