<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reviews\StoreReviewRequest;
use App\Http\Requests\Reviews\UpdateReviewRequest;
use App\Services\ReviewService;
use App\Repositories\Interfaces\ReviewRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use App\Exceptions\DuplicateReviewException;

class ReviewController extends Controller
{
    public function __construct(
        private readonly ReviewService $reviewService,
        private readonly ReviewRepositoryInterface $reviewRepository
    ) {}

    public function index(string $bookId): Response
    {
        return Inertia::render('Reviews/Index', [
            'book' => $this->reviewRepository->getBookDetails($bookId),
            'reviews' => $this->reviewService->getBookReviews($bookId),
        ]);
    }

    public function store(
        string $bookId,
        StoreReviewRequest $request
    ): RedirectResponse {
        try {
            $this->reviewService->createReview(
                $request->validated(),
                $this->reviewRepository->getBookDetails($bookId),
                auth()->user()
            );

            return back()->with('success', 'Review added successfully.');
        } catch (DuplicateReviewException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(
        string $bookId,
        string $id,
        UpdateReviewRequest $request
    ): RedirectResponse {
        $review = $this->reviewRepository->findById($id);
        $this->authorize('update', $review);

        $this->reviewService->updateReview($id, $request->validated());

        return back()->with('success', 'Review updated successfully.');
    }

    public function destroy(string $bookId, string $id): RedirectResponse
    {
        $review = $this->reviewRepository->findById($id);
        $this->authorize('delete', $review);

        $this->reviewService->deleteReview($id);

        return back()->with('success', 'Review deleted successfully.');
    }
}
