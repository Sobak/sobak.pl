<?php

declare(strict_types=1);

namespace App\Content\Indexing\Indexers;

use App\Content\DTO\ProjectDTO;
use App\Content\Indexing\ContentTypeIndexerInterface;
use App\Models\Project;
use SplFileInfo;

class ProjectIndexer extends AbstractContentIndexer implements ContentTypeIndexerInterface
{
    public function index(SplFileInfo $file): void
    {
        $this->output->indentedLine($file->getFilename());

        $project = $this->parseContentFile($file->getPathname(), [
            'slug' => $file->getBasename('.md'),
            'url' => null,
        ], ProjectDTO::class);

        $this->validateMetadata($project, [
            'date' => 'required|date',
            'slug' => 'alpha_dash|unique:indexer.projects',
            'title' => 'required',
            'thumbnail' => 'required',
            'type' => 'required',
        ]);

        Project::create([
            'title' => $project->getTitle(),
            'content' => $project->getContent(),
            'url' => $project->getUrl(),
            'slug' => $project->getSlug(),
            'type' => $project->getType(),
            'thumbnail' => $project->getThumbnailUrl(),
            'created_at' => $project->getCreatedAt(),
        ]);
    }
}
