<?php

namespace App\Services;

use App\Repositories\Interfaces\ReviewRepositoryInterface;
use App\Models\Book;
use App\Models\User;
use App\Exceptions\DuplicateReviewException;

class ReviewService
{
    public function __construct(
        private readonly ReviewRepositoryInterface $reviewRepository
    ) {}

    public function hasUserReviewedBook(string $bookId): bool
    {
        $review = $this->reviewRepository->getUserReviewForBook(
            auth()->id(),
            $bookId
        );

        return $review !== null;
    }

    public function createReview(array $data, Book $book, User $user): void
    {
        $existingReview = $this->reviewRepository->getUserReviewForBook(
            $user->id,
            $book->id
        );
        
        if ($existingReview) {
            throw new DuplicateReviewException();
        }

        $this->reviewRepository->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => $data['rating'],
            'message' => $data['message'],
        ]);
    }

    public function updateReview(string $reviewId, array $data): void
    {
        $this->reviewRepository->update($reviewId, [
            'rating' => $data['rating'],
            'message' => $data['message'],
        ]);
    }

    public function deleteReview(string $reviewId): void
    {
        $this->reviewRepository->delete($reviewId);
    }

    public function getBookReviews(string $bookId, int $perPage = 10)
    {
        return $this->reviewRepository->getBookReviews($bookId);
    }
}