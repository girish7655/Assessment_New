<?php

namespace App\Mail;

use App\Models\BookCheckout;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookReturnLibrarianNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private BookCheckout $checkout
    ) {}

    public function build(): self
    {
        return $this->markdown('emails.returns.librarian-notification')
                    ->subject('Book Return Alert')
                    ->with([
                        'checkout' => $this->checkout,
                        'book' => $this->checkout->book,
                        'user' => $this->checkout->user,
                    ]);
    }
}