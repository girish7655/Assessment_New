<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {
        $this->authorizeResource(Category::class, 'category');
    }

    public function index(): Response
    {
        return Inertia::render('Categories/Index', [
            'categories' => $this->categoryService->getAllPaginated(),
            'filters' => request()->only(['search'])
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Categories/Create', [
            'categories' => $this->categoryService->getSelectList()
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['created_by'] = auth()->id(); // Explicitly set created_by
            
            $this->categoryService->create($data);

            return redirect()
                ->route('categories.index')
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create category. Please try again adding with a different name.');
        }
    }

    public function edit(Category $category): Response
    {
        return Inertia::render('Categories/Edit', [
            'category' => $category,
            'categories' => $this->categoryService->getSelectList($category->id)
        ]);
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        try {
            $this->categoryService->update($category, $request->validated());

            return redirect()
                ->route('categories.index')
                ->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update category. Please try again with a different name.');
        }
    }

    public function destroy(Category $category): RedirectResponse
    {
        try {
            $this->categoryService->delete($category);
            
            return redirect()
                ->route('categories.index')
                ->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete category. Please try again.');
        }
    }
}
