<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use App\Models\Author;
use App\Models\Publisher;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Displays the dashboard for the current user.
     * 
     * If the user is a librarian, it also includes their personal statistics.
     * 
     * @return Response
     */
    public function index(): Response
    {
        $user = auth()->user();
        
        $stats = [
            'total_books' => Book::count(),
            'total_categories' => Category::count(),
            'total_users' => User::count(),
        ];

        if ($user->role === 'librarian') {
            $stats['my_books'] = Book::where('created_by', $user->id)->count();
            $stats['my_categories'] = Category::where('created_by', $user->id)->count();
            $stats['my_authors'] = Author::where('created_by', $user->id)->count();
            $stats['my_publishers'] = Publisher::where('created_by', $user->id)->count();
            $stats['popular_categories'] = $this->getPopularCategories();
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
        ]);
    }

    /**
     * Retrieves the top 5 most popular categories based on the number of books.
     *
     * @return array An array of categories with their respective book counts.
     */

    /**
     * Retrieves the top 5 most popular categories based on the number of books.
     * 
     * @return array An array of categories with their respective book counts.
     */
    private function getPopularCategories(): array
    {
        return Category::withCount('books')
            ->orderBy('books_count', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }
}
