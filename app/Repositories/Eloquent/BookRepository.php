<?php

namespace App\Repositories\Eloquent;

use App\Models\Book;
use App\Repositories\Interfaces\BookRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class BookRepository implements BookRepositoryInterface
{
    public function __construct(private readonly Book $model) {}

    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->with(['creator', 'category', 'publisher', 'author'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->when(auth()->user()->role === 'librarian', function ($query) {
                // Librarians only see their own books
                $query->where('created_by', auth()->id());
            }, function ($query) {
                // Non-librarians see all books created by librarians
                $query->whereHas('creator', function($q) {
                    $q->where('role', 'librarian');
                });
            })
            ->when(request('search'), function ($query, $search) {
                $query->where(function($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                          ->orWhereHas('author', function($query) use ($search) {
                              $query->where('name', 'like', "%{$search}%");
                          })
                          ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->when(request('rating'), function ($query, $rating) {
                $query->having('reviews_avg_rating', '>=', $rating);
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('sortBy'), function ($query, $sortBy) {
                switch ($sortBy) {
                    case 'rating':
                        $query->orderByDesc('reviews_avg_rating');
                        break;
                    case 'created_at':
                        $query->orderBy('created_at', 'desc');
                        break;
                    default:
                        $query->orderBy('title');
                        break;
                }
            }, function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->paginate($perPage)
            ->appends(request()->query());
    }

    public function findById(int $id): ?Book
    {
        $book = $this->model->find($id);
        
        // If user is librarian and book exists, check if they own it
        if ($book && auth()->user()->role === 'librarian' && $book->created_by !== auth()->id()) {
            return null;
        }

        return $book;
    }

    public function create(array $data): Book
    {
        Book::validateUnique($data['title'], $data['author_id']);
        
        $data['created_by'] = auth()->id();
        return Book::create($data);
    }

    public function update(Book $book, array $data): Book
    {
        if (auth()->user()->role === 'librarian' && $book->created_by !== auth()->id()) {
            throw new \Exception('Unauthorized action.');
        }

        if (isset($data['title']) || isset($data['author_id'])) {
            Book::validateUnique(
                $data['title'] ?? $book->title,
                $data['author_id'] ?? $book->author_id,
                $book->id
            );
        }

        $book->update($data);
        return $book->fresh();
    }

    public function delete(Book $book): bool
    {
        if (auth()->user()->role === 'librarian' && $book->created_by !== auth()->id()) {
            throw new \Exception('Unauthorized action.');
        }

        return $book->forceDelete();
    }

    public function count(): int
    {
        return $this->model
            ->when(auth()->user()->role === 'librarian', function ($query) {
                $query->where('created_by', auth()->id());
            })
            ->count();
    }

    public function countByUser(int $userId): int
    {
        return $this->model->where('created_by', $userId)->count();
    }
}
