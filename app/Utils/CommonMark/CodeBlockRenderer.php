<?php

namespace App\Utils\CommonMark;

use Kadet\Highlighter\Formatter\HtmlFormatter;
use Kadet\Highlighter\Language\Language;
use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\ElementRendererInterface;

class CodeBlockRenderer implements BlockRendererInterface
{
    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = false)
    {
        $source = $block->getStringContent();
        $languageName = $block->getInfo();

        $language = Language::byName(empty($languageName) ? 'text' : $languageName);
        $formatter = new HtmlFormatter();

        $result = \Kadet\Highlighter\highlight($source, $language, $formatter);

        return '<pre class="keylighter"><code>' . $result . '</code></pre>';
    }
}
