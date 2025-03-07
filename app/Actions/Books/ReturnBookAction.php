<?php

namespace App\Actions\Books;

use App\Mail\BookReturnCustomerNotification;
use App\Mail\BookReturnLibrarianNotification;
use App\Models\BookCheckout;
use App\Models\User;
use App\Repositories\Interfaces\BookCheckoutRepositoryInterface;
use Illuminate\Support\Facades\Mail;

class ReturnBookAction
{
    public function __construct(
        private readonly BookCheckoutRepositoryInterface $checkoutRepository
    ) {}

    public function execute(BookCheckout $checkout): void
    {
        // Mark the book as returned
        $this->checkoutRepository->markAsReturned($checkout);
        $checkout->book->markAsReturned();

        // Send email to customer
        Mail::to($checkout->user->email)
            ->queue(new BookReturnCustomerNotification($checkout));

        // Send email to librarians
        $librarians = User::where('role', 'librarian')->get();
        foreach ($librarians as $librarian) {
            Mail::to($librarian->email)
                ->queue(new BookReturnLibrarianNotification($checkout));
        }
    }
}
