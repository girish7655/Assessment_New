<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class CategoryRepository implements CategoryRepositoryInterface
{
    protected function scopeForCurrentUser($query)
    {
        if (auth()->user()->role === 'librarian') {
            // Librarians can only see their own categories
            return $query->where('created_by', auth()->id());
        }
        
        // Customers can see all categories created by librarians
        return $query->whereHas('creator', function($query) {
            $query->where('role', 'librarian');
        });
    }

    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return $this->scopeForCurrentUser(Category::query())
            ->when(request('search'), function ($query, $search) {
                $query->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->with('creator')
            ->latest()
            ->paginate($perPage);
    }

    public function getSelectList(): array
    {
        return $this->scopeForCurrentUser(Category::query())
            ->select('id', 'name')
            ->get()
            ->toArray();
    }

    public function findById(int $id): ?Category
    {
        return $this->scopeForCurrentUser(Category::query())
            ->findOrFail($id);
    }

    public function create(array $data): Category
    {
        $this->validateUniqueName($data['name']);
        
        $data['created_by'] = auth()->id();
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        if (auth()->user()->role === 'librarian' && $category->created_by !== auth()->id()) {
            throw new \Exception('Unauthorized action.');
        }

        if (isset($data['name']) && $data['name'] !== $category->name) {
            $this->validateUniqueName($data['name'], $category->id);
        }

        $category->update($data);
        return $category->fresh();
    }

    public function delete(Category $category): bool
    {
        if (auth()->user()->role === 'librarian' && $category->created_by !== auth()->id()) {
            throw new \Exception('Unauthorized action.');
        }

        if ($category->books()->exists()) {
            throw new \Exception('Cannot delete category. It has associated books.');
        }

        return $category->forceDelete();
    }

    private function validateUniqueName(string $name, ?int $excludeId = null): void
    {
        $query = Category::query()
            ->where('name', $name)
            ->where('created_by', auth()->id())  // Only check current librarian's categories
            ->whereNull('deleted_at');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'name' => ['You already have a category with this name.']
            ]);
        }
    }
}
