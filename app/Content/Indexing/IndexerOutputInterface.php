<?php

declare(strict_types=1);

namespace App\Content\Indexing;

interface IndexerOutputInterface
{
    public const VERBOSITY_NORMAL = 0;
    public const VERBOSITY_VERBOSE = 1;

    public function line(string $string, int $verbosity = self::VERBOSITY_NORMAL): void;

    public function warning(string $string, int $verbosity = self::VERBOSITY_NORMAL): void;

    public function error(string $string, int $verbosity = self::VERBOSITY_NORMAL): void;

    public function indentedLine(string $text, int $levels = 1, int $verbosity = self::VERBOSITY_NORMAL): void;
}
