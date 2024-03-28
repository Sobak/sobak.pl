<?php

declare(strict_types=1);

namespace App\Content\Parsing;

interface PreprocessorInterface
{
    public function process(string $markdown): string;
}
