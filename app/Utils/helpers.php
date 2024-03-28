<?php

declare(strict_types=1);

use App\Models\Page;
use App\Models\Translation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;

function localized_date(Carbon $date, bool $withTime = false): string
{
    $genitives = [
        'stycznia',
        'lutego',
        'marca',
        'kwietnia',
        'maja',
        'czerwca',
        'lipca',
        'sierpnia',
        'września',
        'października',
        'listopada',
        'grudnia',
    ];

    $out = $date->format('d') . ' ' . $genitives[$date->format('m') - 1] . ' ' . $date->format('Y');

    if ($withTime) {
        $out .= ' ' . $date->format('H:i');
    }

    return $out;
}

function blog_title(int $pageNumber): string
{
    $title = config('app.name') . ' | ';

    if ($pageNumber === 1) {
        return $title . __('app.branding.description');
    }

    return $title . __('pagination.page') . " $pageNumber";
}

function page_title(string $title): string
{
    return $title . ' | ' . config('app.name');
}

function localized_page_route(string $polishSlug): string
{
    if (app()->getLocale() === 'pl') {
        return route('page', [$polishSlug]);
    }

    $translation = Translation::query()
        ->where('canonical_slug', '=', $polishSlug)
        ->where('language', '=', 'en')
        ->where('type', '=', Page::getTranslatableType())
        ->first();

    $slug = $translation->translated_slug ?? $polishSlug;

    return route('page', [$slug]);
}

/**
 * Determines whether given menu item should be marked active.
 *
 * @param array|string $conditions
 * @return bool
 */
function is_menu_link_active($conditions)
{
    $conditions = (array) $conditions;
    $currentRoute = Request::route();
    $routeName = isset($currentRoute) ? $currentRoute->getName() : null;

    foreach ($conditions as $condition) {
        if (Str::startsWith($condition, 'page:')) {
            return Request::url() === route('page', [substr($condition, strlen('page:'))]);
        }

        if ($routeName === $condition) {
            return true;
        }
    }

    return false;
}

/**
 * Marks menu item active if conditions are met.
 *
 * @param array|string $conditions
 * @return string
 */
function if_active($conditions)
{
    $isActive = is_menu_link_active($conditions);

    return $isActive ? 'class="current-menu-item"' : '';
}

function form_error(string $inputName, ViewErrorBag $errors): HtmlString
{
    if ($errors->has($inputName)) {
        $output = '<span class="validation-error">' . $errors->first($inputName) . '</span>';
    } else {
        $output = '';
    }

    return new HtmlString($output);
}
