<?php

declare(strict_types=1);

namespace App\Content\ContentTypeIndexers;

use App\Interfaces\OutputInterface;
use SplFileInfo;

interface ContentTypeIndexerInterface
{
    public function __construct(OutputInterface $output);

    public function index(SplFileInfo $file): void;
}
