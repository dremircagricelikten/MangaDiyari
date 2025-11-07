<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Manga;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function storeForManga(Request $request, Manga $manga): RedirectResponse
    {
        $this->storeComment($request, $manga);

        return back()->with('status', 'Yorumunuz başarıyla paylaşıldı.');
    }

    public function storeForChapter(Request $request, Manga $manga, string $number): RedirectResponse
    {
        $chapter = $manga->chapters()->where('number', $number)->firstOrFail();

        $this->storeComment($request, $chapter);

        return back()->with('status', 'Yorumunuz başarıyla paylaşıldı.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('status', 'Yorum silindi.');
    }

    private function storeComment(Request $request, Model $commentable): void
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $commentable->comments()->create([
            'user_id' => Auth::id(),
            'body' => $validated['body'],
        ]);
    }
}
