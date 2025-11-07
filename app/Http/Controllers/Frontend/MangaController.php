<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Manga;
use Illuminate\Contracts\View\View;

class MangaController extends Controller
{
    public function show(Manga $manga): View
    {
        $manga->load(['chapters' => function ($query) {
            $query->orderByDesc('number');
        }]);

        return view('mangas.show', [
            'manga' => $manga,
        ]);
    }
}
