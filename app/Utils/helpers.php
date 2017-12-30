<?php

/**
 * Tries to mimic important behavior of its WordPress counterpart.
 *
 * Watch out! This function is full of bad magic. Stay alerted, do
 * not come close and do not invite it for a dinner. Most importantly,
 * though, refactor whenever possible!
 *
 * @return string
 */
function body_class()
{
    $classes = [];

    $routeName = \Request::route()->getName();

    if ($routeName === 'category') {
        $classes[] = 'archive';
        $classes[] = 'category';
    }

    if ($routeName === 'tag') {
        $classes[] = 'archive';
        $classes[] = 'tag';
    }

    return join(' ', $classes);
}

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
 * Generate standard format page title.
 *
 * @param $title
 * @return string
 */
function page_title($title)
{
    return $title . ' | ' . config('app.name');
}
