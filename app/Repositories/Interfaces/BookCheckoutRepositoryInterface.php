<?php

namespace App\Repositories\Interfaces;

use App\Models\BookCheckout;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BookCheckoutRepositoryInterface
{
    public function getCurrentUserCheckouts(): LengthAwarePaginator;
    public function getOverdueCheckouts(): Collection;
    public function findById(string $id): ?BookCheckout;
    public function create(array $data): BookCheckout;
    public function getCurrentCheckoutsCount(string $userId): int;
    public function markAsReturned(BookCheckout $checkout): bool;
}