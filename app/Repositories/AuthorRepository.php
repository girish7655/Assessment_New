<?php

namespace App\Repositories;

use App\Models\Author;
use App\Repositories\Interfaces\AuthorRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class AuthorRepository implements AuthorRepositoryInterface
{
    public function __construct(
        private Author $model
    ) {}

    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->when(auth()->user()->role === 'librarian', function ($query) {
                $query->where('created_by', auth()->id());
            })
            ->when(!auth()->user()->role === 'librarian', function ($query) {
                $query->whereHas('creator', function($q) {
                    $q->where('role', 'librarian');
                });
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
        $query = $this->model->newQuery();
        
        if (auth()->user()->role === 'librarian') {
            // Librarians can only see their own authors
            $query->where('created_by', auth()->id());
        }
        
        return $query->findOrFail($id);
    }

    public function create(array $data): Author
    {
        $data['created_by'] = auth()->id();
        return $this->model->create($data);
    }

    public function update(Author $author, array $data): Author
    {
        if (auth()->user()->role === 'librarian' && $author->created_by !== auth()->id()) {
            throw new \Exception('Unauthorized action.');
        }
        
        $author->update($data);
        return $author;
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
