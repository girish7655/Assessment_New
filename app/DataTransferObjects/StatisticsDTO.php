<?php

namespace App\DataTransferObjects;

class StatisticsDTO
{
    public function __construct(
        public readonly int $totalBooks,
        public readonly int $totalCategories,
        public readonly int $totalUsers,
        public readonly int $myBooks,
        public readonly array $popularCategories,
        public readonly array $recentActivity
    ) {}

    /**
     * Convert DTO to array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'total_books' => $this->totalBooks,
            'total_categories' => $this->totalCategories,
            'total_users' => $this->totalUsers,
            'my_books' => $this->myBooks,
            'popular_categories' => $this->popularCategories,
            'recent_activity' => $this->recentActivity,
        ];
    }
}