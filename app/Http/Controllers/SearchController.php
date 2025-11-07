<?php

namespace App\Http\Controllers;

use App\Models\Manga;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $query = $request->string('q')->trim()->toString();
        $status = $request->string('status')->trim()->toString();
        $genre = $request->string('genre')->trim()->toString();
        $sort = $request->string('sort')->trim()->toString();

        $mangas = Manga::query()->withCount('chapters');

        if ($query !== '') {
            $mangas->where(function ($builder) use ($query) {
                $builder->where('title', 'like', "%{$query}%")
                    ->orWhere('summary', 'like', "%{$query}%")
                    ->orWhere('author', 'like', "%{$query}%");
            });
        }

        if ($status !== '') {
            $mangas->where('status', $status);
        }

        if ($genre !== '') {
            $mangas->whereJsonContains('genres', $genre);
        }

        if ($sort === 'popular') {
            $mangas->orderByDesc('chapters_count')->orderByDesc('created_at');
        } else {
            $mangas->latest();
        }

        $results = $mangas
            ->paginate(12)
            ->withQueryString();

        $availableStatuses = Manga::query()
            ->select('status')
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        $availableGenres = Manga::query()
            ->select('genres')
            ->whereNotNull('genres')
            ->get()
            ->flatMap(fn ($manga) => $manga->genres ?? [])
            ->unique()
            ->sort()
            ->values();

        return view('search.index', [
            'results' => $results,
            'query' => $query,
            'status' => $status,
            'genre' => $genre,
            'sort' => $sort,
            'availableStatuses' => $availableStatuses,
            'availableGenres' => $availableGenres,
        ]);
    }
}
