<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ThemeController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'theme' => ['required', Rule::in(['light', 'dark', 'midnight'])],
        ]);

        $theme = $data['theme'];

        $request->session()->put('theme', $theme);

        if (Auth::check()) {
            Auth::user()->forceFill(['theme_preference' => $theme])->save();
        }

        return back();
    }
}
