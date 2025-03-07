<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Category $category): bool
    {
        if (!$user->isLibrarian()) {
            // Customers can view any category created by a librarian
            return $category->creator->isLibrarian();
        }
        
        // Librarians can only view their own categories
        return $user->id === $category->created_by;
    }

    public function create(User $user): bool
    {
        return $user->isLibrarian();
    }

    public function update(User $user, Category $category): bool
    {
        return $user->isLibrarian() && $user->id === $category->created_by;
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->isLibrarian() && $user->id === $category->created_by;
    }
}
