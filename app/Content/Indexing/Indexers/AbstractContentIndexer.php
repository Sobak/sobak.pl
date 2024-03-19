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
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Renderer\HtmlRenderer;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractContentIndexer
{
    /** @var string Placeholder which will be replaced with assets root path when parsing content */
    private const ASSETS_PATH_PLACEHOLDER = '{{{assets}}}';

    /** @var string Placeholder which will be replaced with root site URL when parsing content */
    private const BASE_URL_PLACEHOLDER = '{{{base}}}';

    private static bool $isMarkdownParserSetUp = false;
    private static MarkdownParser $markdownParser;
    private static HtmlRenderer $markdownHtmlRenderer;

    protected IndexerOutputInterface $output;

    public function __construct(IndexerOutputInterface $output)
    {
        $this->output = $output;

        if (self::$isMarkdownParserSetUp === false) {
            $this->setupMarkdownParser();

            self::$isMarkdownParserSetUp = true;
        }
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
        $content = file_get_contents($path);

        $pattern = '/[\s\r\n]---[\s\r\n]/';

        $parts = preg_split($pattern, PHP_EOL . ltrim($content), 3);

        if (count($parts) < 3) {
            $this->output->indentedLine('FAIL: No YAML front matter found', 2);

            throw new IndexerException('', 2);
        }

        $body = $parts[2];
        $metadata = Yaml::parse(trim($parts[1]));

        // Try to read the title from Markdown
        if (isset($metadata['title']) === false) {
            $bodyLines = explode("\n", $body);

            if (isset($bodyLines[0]) && substr($bodyLines[0], 0, 2) === '# ') {
                $metadata['title'] = substr($bodyLines[0], 2);

                unset($bodyLines[0]);

                $body = implode("\n", $bodyLines);
            }
        }

        $body = strtr($body, [
            self::ASSETS_PATH_PLACEHOLDER => asset('assets/images'),
            self::BASE_URL_PLACEHOLDER => route('index'),
        ]);

        return new $dtoClassString($this->parseMarkdown($body), array_merge($defaultMetadata, $metadata));
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

    private function parseMarkdown($string): string
    {
        $document = self::$markdownParser->parse($string);

        return self::$markdownHtmlRenderer->renderDocument($document)->getContent();
    }

    private function setupMarkdownParser(): void
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());

        $environment->addRenderer(FencedCode::class, new CodeBlockRenderer());
        $environment->addRenderer(Image::class, new ImageRenderer());
        $environment->addRenderer(Link::class, new LinkRenderer());

        self::$markdownParser = new MarkdownParser($environment);
        self::$markdownHtmlRenderer = new HtmlRenderer($environment);
    }
}
