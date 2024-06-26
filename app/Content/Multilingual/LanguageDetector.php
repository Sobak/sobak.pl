<?php

declare(strict_types=1);

namespace App\Content\Multilingual;

use Illuminate\Http\Request;
use Negotiation\LanguageNegotiator;

class LanguageDetector
{
    private const PRIORITIES = ['pl', 'en'];

    public static function detect(Request $request): string
    {
        return self::detectVisitorLanguage(
            $request,
            config('app.locale'),
            $request->cookie('current_language') ?? config('app.locale_override')
        );
    }

    public static function detectVisitorLanguage(
        Request $request,
        string $fallbackLanguage,
        ?string $override
    ): string {
        if (in_array($override, self::PRIORITIES)) {
            return $override;
        }

        $acceptLanguage = $request->headers->get('Accept-Language');
        if (empty($acceptLanguage)) {
            return $fallbackLanguage;
        }

        $languageNegotiator = new LanguageNegotiator();

        try {
            /** @noinspection PhpUnhandledExceptionInspection phpStorm got confused about an interface */
            /** @var \Negotiation\AcceptLanguage|null $language */
            $language = $languageNegotiator->getBest($acceptLanguage, self::PRIORITIES);
        } catch (\Exception $exception) {
            $language = null;
        }

        if ($language === null) {
            return $fallbackLanguage;
        }

        $languageValue = $language->getValue();

        if (in_array($languageValue, self::PRIORITIES)) {
            return $languageValue;
        }

        return $fallbackLanguage;
    }
}
