<?php

use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\MangaController;
use App\Http\Controllers\Frontend\ChapterController as FrontChapterController;
use App\Http\Controllers\Frontend\MangaController as FrontMangaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/mangas/{manga}', [FrontMangaController::class, 'show'])->name('mangas.show');
Route::get('/mangas/{manga}/chapters/{number}', [FrontChapterController::class, 'show'])->name('chapters.show');
Route::get('/search', SearchController::class)->name('search');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
});

Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('mangas', MangaController::class)->except('show');
        Route::resource('chapters', ChapterController::class)->except('show');
    });
