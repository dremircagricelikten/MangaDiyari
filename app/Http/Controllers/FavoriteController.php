<?php

namespace App\Http\Controllers;

use App\Models\Manga;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function store(Request $request, Manga $manga): RedirectResponse
    {
        $request->user()->favorites()->syncWithoutDetaching([$manga->id]);

        return back()->with('status', 'Manga favorilere eklendi.');
    }

    public function destroy(Request $request, Manga $manga): RedirectResponse
    {
        $request->user()->favorites()->detach($manga->id);

        return back()->with('status', 'Manga favorilerden çıkarıldı.');
    }
}
