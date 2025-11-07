<?php

use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\MangaController;
use App\Http\Controllers\Frontend\ChapterController as FrontChapterController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\Frontend\MangaController as FrontMangaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MangaSubscriptionController;
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

    Route::post('mangas/{manga}/favorite', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('mangas/{manga}/favorite', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    Route::post('mangas/{manga}/reading-list', [ReadingListController::class, 'store'])->name('reading-list.store');
    Route::patch('mangas/{manga}/reading-list', [ReadingListController::class, 'update'])->name('reading-list.update');
    Route::delete('mangas/{manga}/reading-list', [ReadingListController::class, 'destroy'])->name('reading-list.destroy');
});

Route::middleware('auth')
    ->group(function () {
        Route::post('/mangas/{manga}/comments', [CommentController::class, 'storeForManga'])->name('manga-comments.store');
        Route::post('/mangas/{manga}/chapters/{number}/comments', [CommentController::class, 'storeForChapter'])->name('chapter-comments.store');
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
        Route::post('/mangas/{manga}/subscribe', [MangaSubscriptionController::class, 'store'])->name('mangas.subscribe');
        Route::delete('/mangas/{manga}/subscribe', [MangaSubscriptionController::class, 'destroy'])->name('mangas.unsubscribe');
    });

Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('chapters/upload', [ChapterController::class, 'uploadForm'])->name('chapters.upload');
        Route::post('chapters/upload', [ChapterController::class, 'bulkStore'])->name('chapters.upload.store');
        Route::resource('mangas', MangaController::class)->except('show');
        Route::resource('chapters', ChapterController::class)->except('show');
    });
