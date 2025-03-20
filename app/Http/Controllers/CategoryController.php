<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Response;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Inject the required dependencies
     *
     * @param  \App\Services\CategoryService  $categoryService
     * @return void
     */
    public function __construct(
        private readonly CategoryService $categoryService
    ) {
        $this->authorizeResource(Category::class, 'category');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(): Response
    {
        return Inertia::render('Categories/Index', [
            'categories' => $this->categoryService->getAllPaginated(),
            'filters' => request()->only(['search'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(): Response
    {
        return Inertia::render('Categories/Create', [
            'categories' => $this->categoryService->getSelectList()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * @throws ValidationException
     * @throws \Exception
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['created_by'] = auth()->id(); 
            
            $this->categoryService->create($data);

            return redirect()
                ->route('categories.index')
                ->with('success', 'Category created successfully.');
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create category. Please try again adding with a different name.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Inertia\Response
     */
    public function edit(Category $category): Response
    {
        return Inertia::render('Categories/Edit', [
            'category' => $category,
            'categories' => $this->categoryService->getSelectList($category->id)
        ]);
    }

    /**
     * Updates the specified resource in storage.
     *
     * Validates and updates the category with the provided data.
     * Redirects back to the categories index on success, or back with error messages on failure.
     *
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        try {
            $this->categoryService->update($category, $request->validated());

            return redirect()
                ->route('categories.index')
                ->with('success', 'Category updated successfully.');
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->errors());
        }catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update category. Please try again with a different name.');
        }
    }

    /**
     * Removes the specified resource from storage.
     *
     * Deletes the category with the given id.
     * Redirects back to the categories index on success, or back with error messages on failure.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
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
