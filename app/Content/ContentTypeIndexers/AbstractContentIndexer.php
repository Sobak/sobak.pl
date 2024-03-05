<?php

declare(strict_types=1);

namespace App\Content\ContentTypeIndexers;

use App\Content\IndexerException;
use App\Interfaces\OutputInterface;
use App\Utils\CommonMark\CodeBlockRenderer;
use App\Utils\CommonMark\ImageRenderer;
use App\Utils\CommonMark\LinkRenderer;
use Illuminate\Support\Facades\Validator;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractContentIndexer
{
    /** @var string Placeholder which will be replaced with assets root path when parsing content */
    private const ASSETS_PATH_PLACEHOLDER = '{{{assets}}}';

    /** @var string Placeholder which will be replaced with root site URL when parsing content */
    private const BASE_URL_PLACEHOLDER = '{{{base}}}';

    private static bool $isMarkdownParserSetUp = false;
    private static DocParser $markdownParser;
    private static HtmlRenderer $markdownHtmlRenderer;

    protected OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;

        if (self::$isMarkdownParserSetUp === false) {
            $this->setupMarkdownParser();

            self::$isMarkdownParserSetUp = true;
        }
    }

    protected function parseContentFile($path, array $defaultMetadata = []): object
    {
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

        return (object) [
            'body' => $this->parseMarkdown($body),
            'metadata' => array_merge($defaultMetadata, $metadata),
        ];
    }

    protected function validateMetadata($metadata, $rules): void
    {
        $validator = Validator::make($metadata, $rules);

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

        return self::$markdownHtmlRenderer->renderBlock($document);
    }

    private function setupMarkdownParser(): void
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addBlockRenderer('League\CommonMark\Block\Element\FencedCode', new CodeBlockRenderer());
        $environment->addInlineRenderer('League\CommonMark\Inline\Element\Image', new ImageRenderer());
        $environment->addInlineRenderer('League\CommonMark\Inline\Element\Link', new LinkRenderer());

        self::$markdownParser = new DocParser($environment);
        self::$markdownHtmlRenderer = new HtmlRenderer($environment);
    }
}
