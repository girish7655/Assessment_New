<?php

namespace App\Providers;

use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\Review;
use App\Policies\BookPolicy;
use App\Policies\AuthorPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\PublisherPolicy;
use App\Policies\ReviewPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Book::class => BookPolicy::class,
        Author::class => AuthorPolicy::class,
        Category::class => CategoryPolicy::class,
        Publisher::class => PublisherPolicy::class,
        Review::class => ReviewPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
