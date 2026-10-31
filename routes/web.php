<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BetaController;
use App\Http\Controllers\LandingController;
use App\Http\Middleware\AdminBasicAuth;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'home'])->name('home');
Route::get('/polityka-prywatnosci', [LandingController::class, 'privacy'])->name('privacy');
Route::get('/regulamin', [LandingController::class, 'terms'])->name('terms');

Route::get('/beta', [BetaController::class, 'show'])->name('beta.show');
Route::post('/beta', [BetaController::class, 'start'])->middleware('throttle:beta-start')->name('beta.start');
Route::get('/beta/ankieta', [BetaController::class, 'survey'])->name('beta.survey');
Route::post('/beta/ankieta/postep', [BetaController::class, 'progress'])->middleware('throttle:beta-progress')->name('beta.progress');
Route::post('/beta/ankieta', [BetaController::class, 'finish'])->middleware('throttle:beta-start')->name('beta.finish');
Route::get('/beta/dziekujemy', [BetaController::class, 'thanks'])->name('beta.thanks');
Route::get('/beta/potwierdz/{token}', [BetaController::class, 'confirm'])->name('beta.confirm');
Route::match(['get', 'post'], '/wypisz/{token}', [BetaController::class, 'unsubscribe'])->name('unsubscribe');

Route::middleware(AdminBasicAuth::class)->prefix('panel')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/eksport.csv', [AdminController::class, 'exportCsv'])->name('export');
});

// Sceny do nagrania filmów z hero (npm run record:intro / record:whatsapp). Tylko lokalnie.
if (app()->environment('local')) {
    Route::view('/studio/intro', 'studio.intro')->name('studio.intro');
    Route::view('/studio/whatsapp', 'studio.whatsapp')->name('studio.whatsapp');
}
