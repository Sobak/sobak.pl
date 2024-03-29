<?php

namespace App\Content\Parsing\CommonMark;

use Kadet\Highlighter\Formatter\HtmlFormatter;
use Kadet\Highlighter\KeyLighter;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

class CodeBlockRenderer implements NodeRendererInterface
{
    /**
     * @param FencedCode $node
     * @param ChildNodeRendererInterface $childRenderer
     * @return string
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        FencedCode::assertInstanceOf($node);

        $source = $node->getLiteral();
        $languageName = $node->getInfo();

        $keylighter = new KeyLighter();
        $keylighter->init();

        $language = $keylighter->getLanguage($languageName ?? 'text');

        $result = $keylighter->highlight($source, $language, new HtmlFormatter());

        return '<pre class="keylighter" lang="en"><code>' . $result . '</code></pre>';
    }
}
