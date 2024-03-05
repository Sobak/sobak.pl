<?php

namespace App\Content\Parsing\CommonMark;

use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Util\RegexHelper;
use League\CommonMark\Xml\XmlNodeRendererInterface;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;
use Stringable;

class LinkRenderer implements NodeRendererInterface, XmlNodeRendererInterface, ConfigurationAwareInterface
{
    use LinksAssets;

    private ConfigurationInterface $config;

    /**
     * @param Link $node
     *
     * {@inheritDoc}
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): Stringable
    {
        Link::assertInstanceOf($node);

        $attrs = $node->data->get('attributes');

        // Conditional customization for blog://foo links
        if ($this->isBlogLink($node->getUrl())) {
            $url = $this->convertToBlogPostLink($node->getUrl());

            $node->setUrl($url);
        }

        // Conditional customization for internal relative links
        if ($this->isInternalAsset($node->getUrl())) {
            $url = $this->convertToAssetLink($node->getUrl());

            $node->setUrl($url);
        }

        $forbidUnsafeLinks = ! $this->config->get('allow_unsafe_links');
        if (! ($forbidUnsafeLinks && RegexHelper::isLinkPotentiallyUnsafe($node->getUrl()))) {
            $attrs['href'] = $node->getUrl();
        }

        if (($title = $node->getTitle()) !== null) {
            $attrs['title'] = $title;
        }

        if (isset($attrs['target']) && $attrs['target'] === '_blank' && ! isset($attrs['rel'])) {
            $attrs['rel'] = 'noopener noreferrer';
        }

        return new HtmlElement('a', $attrs, $childRenderer->renderNodes($node->children()));
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }

    public function getXmlTagName(Node $node): string
    {
        return 'link';
    }

    /**
     * @param Link $node
     *
     * @return array<string, scalar>
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function getXmlAttributes(Node $node): array
    {
        Link::assertInstanceOf($node);

        return [
            'destination' => $node->getUrl(),
            'title' => $node->getTitle() ?? '',
        ];
    }

    private function isBlogLink(string $url): bool
    {
        return str_starts_with($url, 'blog://');
    }

    private function convertToBlogPostLink(string $url): string
    {
        $slug = substr($url, strlen('blog://'));

        return route('post', [$slug]);
    }
}
