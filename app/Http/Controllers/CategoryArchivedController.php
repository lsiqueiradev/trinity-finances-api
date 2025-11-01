<?php
namespace App\Http\Controllers;

use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryArchivedController extends Controller
{

    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $categoryId)
    {
        $categoryArchived = $this->categoryService->archiveOrUnarchive($request, $categoryId, 'archive');

        if (! $categoryArchived) {
            return response()->json([
                'message' => __('An error occurred while archiving the category'),
            ], 404);
        }

        return response()->json([
            'message' => __('Category archived successfully'),
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function destroy(Request $request, string $categoryId)
    {
        $categoryArchived = $this->categoryService->archiveOrUnarchive($request, $categoryId, 'unarchive');

        if (! $categoryArchived) {
            return response()->json([
                'message' => __('An error occurred while archiving the category'),
            ], 404);
        }

        return response()->json([
            'message' => __('Category unarchived successfully'),
        ]);

    }
}
