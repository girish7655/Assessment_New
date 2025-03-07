<?php

namespace App\Services;

use App\Models\Publisher;
use App\Repositories\Interfaces\PublisherRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class PublisherService
{
    public function __construct(
        private readonly PublisherRepositoryInterface $publisherRepository
    ) {}

    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return $this->publisherRepository->getAllPaginated($perPage);
    }

    public function getSelectList(): array
    {
        try {
            return $this->publisherRepository->getSelectList();
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve publisher list.');
        }
    }

    public function create(array $data): Publisher
    {
        try {
            return $this->publisherRepository->create($data);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception('Failed to create publisher. Please try again with a different name.');
        }
    }

    public function update(Publisher $publisher, array $data): Publisher
    {
        try {
            return $this->publisherRepository->update($publisher, $data);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception('Failed to update publisher. Please try again with a different name.');
        }
    }

    public function delete(Publisher $publisher): bool
    {
        return $this->publisherRepository->delete($publisher);
    }

    public function findById(int $id): ?Publisher
    {
        return $this->publisherRepository->findById($id);
    }
}
