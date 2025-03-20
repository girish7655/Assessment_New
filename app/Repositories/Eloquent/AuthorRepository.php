<?php

namespace App\Repositories\Eloquent;

use App\Models\Author;
use App\Repositories\Interfaces\AuthorRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class AuthorRepository implements AuthorRepositoryInterface
{
    private function validateUnique(array $data, ?int $excludeId = null): void
    {
        $query = Author::query()
            ->where('name', $data['name'])
            ->where('birth_date', $data['birth_date'] ?? null)
            ->where('nationality', $data['nationality'] ?? null)
            ->where('created_by', auth()->id())
            ->whereNull('deleted_at');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'name' => ['You already have an author with these details.']
            ]);
        }
    }

    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Author::query()
            ->when(auth()->user()->role === 'librarian', function ($query) {
                $query->where('created_by', auth()->id());
            })
            ->when(request('search'), function ($query) {
                $search = request('search');
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('nationality', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?Author
    {
        $query = Author::query();
        
        if (auth()->user()->role === 'librarian') {
            $query->where('created_by', auth()->id());
        }
        
        return $query->findOrFail($id);
    }

    public function create(array $data): Author
    {
        $this->validateUnique($data);
        
        $data['created_by'] = auth()->id();
        return Author::create($data);
    }

    public function update(Author $author, array $data): Author
    {
        if (auth()->user()->role === 'librarian' && $author->created_by !== auth()->id()) {
            throw new \Exception('Unauthorized action.');
        }

        $this->validateUnique($data, $author->id);
        
        $author->update($data);
        return $author->fresh();
    }

    public function delete(Author $author): bool
    {
        if (auth()->user()->role === 'librarian' && $author->created_by !== auth()->id()) {
            throw new \Exception('Unauthorized action.');
        }

        if ($author->books()->exists()) {
            throw new \Exception('Cannot delete author. It has associated books.');
        }

        return $author->delete();
    }
}
