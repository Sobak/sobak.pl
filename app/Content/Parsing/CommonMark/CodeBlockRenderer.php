<?php

namespace App\Content\Parsing\CommonMark;

use Kadet\Highlighter\Formatter\HtmlFormatter;
use Kadet\Highlighter\Language\Language;
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
    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        FencedCode::assertInstanceOf($node);

        $source = $node->getLiteral();
        $languageName = $node->getInfo();

        $language = Language::byName(empty($languageName) ? 'text' : $languageName);
        $formatter = new HtmlFormatter();

        $result = \Kadet\Highlighter\highlight($source, $language, $formatter);

        return '<pre class="keylighter" lang="en"><code>' . $result . '</code></pre>';
    }
}
