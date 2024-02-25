<?php

declare(strict_types=1);

namespace App\Content\ContentTypeIndexers;

use App\Models\Page;
use SplFileInfo;

class PageIndexer extends AbstractContentIndexer implements ContentTypeIndexerInterface
{
    public function index(SplFileInfo $file): void
    {
        $this->output->indentedLine($file->getFilename());

        $page = $this->parseContentFile($file->getPathname(), [
            'slug' => $file->getBasename('.md'),
        ]);

        $this->validateMetadata($page->metadata, [
            'slug' => 'alpha_dash|unique:indexer.pages',
            'title' => 'required',
        ]);

        Page::create([
            'title' => $page->metadata['title'],
            'content' => $page->body,
            'slug' => $page->metadata['slug'],
        ]);
    }
}
