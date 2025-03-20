<?php

namespace App\Providers;

use App\Repositories\Interfaces\BookRepositoryInterface;
use App\Repositories\Interfaces\AuthorRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\PublisherRepositoryInterface;
use App\Repositories\Interfaces\BookCheckoutRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\ReviewRepositoryInterface;
use App\Repositories\Eloquent\RoleRepository;
use App\Repositories\Eloquent\AuthorRepository;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\PublisherRepository;
use App\Repositories\Eloquent\BookCheckoutRepository;
use App\Repositories\Eloquent\BookRepository;
use App\Repositories\Eloquent\ReviewRepository;
use App\Services\ReviewService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BookRepositoryInterface::class, BookRepository::class);
        $this->app->bind(AuthorRepositoryInterface::class, AuthorRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(PublisherRepositoryInterface::class, PublisherRepository::class);
        $this->app->bind(BookCheckoutRepositoryInterface::class, BookCheckoutRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);

        $this->app->bind(ReviewRepositoryInterface::class, ReviewRepository::class);
        
        $this->app->bind(ReviewService::class, function ($app) {
            return new ReviewService(
                $app->make(ReviewRepositoryInterface::class)
            );
        });
    }
}
