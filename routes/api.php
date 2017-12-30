<?php

Route::get('stats', 'StatsController@index');
Route::get('twitter/entries', 'TwitterController@entries')->name('twitter.entries');
