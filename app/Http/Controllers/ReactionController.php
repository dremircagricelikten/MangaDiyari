<?php

namespace App\Http\Controllers;

use App\Enums\ReactionType;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReactionController extends Controller
{
    public function store(Request $request, Comment $comment): RedirectResponse
    {
        $this->authorize('react', $comment);

        $data = $request->validate([
            'type' => ['required', Rule::enum(ReactionType::class)],
        ]);

        $user = $request->user();
        $reactionType = ReactionType::from($data['type']);

        $comment->reactions()
            ->where('user_id', $user->id)
            ->where('type', '!=', $reactionType->value)
            ->delete();

        $existing = $comment->reactions()
            ->where('user_id', $user->id)
            ->where('type', $reactionType->value)
            ->first();

        if ($existing) {
            $existing->delete();

            return back()->with('status', 'Tepkin kaldırıldı.');
        }

        $comment->reactions()->create([
            'user_id' => $user->id,
            'type' => $reactionType->value,
        ]);

        return back()->with('status', 'Tepkin kaydedildi.');
    }
}
