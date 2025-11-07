<?php

namespace App\Http\Controllers;

use App\Models\Manga;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $popularMangas = Manga::withCount('chapters')
            ->orderByDesc('chapters_count')
            ->latest()
            ->take(6)
            ->get();

        $latestMangas = Manga::latest()->take(6)->get();

        return view('welcome', [
            'popularMangas' => $popularMangas,
            'latestMangas' => $latestMangas,
        ]);
    }
}
