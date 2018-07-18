<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\HtmlString;
use Illuminate\Support\ViewErrorBag;

/**
 * There must be a better way...
 *
 * @param \Carbon\Carbon $date
 * @return string
 */
function localized_date(\Carbon\Carbon $date)
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

    return $date->format('d') . ' ' . $genitives[$date->format('m') - 1] . ' ' . $date->format('Y');
}

/**
 * Generate proper blog title given the current page.
 *
 * @param int $page Current page number
 * @return string
 */
function blog_title($page)
{
    return config('app.name') . ' | ' . ($page === 1 ? config('app.description') : "Strona {$page}");
}

/**
 * Generate standard format page title.
 *
 * @param string $title
 * @return string
 */
function page_title($title)
{
    return $title . ' | ' . config('app.name');
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
        if (starts_with($condition, 'page:')) {
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

/**
 * Shuffle an array preserving the keys.
 *
 * @param array $array
 * @return array
 */
function shuffle_assoc($array) {
    $keys = array_keys($array);

    shuffle($keys);

    $new = [];
    foreach($keys as $key) {
        $new[$key] = $array[$key];
    }

    return $new;
}

/**
 * Handle errors for the given form input
 *
 * @param string $inputName Form input name
 * @param \Illuminate\Support\ViewErrorBag $errors Form errors bag instance
 * @return \Illuminate\Support\HtmlString
 */
function form_error($inputName, ViewErrorBag $errors)
{
    if ($errors->has($inputName)) {
        $output = '<span class="validation-error">' . $errors->first($inputName) . '</span>';
    } else {
        $output = '';
    }

    return new HtmlString($output);
}

/**
 * Linkify URLs, mentions and hashtags inside the Twitter entry.
 *
 * @param string $status
 * @return string
 */
function twitter_parse_status($status)
{
    $status = preg_replace_callback("/((https?|s?ftp|ssh)\:\/\/[^\"\s\<\>]*[^.,;'\">\:\s\<\>\)\]\!])/", function ($matches) {
        return '<a href="' . $matches[0] . '">' . $matches[0] . '</a>';
    }, $status);

    $status = preg_replace_callback("/\B@([_a-z0-9]+)/i", function ($matches) {
        return '@<a href="https://twitter.com/' . $matches[1] . '">' . $matches[1] . '</a>';
    }, $status);

    $status = preg_replace_callback("/(^|[^&\w'\"]+)\#([a-zA-Z0-9_^\"^<]+)/", function ($matches) {
        if (substr($matches[0], -1) == '""' || substr($matches[0], -1) == '<') {
            return $matches[0];
        }

        return '<strong>#<a href="https://twitter.com/hashtag/' . $matches[2] . '">' . $matches[2] . '</a></strong>';
    }, $status);

    $status = nl2br($status);

    return $status;
}

/**
 * Return human-readable date difference for Twitter entry.
 *
 * @param string $datetime
 * @return string
 */
function twitter_relative_time($datetime)
{
    $datetime = new Datetime($datetime);
    $now = new Datetime();

    $delta = floor($now->getTimestamp() - $datetime->getTimestamp());

    if ($delta < 60) {
        return 'Mniej niż minutę temu';
    } elseif ($delta < 120) {
        return 'Około minuty temu';
    } elseif ($delta < (60 * 60)) {
        $reltime = floor($delta / 60);
        return "$reltime minut temu";
    } elseif ($delta < (120 * 60)) {
        return 'około godzinę temu';
    } elseif ($delta < (24 * 60 * 60)) {
        $reltime = floor($delta / 3600);
        return "około $reltime godzin temu";
    } elseif ($delta < (48 * 60 * 60)) {
        return 'wczoraj';
    } else {
        $reltime = floor($delta / 86400);
        return "$reltime dni temu";
    }
}
