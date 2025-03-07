<?php

namespace App\Exceptions;

use Exception;

class DuplicateReviewException extends Exception
{
    protected $message = 'You have already submitted a review for this book.';
}