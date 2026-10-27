<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesPageController;
use App\Http\Controllers\SitemapController;
use App\Models\Region;

Route::get('/', function () {
    return view('layouts.main-page.home');
});


// 1. Keyword + region
Route::get('/{keywords}/{type}/{locationSlug}/terbaik', [SalesPageController::class, 'keywordWithRegion'])
    ->where([
        'keywords' => '[a-zA-Z0-9\-]+',
        'type' => '(kota|kecamatan|kelurahan|desa)',
        'locationSlug' => '[a-z0-9\-]+'
    ])
    ->name('sales.keyword.region');

// 2. Keyword only
Route::get('/les-privat/{keywords}', [SalesPageController::class, 'keywordOnly'])
    ->where([
        'keywords' => '[a-zA-Z0-9\-]+',
    ])
    ->name('sales.keyword.only');

Route::get('/{type}/{locationSlug}', function ($type, $locationSlug) {

    $exists = Region::where('type', $type)
        ->where('slug', $locationSlug)
        ->exists();

    if ($exists) {
        return redirect('/');
    }

    abort(404);

})->where([
    'type' => '(kota|kecamatan|kelurahan|desa)',
    'locationSlug' => '[a-z0-9\-]+',
]);