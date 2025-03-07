<?php

namespace App\Contracts\Stats;

use App\Repositories\Contracts\BookRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

interface StatsCalculator
{
    public function calculateTotalBooks(BookRepositoryInterface $repository): int;
    public function calculateTotalCategories(CategoryRepositoryInterface $repository): int;
    public function calculateTotalUsers(UserRepositoryInterface $repository): int;
    public function calculateUserBooks(BookRepositoryInterface $repository, int $userId): int;
    public function calculatePopularCategories(CategoryRepositoryInterface $repository): array;
    public function calculateRecentActivity(int $userId): array;
}