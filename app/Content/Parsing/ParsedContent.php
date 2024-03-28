<?php

declare(strict_types=1);

namespace App\Content\Parsing;

class ParsedContent
{
    private string $content;
    private array $metadata;

    public function __construct(string $content, array $metadata)
    {
        $this->content = $content;
        $this->metadata = $metadata;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }
}
