<?php

declare(strict_types=1);

namespace App\Content\DTO;

use Carbon\Carbon;

class ProjectDTO extends PageDTO implements ContentDTOInterface
{
    public function __construct(string $content, array $metadata)
    {
        parent::__construct($content, $metadata);
    }

    public function getCreatedAt(): Carbon
    {
        return Carbon::createFromTimestamp(strtotime($this->metadata['date']));
    }

    public function getThumbnailUrl(): string
    {
        return $this->metadata['thumbnail'];
    }

    public function getType(): string
    {
        return $this->metadata['type'];
    }

    public function getUrl(): ?string
    {
        return $this->metadata['url'] ?? null;
    }
}
