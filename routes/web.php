<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectController;

Route::paginate('/', [BlogController::class, 'index'])->name('index');

Route::get('blog/{post}', [BlogController::class, 'show'])->name('post');

Route::get('feed', [FeedController::class, 'polish'])->name('feed.all');
Route::get('feed/pl', [FeedController::class, 'polish'])->name('feed.pl');
Route::get('feed/en', [FeedController::class, 'english'])->name('feed.en');

Route::get('change-language/{lang}', [LanguageController::class, 'change'])->where('lang', '[a-z]{2}')->name('language_switch');

Route::paginate('kategoria/{category}', [BlogController::class, 'category'])->name('category');

Route::get('kontakt', [ContactController::class, 'show'])->middleware('stateful')->name('contact');
Route::post('kontakt', [ContactController::class, 'send'])->middleware('stateful');

Route::get('portfolio', [ProjectController::class, 'index'])->name('projects');
Route::get('portfolio/{project}', [ProjectController::class, 'show'])->name('project');

Route::paginate('szukaj', [BlogController::class, 'search'])->name('search');

Route::paginate('tag/{tag}', [BlogController::class, 'tag'])->name('tag');

Route::get('{page}', [PageController::class, 'show'])->name('page');
