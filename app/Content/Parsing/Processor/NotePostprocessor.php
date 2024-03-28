<?php

declare(strict_types=1);

namespace App\Content\Parsing\Processor;

use App\Content\Parsing\PostprocessorInterface;

class NotePostprocessor implements PostprocessorInterface
{
    const HIDDEN_TITLE = 'Note';
    const REGEX = '#<blockquote>(\n)<p><strong>(?P<title>.+?):<\/strong>+(?P<note>.+?)<\/p>(\n)+<\/blockquote>#ms';

    public function process(string $html): string
    {
        return preg_replace_callback(self::REGEX, function ($matches) {
            $title = '';
            if ($matches['title'] !== self::HIDDEN_TITLE) {
                $title = "<strong>{$matches['title']}:</strong> ";
            }

            return '<div class="box box-warning"><p>' . $title . $this->ucfirst(trim($matches['note'])) . '</p></div>';
        }, $html);
    }

    private function ucfirst(string $string): string
    {
        $strlen = mb_strlen($string);
        $firstChar = mb_substr($string, 0, 1);
        $rest = mb_substr($string, 1, $strlen - 1);

        return mb_strtoupper($firstChar) . $rest;
    }
}
