<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private User $user
    ) {}

    public function build(): self
    {
        return $this->markdown('emails.welcome')
                    ->subject('Welcome to the Library')
                    ->with([
                        'user' => $this->user
                    ]);
    }
}