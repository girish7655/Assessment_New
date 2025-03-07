<?php

namespace App\Providers;

use App\Services\ReviewService;
use App\Repositories\Interfaces\ReviewRepositoryInterface;
use App\Repositories\Eloquent\ReviewRepository;
use Illuminate\Support\ServiceProvider;

class ReviewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ReviewRepositoryInterface::class, ReviewRepository::class);
        
        $this->app->bind(ReviewService::class, function ($app) {
            return new ReviewService(
                $app->make(ReviewRepositoryInterface::class)
            );
        });
    }
}