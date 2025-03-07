<?php

namespace App\Actions\Books;

use App\Mail\BookCheckoutCustomerNotification;
use App\Mail\BookCheckoutLibrarianNotification;
use App\Models\Book;
use App\Models\User;
use App\Models\BookCheckout;
use App\Exceptions\BookNotAvailableException;
use App\Exceptions\CheckoutLimitExceededException;
use App\Repositories\Interfaces\BookCheckoutRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class CheckoutBookAction
{
    public function __construct(
        private readonly BookCheckoutRepositoryInterface $checkoutRepository
    ) {}

    public function execute(Book $book, User $user, ?Carbon $dueDate = null): void
    {
        if ($book->status !== 'available') {
            throw new BookNotAvailableException('This book is not available for checkout.');
        }

        $currentCheckouts = $this->checkoutRepository->getCurrentCheckoutsCount($user->id);
        if ($currentCheckouts >= config('library.max_checkouts', 5)) {
            throw new CheckoutLimitExceededException('You have reached the maximum number of checkouts allowed.');
        }

        // Create checkout record
        $checkout = $this->checkoutRepository->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'checkout_date' => now(),
            'due_date' => $dueDate ?? now()->addDays(5),
            'return_date' => null
        ]);

        // Update book status using the new method
        $book->markAsCheckedOut();

        // Send email to customer
        Mail::to($user->email)
            ->queue(new BookCheckoutCustomerNotification($checkout));

        // Send email to librarians
        $librarians = User::where('role', 'librarian')->get();
        foreach ($librarians as $librarian) {
            Mail::to($librarian->email)
                ->queue(new BookCheckoutLibrarianNotification($checkout));
        }
    }
}
