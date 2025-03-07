<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Book;

class BookPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }


    public function view(User $user, Book $book): bool
    {
        if ($user->role === 'librarian') {
            return $user->id === $book->created_by;
        }
        
        return $book->creator->isLibrarian();
    }

    public function create(User $user): bool
    {
        return $user->role === 'librarian';
    }

    public function update(User $user, Book $book): bool
    {
        return $user->role === 'librarian' && $user->id === $book->created_by;
    }

    public function delete(User $user, Book $book): bool
    {
        return $user->role === 'librarian' && $user->id === $book->created_by;
    }
}
