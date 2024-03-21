<?php

declare(strict_types=1);

namespace App\Content\Parsing;

use App\Content\Parsing\CommonMark\CodeBlockRenderer;
use App\Content\Parsing\CommonMark\ImageRenderer;
use App\Content\Parsing\CommonMark\LinkRenderer;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\MarkdownConverter;

class CommonMarkFactory
{
    private static ?MarkdownConverter $instance = null;

    public static function create(): MarkdownConverter
    {
        if (self::$instance === null) {
            $environment = new Environment();
            $environment->addExtension(new CommonMarkCoreExtension());
            $environment->addExtension(new FrontMatterExtension());
            $environment->addExtension(new FootnoteExtension());
            $environment->addExtension(new StrikethroughExtension());

            $environment->addRenderer(FencedCode::class, new CodeBlockRenderer());
            $environment->addRenderer(Image::class, new ImageRenderer());
            $environment->addRenderer(Link::class, new LinkRenderer());

            self::$instance = new MarkdownConverter($environment);
        }

        return self::$instance;
    }
}
