<?php

declare(strict_types=1);

namespace App\Content\DTO;

use Carbon\Carbon;

class PostDTO extends PageDTO implements ContentDTOInterface
{
    public function __construct(string $content, array $metadata)
    {
        parent::__construct($content, $metadata);
    }

    public function getMetadata(): array
    {
        if (isset($this->metadata['alias'])) {
            $this->metadata['aliases'] = [$this->metadata['alias']];
        }

        if (isset($this->metadata['category'])) {
            $this->metadata['categories'] = [$this->metadata['category']];
        }

        return $this->metadata;
    }

    /** @return string[] */
    public function getAliases(): array
    {
        return $this->metadata['aliases'];
    }

    /** @return string[] */
    public function getCategories(): array
    {
        return $this->metadata['categories'];
    }

    public function getCreatedAt(): Carbon
    {
        return Carbon::createFromTimestamp(strtotime($this->metadata['date']));
    }

    public function getLanguage(): string
    {
        return $this->metadata['language'];
    }

    public function getProject(): ?string
    {
        return $this->metadata['project'] ?? null;
    }

    /** @return string[] */
    public function getTags(): array
    {
        return $this->metadata['tags'];
    }

    public function getTranslations(): array
    {
        return $this->metadata['translations'];
    }

    public function getType(): string
    {
        return $this->metadata['type'];
    }
}
