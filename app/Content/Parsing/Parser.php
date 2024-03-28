<?php

declare(strict_types=1);

namespace App\Content\Parsing;

use App\Content\Indexing\IndexerException;
use App\Content\Indexing\IndexerOutputInterface;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;

class Parser
{
    private ?IndexerOutputInterface $output;

    /** @var PreprocessorInterface[] */
    private array $preprocessors = [];

    /** @var PostprocessorInterface[] */
    private array $postprocessors = [];

    public static function create(?IndexerOutputInterface $output = null): self
    {
        $parser = new self($output);

        return $parser;
    }

    public function __construct(?IndexerOutputInterface $output = null)
    {
        $this->output = $output;
    }

    public function addPreprocessor(PreprocessorInterface $processor): void
    {
        $this->preprocessors[] = $processor;
    }

    public function addPostprocessor(PostprocessorInterface $processor): void
    {
        $this->postprocessors[] = $processor;
    }

    public function parseContent(string $string): ParsedContent
    {
        $markdownConverter = CommonMarkFactory::create();

        foreach ($this->preprocessors as $preprocessor) {
            $string = $preprocessor->process($string);
        }

        $result = $markdownConverter->convert($string);

        if ($result instanceof RenderedContentWithFrontMatter === false) {
            if ($this->output !== null) {
                $this->output->indentedLine('FAIL: No YAML front matter found', 2);
            }

            throw new IndexerException('', 2);
        }

        $result = new ParsedContent($result->getContent(), $result->getFrontMatter());

        foreach ($this->postprocessors as $postprocessor) {
            $result->setContent($postprocessor->process($result->getContent()));
        }

        return $result;
    }
}
