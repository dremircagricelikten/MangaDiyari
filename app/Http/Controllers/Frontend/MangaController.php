<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Manga;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class MangaController extends Controller
{
    public function show(Manga $manga): View
    {
        $manga->load([
            'chapters' => function ($query) {
                $query->orderByDesc('number');
            },
            'comments' => function ($query) {
                $query->latest()->with('user');
            },
        ]);

        $isSubscribed = false;

        if (Auth::check()) {
            $isSubscribed = $manga->subscribers()
                ->where('users.id', Auth::id())
                ->exists();
        }

        return view('mangas.show', [
            'manga' => $manga,
            'isSubscribed' => $isSubscribed,
        ]);
    }
}
