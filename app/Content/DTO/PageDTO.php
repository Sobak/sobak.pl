<?php

declare(strict_types=1);

namespace App\Content\DTO;

class PageDTO implements ContentDTOInterface
{
    protected array $metadata;

    private string $content;

    public function __construct(string $content, array $metadata)
    {
        $this->content = $content;
        $this->metadata = $metadata;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getSlug(): string
    {
        return (string) $this->metadata['slug'];
    }

    public function getTitle(): string
    {
        return $this->metadata['title'];
    }
}
