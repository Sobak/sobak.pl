<?php

declare(strict_types=1);

namespace App\Content\Translation;

use App\Content\Indexing\IndexerException;
use App\Content\Indexing\IndexerOutputInterface;
use App\Models\Translation;

class TranslationsIndexerService
{
    private IndexerOutputInterface $output;

    public function __construct(IndexerOutputInterface $output)
    {
        $this->output = $output;
    }

    public function processTranslations(TranslatableModelInterface $model, array $translations): void
    {
        foreach ($translations as $language => $translatedSlug) {
            $this->ensureModelExists($model, $translatedSlug);

            if ($this->hasCompatibleDuplicate($model, $model->getSlug(), $language) === false) {
                $translation = new Translation();
                $translation->canonical_slug = $model->getSlug();
                $translation->translated_slug = $translatedSlug;
                $translation->language = $language;
                $translation->type = $model->getTranslatableType();
                $translation->save();

                $message = sprintf('> %s translation found: %s', $language, $translatedSlug);
                $this->output->indentedLine($message, 2, IndexerOutputInterface::VERBOSITY_VERBOSE);
            }

            // Create opposite entry
            $oppositeLanguage = $language === 'pl' ? 'en' : 'pl';

            if ($this->hasCompatibleDuplicate($model, $translatedSlug, $oppositeLanguage) === false) {
                $translation = new Translation();
                $translation->canonical_slug = $translatedSlug;
                $translation->translated_slug = $model->getSlug();
                $translation->language = $oppositeLanguage;
                $translation->type = $model->getTranslatableType();
                $translation->save();
            }
        }
    }

    private function ensureModelExists(TranslatableModelInterface $model, string $slug): void
    {
        $result = $model::query()
            ->where('slug', '=', $slug)
            ->exists();

        if ($result === false) {
            $message = sprintf(
                'No %s found with slug: %s',
                $model->getTranslatableType(),
                $slug,
            );

            $this->output->indentedLine($message, 2);

            throw new IndexerException('', 2);
        }
    }

    private function hasCompatibleDuplicate(
        TranslatableModelInterface $model,
        string $language,
        string $slug
    ): bool {
        // A duplicate may exist if both English and Polish articles
        // defined the other side as their translation.
        return Translation::query()
            ->where('canonical_slug', '=', $slug)
            ->where('language', '=', $language)
            ->where('type', '=', $model->getTranslatableType())
            ->exists();
    }
}
