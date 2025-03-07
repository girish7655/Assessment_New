<?php

namespace App\Repositories\Eloquent;

use App\Models\Review;
use App\Models\Book;
use App\Repositories\Interfaces\ReviewRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function __construct(
        private readonly Review $model,
        private readonly Book $bookModel
    ) {}

    public function getBookReviews(string $bookId): LengthAwarePaginator
    {
        return $this->model
            ->with('user')
            ->where('book_id', $bookId)
            ->latest()
            ->paginate(10);
    }

    public function getBookDetails(string $bookId): Book
    {
        return $this->bookModel
            ->with(['author', 'category'])
            ->findOrFail($bookId);
    }

    public function findById(string $id): ?Review
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Review
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): bool
    {
        return $this->findById($id)->update($data);
    }

    public function delete(string $id): bool
    {
        return $this->findById($id)->delete();
    }

    public function getUserReviewForBook(string $userId, string $bookId): ?Review
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('book_id', $bookId)
            ->first();
    }
}