<?php

declare(strict_types=1);

namespace App\Content\Parsing;

interface PostprocessorInterface
{
    public function process(string $html): string;
}
