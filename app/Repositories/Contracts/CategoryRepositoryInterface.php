<?php

namespace App\Repositories\Contracts;

interface CategoryRepositoryInterface
{
    public function count(): int;
    public function getPopularCategories(int $limit = 5): array;
}