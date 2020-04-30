<?php

declare(strict_types=1);

namespace App\Interfaces;

interface OutputInterface
{
    public const VERBOSITY_NORMAL = 0;
    public const VERBOSITY_VERBOSE = 1;

    public function line(string $string, ?string $style = null, int $verbosity = self::VERBOSITY_NORMAL);

    public function indentedLine(string $text, int $levels = 1, int $verbosity = self::VERBOSITY_NORMAL);
}
