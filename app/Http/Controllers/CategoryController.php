<?php
namespace App\Http\Controllers;

use App\Http\Requests\Category\CategoryCreateUpdateRequest;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $categories = $this->categoryService->getAll($request);

        return response()->json($categories);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryCreateUpdateRequest $request)
    {
        $this->categoryService->create($request);

        return response()->json([
            'message' => __('Category created successfully'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $categoryId)
    {
        $category = $this->categoryService->get($request, $categoryId);
        return response()->json($category);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryCreateUpdateRequest $request, string $categoryId)
    {
        $categoryUpdated = $this->categoryService->update($request, $categoryId);

        if (! $categoryUpdated) {
            return response()->json([
                'message' => __('An error occurred while updating the category'),
            ], 403);
        }

        return response()->json([], 204);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $categoryId)
    {
        $categoryDeleted = $this->categoryService->delete($request, $categoryId);

        if (! $categoryDeleted) {
            return response()->json([
                'message' => __('An error occurred while deleting the category'),
            ], 403);
        }

        return response()->json([
            'message' => __('Category deleted successfully'),
        ]);

    }
}
