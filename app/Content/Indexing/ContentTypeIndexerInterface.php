<?php

declare(strict_types=1);

namespace App\Content\Indexing;

use SplFileInfo;

interface ContentTypeIndexerInterface
{
    public static function getModelClass(): string;

    public static function getTranslatableType(): string;

    public function __construct(IndexerOutputInterface $output);

    public function setTranslations(array $translations): void;

    public function index(SplFileInfo $file): void;
}
