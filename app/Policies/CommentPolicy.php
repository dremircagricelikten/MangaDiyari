<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Determine whether the user can delete the comment.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user->hasAnyRole(UserRole::ADMIN, UserRole::MODERATOR) || $user->id === $comment->user_id;
    }

    public function react(User $user, Comment $comment): bool
    {
        return $user->exists;
    }
}
