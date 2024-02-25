<?php

declare(strict_types=1);

namespace App\Content\ContentTypeIndexers;

use App\Models\Project;
use Carbon\Carbon;
use SplFileInfo;

class ProjectIndexer extends AbstractContentIndexer implements ContentTypeIndexerInterface
{
    public function index(SplFileInfo $file): void
    {
        $this->output->indentedLine($file->getFilename());

        $project = $this->parseContentFile($file->getPathname(), [
            'slug' => $file->getBasename('.md'),
            'url' => null,
        ]);

        $this->validateMetadata($project->metadata, [
            'date' => 'required|date',
            'slug' => 'alpha_dash|unique:indexer.projects',
            'title' => 'required',
            'thumbnail' => 'required',
            'type' => 'required',
        ]);

        Project::create([
            'title' => $project->metadata['title'],
            'content' => $project->body,
            'url' => $project->metadata['url'],
            'slug' => $project->metadata['slug'],
            'type' => $project->metadata['type'],
            'thumbnail' => $project->metadata['thumbnail'],
            'created_at' => Carbon::createFromTimestamp(strtotime($project->metadata['date'])),
        ]);
    }
}
