<?php

namespace App\Repositories\Contracts;

interface BookRepositoryInterface
{
    public function count(): int;
    public function countByUser(int $userId): int;
}