<?php

declare(strict_types=1);

namespace App\Content\Indexing\Indexers;

use App\Content\DTO\ContentDTOInterface;
use App\Content\Indexing\IndexerException;
use App\Content\Indexing\IndexerOutputInterface;
use App\Content\Parsing\CommonMark\CodeBlockRenderer;
use App\Content\Parsing\CommonMark\ImageRenderer;
use App\Content\Parsing\CommonMark\LinkRenderer;
use Illuminate\Support\Facades\Validator;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\MarkdownConverter;

abstract class AbstractContentIndexer
{
    /** @var string Placeholder which will be replaced with assets root path when parsing content */
    private const ASSETS_PATH_PLACEHOLDER = '{{{assets}}}';

    /** @var string Placeholder which will be replaced with root site URL when parsing content */
    private const BASE_URL_PLACEHOLDER = '{{{base}}}';

    protected IndexerOutputInterface $output;

    public function __construct(IndexerOutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param string $path
     * @param array $defaultMetadata
     * @param class-string<ContentDTOInterface> $dtoClassString
     * @return ContentDTOInterface
     */
    protected function parseContentFile(
        string $path,
        array $defaultMetadata,
        string $dtoClassString
    ): ContentDTOInterface {
        $content = strtr(file_get_contents($path), [
            self::ASSETS_PATH_PLACEHOLDER => asset('assets/images'),
            self::BASE_URL_PLACEHOLDER => route('index'),
        ]);

        $result = $this->parseMarkdown($content);

        $metadata = array_merge($defaultMetadata, $result->getFrontMatter());

        return new $dtoClassString($result->getContent(), $metadata);
    }

    protected function validateMetadata(ContentDTOInterface $contentDTO, $rules): void
    {
        $validator = Validator::make($contentDTO->getMetadata(), $rules);

        foreach ($validator->errors()->all() as $error) {
            $this->output->indentedLine("FAIL: $error", 2);
        }

        if ($validator->fails()) {
            throw new IndexerException('', 3);
        }
    }

    private function parseMarkdown($string): RenderedContentWithFrontMatter
    {
        static $markdownConverter;

        if ($markdownConverter === null) {
            $environment = new Environment();
            $environment->addExtension(new CommonMarkCoreExtension());
            $environment->addExtension(new FrontMatterExtension());
            $environment->addExtension(new FootnoteExtension());
            $environment->addExtension(new StrikethroughExtension());

            $environment->addRenderer(FencedCode::class, new CodeBlockRenderer());
            $environment->addRenderer(Image::class, new ImageRenderer());
            $environment->addRenderer(Link::class, new LinkRenderer());

            $markdownConverter = new MarkdownConverter($environment);
        }

        $result = $markdownConverter->convert($string);

        if ($result instanceof RenderedContentWithFrontMatter === false) {
            $this->output->indentedLine('FAIL: No YAML front matter found', 2);

            throw new IndexerException('', 2);
        }

        return $result;
    }
}
