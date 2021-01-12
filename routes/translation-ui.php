<?php

use Illuminate\Support\Facades\Route;
use Twigger\Translate\Http\Controllers\Api\DatabaseTranslationController;
use Twigger\Translate\Http\Controllers\UI\DashboardController;

Route::get('/', [\Twigger\Translate\Http\Controllers\UI\TranslationController::class, 'index'])
    ->name('translate.ui.translations');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('translate.ui.dashboard');

Route::prefix('api')->group(function() {
    Route::apiResource('translations', DatabaseTranslationController::class)
        ->parameter('translations', 'database_translation')
        ->only(['index', 'store', 'update', 'destroy']);
});
