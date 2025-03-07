<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return $this->categoryRepository->getAllPaginated($perPage);
    }

    public function create(array $data): Category
    {
        if (!isset($data['created_by'])) {
            $data['created_by'] = auth()->id();
        }
        return $this->categoryRepository->create($data);
    }

    public function update(Category $category, array $data): Category
    {
        return $this->categoryRepository->update($category, $data);
    }

    public function delete(Category $category): bool
    {
        return $this->categoryRepository->delete($category);
    }

    public function getSelectList(?int $excludeId = null): array
    {
        $categories = $this->categoryRepository->getSelectList();
        
        if ($excludeId) {
            return array_filter($categories, fn($category) => $category['id'] !== $excludeId);
        }

        return $categories;
    }
}
