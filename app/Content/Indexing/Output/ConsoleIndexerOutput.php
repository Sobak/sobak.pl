<?php

declare(strict_types=1);

namespace App\Content\Indexing\Output;

use App\Content\Indexing\IndexerOutputInterface;
use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface as SymfonyOutputInterface;

class ConsoleIndexerOutput implements IndexerOutputInterface
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

    public function line(string $string, int $verbosity = self::VERBOSITY_NORMAL): void
    {
        $this->writeLine($string, null, $verbosity);
    }

    public function warning(string $string, int $verbosity = self::VERBOSITY_NORMAL): void
    {
        if ($this->output->getFormatter()->hasStyle('warning') === false) {
            $style = new OutputFormatterStyle('yellow');

            $this->output->getFormatter()->setStyle('warning', $style);
        }

        $this->writeLine($string, 'warning', $verbosity);
    }

    public function error(string $string, int $verbosity = self::VERBOSITY_NORMAL): void
    {
        if ($this->output->getFormatter()->hasStyle('error') === false) {
            $style = new OutputFormatterStyle('red');

            $this->output->getFormatter()->setStyle('error', $style);
        }

        $this->writeLine($string, 'warning', $verbosity);
    }

    public function indentedLine(string $text, int $levels = 1, int $verbosity = self::VERBOSITY_NORMAL): void
    {
        $indentation = str_repeat(' ', $levels * self::INDENTATION_STEP);

        $this->writeLine($indentation . $text, null, $verbosity);
    }

    private function writeLine(string $string, ?string $style = null, int $verbosity = self::VERBOSITY_NORMAL): void
    {
        $styled = $style ? "<$style>$string</$style>" : $string;

        $this->output->writeln($styled, $this->parseVerbosity($verbosity));
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
