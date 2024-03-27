<?php

declare(strict_types=1);

namespace App\Content\Translation;

use App\Models\Translation;

trait HasTranslations
{
    public function getTranslation(string $language): ?self
    {
        $translation = Translation::query()
            ->where('canonical_slug', '=', $this->getSlug())
            ->where('language', '=', $language)
            ->where('type', '=', self::getTranslatableType())
            ->first();

        if ($translation === null) {
            return null;
        }

        return $this->where('slug', '=', $translation->translated_slug)->first();
    }
}
