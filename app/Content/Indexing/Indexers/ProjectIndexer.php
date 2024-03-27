<?php

declare(strict_types=1);

namespace App\Content\Indexing\Indexers;

use App\Content\DTO\ProjectDTO;
use App\Content\Indexing\ContentTypeIndexerInterface;
use App\Content\Translation\TranslationsIndexerService;
use App\Models\Project;
use SplFileInfo;

class ProjectIndexer extends AbstractContentIndexer implements ContentTypeIndexerInterface
{
    public static function getModelClass(): string
    {
        return Project::class;
    }

    public static function getTranslatableType(): string
    {
        return 'project';
    }

    public function index(SplFileInfo $file): void
    {
        $this->output->indentedLine($file->getFilename());

        $project = $this->parseContentFile($file->getPathname(), [
            'language' => self::DEFAULT_CONTENT_LANGUAGE,
            'slug' => $file->getBasename('.md'),
            'translations' => [],
            'url' => null,
        ], ProjectDTO::class);

        $this->validateMetadata($project, [
            'date' => 'required|date',
            'slug' => 'alpha_dash|unique:indexer.projects',
            'title' => 'required',
            'thumbnail' => 'required',
            'type' => 'required',
        ]);

        $projectModel = Project::create([
            'title' => $project->getTitle(),
            'content' => $project->getContent(),
            'url' => $project->getUrl(),
            'slug' => $project->getSlug(),
            'type' => $project->getType(),
            'thumbnail' => $project->getThumbnailUrl(),
            'language' => $project->getLanguage(),
            'created_at' => $project->getCreatedAt(),
        ]);

        $translationsIndexer = new TranslationsIndexerService($this->output);
        $translationsIndexer->processTranslations($projectModel, $project->getTranslations());
    }
}
