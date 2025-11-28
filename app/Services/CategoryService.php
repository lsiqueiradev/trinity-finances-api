<?php
namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryService
{
    /**
     * Validate category ownership by checking if the current user owns the category.
     *
     * @param string $categoryId The ID of the category to validate.
     * @param Request $request The HTTP request object containing user information.
     * @return JsonResponse|Category Returns the category if validation is successful, otherwise throws an exception.
     * @throws HttpResponseException If the category is not found or the user does not own the category.
     */
    public function validateOwnership(string $categoryId, Request $request): JsonResponse | Category
    {
        try {
            $category = Category::findOrFail($categoryId);

            if ($category->user_id !== $request->user()->id) {
                throw new HttpResponseException(
                    response()->json([
                        'message' => __('You do not have permission to access this resource.'),
                    ], 403)
                );
            }
            return $category;
        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(
                response()->json([
                    'message' => __('You do not have permission to access this resource.'),
                ], 403)
            );
        }
    }

    /**
     * Creates a new category based on the given request.
     * If a parent category is specified, inherits its color, icon, and type.
     *
     * @param Request $request The HTTP request object containing category details.
     * @return Category The newly created category instance.
     */

    public function create(Request $request): Category
    {

        if ($request->parent_id) {
            $parentCategory = Category::find($request->parent_id);
            $request->merge([
                'color' => $parentCategory->color,
                'icon'  => $parentCategory->icon,
                'type'  => $parentCategory->type,
            ]);
        }

        return Category::create(array_merge($request->all(), [
            'user_id' => $request->user()->id,
        ]));
    }

    /**
     * Updates an existing category based on the given request.
     *
     * @param Request $request The HTTP request object containing category details.
     * @param string $categoryId The ID of the category to update.
     * @return bool True if the update was successful, false otherwise.
     */
    public function update(Request $request, string $categoryId): bool
    {
        return $this->validateOwnership($categoryId, $request)->update($request->all());
    }

    /**
     * Deletes an existing category based on the given request.
     *
     * @param Request $request The HTTP request object containing user information.
     * @param string $categoryId The ID of the category to delete.
     * @return bool True if the deletion was successful, false otherwise.
     * @throws HttpResponseException If the category is not archived, or if the category is not found, or if the user does not own the category.
     */
    public function delete(Request $request, string $categoryId): bool
    {
        $category = $this->validateOwnership($categoryId, $request);

        if (! $category->is_archived) {
            throw new HttpResponseException(
                response()->json([
                    'message' => __('To delete the category, it must be archived first.'),
                ], 403)
            );

        }

        return $category->delete();
    }

    /**
     * Retrieves all categories for the current user based on the given request.
     *
     * @param Request $request The HTTP request object containing user information and query parameters.
     * @return Collection A collection of category instances.
     */
    public function getAll(Request $request): Collection
    {

        $type     = (string) $request->input('type', 'expense');
        $archived = filter_var($request->input('archived', false), FILTER_VALIDATE_BOOLEAN);

        $categories = Category::where([
            'user_id'   => $request->user()->id,
            'is_system' => false,
            'type'      => $type,
            'parent_id' => null,
        ])
            ->select('id', 'parent_id', 'name', 'color', 'icon', 'type', 'is_system', 'is_archived', 'created_at', 'updated_at')
            ->with(['subcategories' => function ($query) use ($archived) {
                $query->select('id', 'parent_id', 'name', 'color', 'icon', 'type', 'is_system', 'is_archived', 'created_at', 'updated_at'
                )->where('is_archived', $archived)->orderBy('name');
            }])
            ->where(function ($query) use ($archived) {
                if ($archived) {
                    $query->where('is_archived', true)
                        ->orWhereHas('subcategories', function ($subQuery) {
                            $subQuery->where('is_archived', true);
                        });
                } else {
                    $query->where('is_archived', false);
                }
            })
            ->orderBy('name')
            ->get();

        return $categories;

    }

    /**
     * Retrieves a single category based on the given request.
     *
     * @param  Request  $request
     * @param  string  $categoryId
     * @return Category
     *
     * @throws HttpResponseException If the category is not found or the user does not own the category.
     */
    public function get(Request $request, $categoryId): Category
    {
        $category = $this->validateOwnership($categoryId, $request);
        $category->setVisible(['id', 'parent_id', 'name', 'color', 'icon', 'type', 'is_system', 'is_archived', 'created_at', 'updated_at']);

        return $category;
    }

    public function archiveOrUnarchive(Request $request, String $categoryId, String $type): JsonResponse | bool
    {
        if (! in_array($type, ['archive', 'unarchive'])) {
            return response()->json([
                'message' => __('An error occurred while archiving the category, incorrect parameters'),
            ], 422);
        }

        $status = $type === 'archive';

        $category = $this->validateOwnership($categoryId, $request);

        $categoriesParent = Category::where('parent_id', $category->id);
        if ($categoriesParent->exists()) {
            $categoriesParent->update(['is_archived' => $status]);
        }

        if (! $status && $category->parent()->exists() && $category->parent->is_archived) {
            $category->parent()->update(['is_archived' => $status]);
        }

        return $category->update(['is_archived' => $status]);
    }
}
