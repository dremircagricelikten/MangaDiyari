<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user()->load([
            'favorites' => fn ($query) => $query->withCount('chapters')->orderBy('title'),
            'readingList' => fn ($query) => $query->withCount('chapters')->orderBy('title'),
        ]);

        return view('dashboard', [
            'user' => $user,
            'favorites' => $user->favorites,
            'readingList' => $user->readingList,
        ]);
    }
}
