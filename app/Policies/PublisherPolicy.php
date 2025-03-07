<?php

namespace App\Policies;

use App\Models\Publisher;
use App\Models\User;

class PublisherPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Everyone can view the publishers list (it will be filtered in the repository)
    }

    public function view(User $user, Publisher $publisher): bool
    {
        if (!$user->isLibrarian()) {
            // Customers can view any publisher created by a librarian
            return $publisher->creator->isLibrarian();
        }
        
        // Librarians can only view their own publishers
        return $user->id === $publisher->created_by;
    }

    public function create(User $user): bool
    {
        return $user->isLibrarian();
    }

    public function update(User $user, Publisher $publisher): bool
    {
        return $user->isLibrarian() && $user->id === $publisher->created_by;
    }

    public function delete(User $user, Publisher $publisher): bool
    {
        return $user->isLibrarian() && $user->id === $publisher->created_by;
    }
}
