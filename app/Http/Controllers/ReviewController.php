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
    /**
     * Construct a new ReviewController instance.
     *
     * @param  ReviewService  $reviewService
     * @param  ReviewRepositoryInterface  $reviewRepository
     * @return void
     */
    public function __construct(
        private readonly ReviewService $reviewService,
        private readonly ReviewRepositoryInterface $reviewRepository
    ) {}

    /**
     * Show the list of reviews for a given book.
     *
     * @param  string  $bookId
     * @return \Inertia\Response
     */
    public function index(string $bookId): Response
    {
        return Inertia::render('Reviews/Index', [
            'book' => $this->reviewRepository->getBookDetails($bookId),
            'reviews' => $this->reviewService->getBookReviews($bookId),
        ]);
    }

    /**
     * Store a new review for a given book.
     *
     * @param  string  $bookId
     * @param  StoreReviewRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \App\Exceptions\DuplicateReviewException
     */
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

    /**
     * Update an existing review for a given book.
     *
     * @param  string  $bookId  The ID of the book associated with the review.
     * @param  string  $id  The ID of the review to be updated.
     * @param  UpdateReviewRequest  $request  The request containing the new review data.
     * @return \Illuminate\Http\RedirectResponse  A redirect response indicating the result of the operation.
     * 
     * @throws \Illuminate\Auth\Access\AuthorizationException  If the user is not authorized to update the review.
     */

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

    /**
     * Remove the specified review for a given book.
     *
     * @param  string  $bookId  The ID of the book associated with the review.
     * @param  string  $id  The ID of the review to be deleted.
     * @return \Illuminate\Http\RedirectResponse  A redirect response indicating the result of the operation.
     * 
     * @throws \Illuminate\Auth\Access\AuthorizationException  If the user is not authorized to delete the review.
     */

    public function destroy(string $bookId, string $id): RedirectResponse
    {
        $review = $this->reviewRepository->findById($id);
        $this->authorize('delete', $review);

        $this->reviewService->deleteReview($id);

        return back()->with('success', 'Review deleted successfully.');
    }
}
