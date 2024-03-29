<?php

declare(strict_types=1);

namespace App\Content\Indexing\Indexers;

use App\Content\DTO\PageDTO;
use App\Content\Indexing\ContentTypeIndexerInterface;
use App\Content\Translation\TranslationsIndexerService;
use App\Models\Page;
use SplFileInfo;

class PageIndexer extends AbstractContentIndexer implements ContentTypeIndexerInterface
{
    public static function getModelClass(): string
    {
        return Page::class;
    }

    public static function getTranslatableType(): string
    {
        return 'page';
    }

    public function index(SplFileInfo $file): void
    {
        $this->output->indentedLine($file->getFilename());

        $page = $this->parseContentFile($file->getPathname(), [
            'language' => self::DEFAULT_CONTENT_LANGUAGE,
            'slug' => $file->getBasename('.md'),
            'translations' => [],
        ], PageDTO::class);

        $this->validateMetadata($page, [
            'slug' => 'alpha_dash|unique:indexer.pages',
            'title' => 'required',
        ]);

        $pageModel = Page::create([
            'title' => $page->getTitle(),
            'content' => $page->getContent(),
            'slug' => $page->getSlug(),
            'language' => $page->getLanguage(),
        ]);

        $translationsIndexer = new TranslationsIndexerService($this->output);
        $translationsIndexer->processTranslations($pageModel, $page->getTranslations());
    }
}
