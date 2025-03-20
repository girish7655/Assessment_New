<?php

namespace App\Policies;

use App\Models\Author;
use App\Models\User;

class AuthorPolicy
{
    public function viewAny(User $user): bool
    {
        // Allow customers to view all authors, but librarians only see their own
        return !$user->isLibrarian() || $user->isLibrarian();
    }

    public function view(User $user, Author $author): bool
    {
        if (!$user->isLibrarian()) {
            return true;
        }

        return $user->id === $author->created_by;
    }

    public function create(User $user): bool
    {
        return $user->isLibrarian();
    }

    public function update(User $user, Author $author): bool
    {
        // Only the librarian who created the author can update it
        return $user->isLibrarian() && $user->id === $author->created_by;
    }

    public function delete(User $user, Author $author): bool
    {
        // Only the librarian who created the author can delete it
        return $user->isLibrarian() && $user->id === $author->created_by;
    }
}
