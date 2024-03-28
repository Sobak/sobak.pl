<?php

declare(strict_types=1);

namespace App\Content\Parsing;

use App\Content\Indexing\IndexerException;
use App\Content\Indexing\IndexerOutputInterface;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;

class Parser
{
    private ?IndexerOutputInterface $output;

    public function __construct(?IndexerOutputInterface $output = null)
    {
        $this->output = $output;
    }

    public function parseContent(string $string): RenderedContentWithFrontMatter
    {
        $markdownConverter = CommonMarkFactory::create();

        $result = $markdownConverter->convert($string);

        if ($result instanceof RenderedContentWithFrontMatter === false) {
            if ($this->output !== null) {
                $this->output->indentedLine('FAIL: No YAML front matter found', 2);
            }

            throw new IndexerException('', 2);
        }

        return $result;
    }
}
