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
    /**
     * Handle the checkout process for a book.
     *
     * This method uses the CheckoutBookAction to execute the checkout logic,
     * including validation and updating the book's status. It then redirects
     * the user to the book's detail page with a success message upon completion.
     * If an exception occurs during checkout, the user is redirected back with an error message.
     *
     * @param  \App\Models\Book  $book
     * @param  \App\Actions\Books\CheckoutBookAction  $checkoutAction
     * @return \Illuminate\Http\RedirectResponse
     */

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

    /**
     * Handle the return process for a book.
     *
     * This method uses the ReturnBookAction to execute the return logic,
     * including validation and updating the book's status. It then redirects
     * the user to the book's detail page with a success message upon completion.
     * If an exception occurs during return, the user is redirected back with an error message.
     *
     * @param  \App\Models\Book  $book
     * @param  \App\Actions\Books\ReturnBookAction  $returnAction
     * @return \Illuminate\Http\RedirectResponse
     */
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
