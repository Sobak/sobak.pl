<?php

declare(strict_types=1);

namespace App\Content\Indexing\Indexers;

use App\Content\DTO\ContentDTOInterface;
use App\Content\Indexing\IndexerException;
use App\Content\Indexing\IndexerOutputInterface;
use App\Content\Parsing\CommonMarkFactory;
use App\Content\Parsing\Parser;
use Illuminate\Support\Facades\Validator;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;

abstract class AbstractContentIndexer
{
    /** @var string Decoupled from the `app.locale` config to avoid weird results when using `app.locale_override` */
    protected const DEFAULT_CONTENT_LANGUAGE = 'pl';

    /** @var string Placeholder which will be replaced with assets root path when parsing content */
    private const ASSETS_PATH_PLACEHOLDER = '{{{assets}}}';

    /** @var string Placeholder which will be replaced with root site URL when parsing content */
    private const BASE_URL_PLACEHOLDER = '{{{base}}}';

    protected IndexerOutputInterface $output;
    protected array $translations = [];
    private Parser $parser;

    public function __construct(IndexerOutputInterface $output)
    {
        $this->output = $output;

        $this->parser = new Parser($this->output);
    }

    public function setTranslations(array $translations): void
    {
        $this->translations = $translations;
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

        $result = $this->parser->parseContent($content);

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
}
