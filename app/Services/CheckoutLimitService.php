<?php

namespace App\Services;

use App\Repositories\Interfaces\BookCheckoutRepositoryInterface;

class CheckoutLimitService
{
    private const MAX_CHECKOUTS = 3;

    public function __construct(
        private readonly BookCheckoutRepositoryInterface $checkoutRepository
    ) {}

    public function canCheckoutMore(): bool
    {
        $currentCheckouts = $this->checkoutRepository
            ->getCurrentCheckoutsCount(auth()->id());

        return $currentCheckouts < self::MAX_CHECKOUTS;
    }
}