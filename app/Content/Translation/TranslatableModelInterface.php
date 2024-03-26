<?php

declare(strict_types=1);

namespace App\Content\Translation;

interface TranslatableModelInterface
{
    public static function getTranslatableType(): string;

    public static function getAllSlugs(): array;

    public function getSlug(): string;
}
