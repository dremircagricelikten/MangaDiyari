<?php

namespace App\Http\Controllers;

use App\Models\Manga;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class MangaSubscriptionController extends Controller
{
    public function store(Manga $manga): RedirectResponse
    {
        $user = Auth::user();
        $user->subscribedMangas()->syncWithoutDetaching([$manga->id]);

        return back()->with('status', 'Manga aboneliğiniz aktifleştirildi.');
    }

    public function destroy(Manga $manga): RedirectResponse
    {
        $user = Auth::user();
        $user->subscribedMangas()->detach($manga->id);

        return back()->with('status', 'Manga aboneliğiniz kaldırıldı.');
    }
}
