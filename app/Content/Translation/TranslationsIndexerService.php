<?php

declare(strict_types=1);

namespace App\Content\Translation;

use App\Content\Indexing\IndexerException;
use App\Content\Indexing\IndexerOutputInterface;
use App\Models\Translation;

class TranslationsIndexerService
{
    private IndexerOutputInterface $output;

    /** @var array<string, class-string<TranslatableModelInterface> */
    private static array $translatableTypeToModel = [];

    public function __construct(IndexerOutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param class-string<TranslatableModelInterface> $modelClassString
     * @return void
     */
    public static function registerTranslatableModel(string $modelClassString): void
    {
        self::$translatableTypeToModel[$modelClassString::getTranslatableType()] = $modelClassString;
    }

    public function processTranslations(TranslatableModelInterface $model, array $translations): void
    {
        foreach ($translations as $language => $translatedSlug) {
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

    public function ensureAllTranslationsExist(string $type): void
    {
        $model = self::$translatableTypeToModel[$type];

        /** @var string[] $allSlugs */
        $allSlugs = $model::getAllSlugs();

        $canonicalSlugs = Translation::where('type', '=', $type)->pluck('canonical_slug');
        $translatedSlugs = Translation::where('type', '=', $type)->pluck('translated_slug');

        $nonExistentSlugs = array_merge(
            $canonicalSlugs->diff($allSlugs)->toArray(),
            $translatedSlugs->diff($allSlugs)->toArray(),
        );

        $nonExistentSlugs = array_unique($nonExistentSlugs);

        foreach ($nonExistentSlugs as $nonExistentSlug) {
            $message = sprintf(
                '> FAIL: No %s found with slug: %s',
                $model::getTranslatableType(),
                $nonExistentSlug,
            );

            $this->output->indentedLine($message, 2);
        }

        if (count($nonExistentSlugs) > 0) {
            throw new IndexerException('', 2);
        }
    }

    private function hasCompatibleDuplicate(
        TranslatableModelInterface $model,
        string $slug,
        string $language
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
