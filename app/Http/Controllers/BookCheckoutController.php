<?php

namespace App\Http\Controllers;

use App\Actions\Books\CheckoutBookAction;
use App\Actions\Books\ReturnBookAction;
use App\Models\Book;
use App\Models\BookCheckout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class BookCheckoutController extends Controller
{
    public function checkout(
        Book $book,
        CheckoutBookAction $checkoutAction
    ): RedirectResponse {
        try {
            $checkoutAction->execute($book, Auth::user());
            
            return redirect()
                ->route('books.show', $book)
                ->with('success', 'Book checked out successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function return(
        Book $book,
        ReturnBookAction $returnAction
    ): RedirectResponse {
        try {
            // Get the active checkout
            $checkout = $book->checkouts()
                ->whereNull('return_date')
                ->latest()
                ->first();

            if (!$checkout) {
                return back()->with('error', 'No active checkout found for this book.');
            }

            $returnAction->execute($checkout);
            
            return redirect()
                ->route('books.show', $book)
                ->with('success', 'Book returned successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
