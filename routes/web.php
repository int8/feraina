<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\BetaController;
use App\Http\Controllers\LandingController;
use App\Http\Middleware\AdminBasicAuth;
use Illuminate\Support\Facades\Route;

// Analytics event proxy (no CSRF: POST from Plausible script).
Route::post('/api/event', [AnalyticsController::class, 'event'])->withoutMiddleware('web');

Route::get('/', [LandingController::class, 'home'])->name('home');
Route::get('/polityka-prywatnosci', [LandingController::class, 'privacy'])->name('privacy');
Route::get('/regulamin', [LandingController::class, 'terms'])->name('terms');

Route::get('/signup', [BetaController::class, 'show'])->name('beta.show');
Route::post('/signup', [BetaController::class, 'start'])->middleware('throttle:beta-start')->name('beta.start');
Route::get('/signup/survey', [BetaController::class, 'survey'])->name('beta.survey');
Route::post('/signup/progress', [BetaController::class, 'progress'])->middleware('throttle:beta-progress')->name('beta.progress');
Route::post('/signup/survey', [BetaController::class, 'finish'])->middleware('throttle:beta-start')->name('beta.finish');
Route::get('/signup/thanks', [BetaController::class, 'thanks'])->name('beta.thanks');
Route::get('/signup/confirm/{token}', [BetaController::class, 'confirm'])->name('beta.confirm');
Route::match(['get', 'post'], '/unsubscribe/{token}', [BetaController::class, 'unsubscribe'])->name('unsubscribe');

Route::middleware(AdminBasicAuth::class)->prefix('panel')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/eksport.csv', [AdminController::class, 'exportCsv'])->name('export');
});

// Sceny do nagrania filmów z hero (npm run record:intro / record:whatsapp). Tylko lokalnie.
if (app()->environment('local')) {
    Route::view('/studio/intro', 'studio.intro')->name('studio.intro');
    Route::view('/studio/whatsapp', 'studio.whatsapp')->name('studio.whatsapp');
}
