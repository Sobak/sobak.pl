<?php

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
 * @param $page
 * @return string
 */
function blog_title($page)
{
    return config('app.name') . ' | ' . ($page === 1 ? config('app.description') : "Strona {$page}");
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

/**
 * Shuffle an array preserving the keys.
 *
 * @param $array
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
