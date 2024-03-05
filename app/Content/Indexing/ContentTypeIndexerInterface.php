<?php

declare(strict_types=1);

namespace App\Content\Indexing;

use SplFileInfo;

interface ContentTypeIndexerInterface
{
    public function __construct(IndexerOutputInterface $output);

    public function index(SplFileInfo $file): void;
}
