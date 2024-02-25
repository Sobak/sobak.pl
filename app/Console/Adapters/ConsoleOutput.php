<?php

declare(strict_types=1);

namespace App\Console\Adapters;

use App\Interfaces\OutputInterface;
use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Output\OutputInterface as SymfonyOutputInterface;

class ConsoleOutput implements OutputInterface
{
    /** @var integer Indentation level step used for indexer's output */
    private const INDENTATION_STEP = 2;

    private int $defaultVerbosity = SymfonyOutputInterface::VERBOSITY_NORMAL;
    private OutputStyle $output;
    private array $verbosityMap = [
        self::VERBOSITY_NORMAL => SymfonyOutputInterface::VERBOSITY_NORMAL,
        self::VERBOSITY_VERBOSE => SymfonyOutputInterface::VERBOSITY_VERBOSE,
    ];

    public function __construct(OutputStyle $output)
    {
        $this->output = $output;
    }

    public function line(string $string, ?string $style = null, int $verbosity = self::VERBOSITY_NORMAL)
    {
        $styled = $style ? "<$style>$string</$style>" : $string;

        $this->output->writeln($styled, $this->parseVerbosity($verbosity));
    }

    public function indentedLine(string $text, int $levels = 1, int $verbosity = self::VERBOSITY_NORMAL)
    {
        $indentation = str_repeat(' ', $levels * self::INDENTATION_STEP);

        $this->line($indentation . $text, null, $verbosity);
    }

    private function parseVerbosity(int $level = null): int
    {
        $verbosity = $this->defaultVerbosity;
        if (isset($this->verbosityMap[$level])) {
            $verbosity = $this->verbosityMap[$level];
        }

        return $verbosity;
    }
}
