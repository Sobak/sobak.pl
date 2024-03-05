<?php

namespace App\Content\Parsing\CommonMark;

use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Node;
use League\CommonMark\Node\NodeIterator;
use League\CommonMark\Node\StringContainerInterface;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Util\RegexHelper;
use League\CommonMark\Xml\XmlNodeRendererInterface;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;
use Stringable;

class ImageRenderer implements NodeRendererInterface, XmlNodeRendererInterface, ConfigurationAwareInterface
{
    use LinksAssets;

    private ConfigurationInterface $config;

    /**
     * @param Image $node
     *
     * {@inheritDoc}
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): Stringable
    {
        Image::assertInstanceOf($node);

        $attrs = $node->data->get('attributes');

        // Conditional customization
        $isInternalAsset = $this->isInternalAsset($node->getUrl());

        if ($isInternalAsset) {
            $src = $this->convertToAssetLink($node->getUrl());

            $node->setUrl($src);
        }

        $forbidUnsafeLinks = ! $this->config->get('allow_unsafe_links');
        if ($forbidUnsafeLinks && RegexHelper::isLinkPotentiallyUnsafe($node->getUrl())) {
            $attrs['src'] = '';
        } else {
            $attrs['src'] = $node->getUrl();
        }

        $attrs['alt'] = $this->getAltText($node);

        if (($title = $node->getTitle()) !== null) {
            $attrs['title'] = $title;
        }

        // Customize HTML output in case of the internal asset
        if ($isInternalAsset) {
            return new HtmlElement(
                'a',
                ['href' => $node->getUrl()],
                new HtmlElement('img', $attrs, '', true)
            );
        }

        return new HtmlElement('img', $attrs, '', true);
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }

    public function getXmlTagName(Node $node): string
    {
        return 'image';
    }

    /**
     * @param Image $node
     *
     * @return array<string, scalar>
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function getXmlAttributes(Node $node): array
    {
        Image::assertInstanceOf($node);

        return [
            'destination' => $node->getUrl(),
            'title' => $node->getTitle() ?? '',
        ];
    }

    private function getAltText(Image $node): string
    {
        $altText = '';

        foreach ((new NodeIterator($node)) as $n) {
            if ($n instanceof StringContainerInterface) {
                $altText .= $n->getLiteral();
            } elseif ($n instanceof Newline) {
                $altText .= "\n";
            }
        }

        return $altText;
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
