<?php

declare(strict_types=1);

namespace App\Content\Translation;

interface TranslatableModelInterface
{
    public function getSlug(): string;

    public function getTranslatableType(): string;
}
