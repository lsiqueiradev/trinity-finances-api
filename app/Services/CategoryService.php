<?php
namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as BaseCollection;

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
        $category = $this->validateOwnership($categoryId, $request);

        if ($request->parent_id) {
            $parentCategory = Category::find($request->parent_id);
            $request->merge([
                'color' => $parentCategory->color,
                'icon'  => $parentCategory->icon,
            ]);
        }

        return $category->update($request->all());
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

    public function getAll(Request $request): \Illuminate\Support\Collection
    {
        $type = (string) $request->input('type', 'expense');

        $archivedInput = $request->input('archived', null);
        $archived      = null;
        if ($archivedInput !== null && $archivedInput !== '') {
            $archived = filter_var($archivedInput, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        $selectFields = [
            'id', 'parent_id', 'name', 'color', 'icon', 'type',
            'is_system', 'is_archived', 'created_at', 'updated_at',
        ];
        $selectFieldsParent = [
            'id', 'name', 'color', 'icon',
        ];

        $baseQuery = Category::where([
            'user_id'   => $request->user()->id,
            'is_system' => false,
            'type'      => $type,
            'parent_id' => null,
        ])->select($selectFields);

        $withSubcategories = function ($query, ?bool $archivedFilter) use ($selectFields, $selectFieldsParent
        ) {
            $query->select($selectFields)->with(['parent' => function ($q) use ($selectFieldsParent) {
                $q->select($selectFieldsParent);
            }])->orderBy('name');
            if ($archivedFilter !== null) {
                $query->where('is_archived', $archivedFilter);
            }
        };

        $activeCategories = (clone $baseQuery)
            ->with(['subcategories' => function ($query) use ($withSubcategories) {
                $withSubcategories($query, false);
            }])
            ->where('is_archived', false)
            ->orderBy('name')
            ->get();

        $archivedBlockCategories = (clone $baseQuery)
            ->with(['subcategories' => function ($query) use ($withSubcategories) {
                $withSubcategories($query, true);
            }])
            ->where(function ($query) {
                $query->where('is_archived', true)
                    ->orWhereHas('subcategories', function ($subQuery) {
                        $subQuery->where('is_archived', true);
                    });
            })
            ->orderBy('name')
            ->get();

        /**
         * Se archived FOI enviado: retorna só o bloco correspondente (flat)
         */
        if ($archived !== null) {

            $final = collect();

            if ($archived === false) {
                foreach ($activeCategories as $cat) {

                    if ($cat instanceof Model) {

                        $cat->makeHidden(['subcategories']);
                    }
                    $final = $final->merge($this->flattenCategory($cat));
                }
                return $final->values();
            }

            foreach ($archivedBlockCategories as $cat) {

                if ($cat->is_archived) {

                    if ($cat instanceof Model) {

                        $cat->makeHidden(['subcategories']);
                    }
                    $final = $final->merge($this->flattenCategory($cat));
                    continue;
                }

                $virtual = [
                    'id' => "archived-parent-{$cat->id}",
                    'synthetic_id' => true,
                    'name'         => $cat->name,
                    'icon'         => $cat->icon,
                    'color'        => $cat->color,
                    'is_archived'  => true,
                    'is_system'    => $cat->is_system,
                    'type'         => $cat->type,
                    'parent_id'    => null,
                ];

                $final = $final->merge($this->flattenCategory($virtual));
            }

            return $final->values();
        }

        /**
         * archived NÃO enviado → lista completa:
         * - ativas (flat)
         * - título Arquivadas (se houver)
         * - bloco arquivado (flat)
         */
        $result = collect();

        foreach ($activeCategories as $cat) {
            if ($cat instanceof Model) {
                $cat->makeHidden(['subcategories']);
            }
            $result = $result->merge($this->flattenCategory($cat));
        }

        if ($archivedBlockCategories->isNotEmpty()) {
            $result->push(['title' => 'Arquivadas']);

            foreach ($archivedBlockCategories as $cat) {
                if ($cat->is_archived) {
                    if ($cat instanceof Model) {
                        $cat->makeHidden(['subcategories']);
                    }
                    $result = $result->merge($this->flattenCategory($cat));
                    continue;
                }

                $virtual = [
                    'id' => "archived-parent-{$cat->id}",
                    'synthetic_id' => true,
                    'name'         => $cat->name,
                    'icon'         => $cat->icon,
                    'color'        => $cat->color,
                    'is_archived'  => true,
                    'is_system'    => $cat->is_system,
                    'type'         => $cat->type,
                ];

                $result = $result->merge($this->flattenCategory($virtual));
            }
        }

        return $result->values();
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

        $category->load('parent');

        $category->setVisible(['id', 'name', 'color', 'icon', 'type', 'is_system', 'is_archived', 'parent', 'created_at', 'updated_at']);

        if ($category->parent) {
            $category->parent->setVisible([
                'id', 'name', 'color', 'icon',
            ]);
        }

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

    private function flattenCategory($cat): BaseCollection
    {
        $flat = collect();

        if ($cat instanceof Model) {
            $parentArr = collect($cat->toArray())->except('subcategories');
            $subs      = $cat->relationLoaded('subcategories') ? $cat->subcategories->toArray() : [];
        } else {
            $parentArr = collect($cat)->except('subcategories');
            $subs      = isset($cat['subcategories']) ? $cat['subcategories'] : [];
        }

        $flat->push($parentArr->all());

        if (! empty($subs)) {
            foreach ($subs as $sub) {

                if ($sub instanceof Model) {
                    $flat->push(collect($sub->toArray())->except('subcategories')->all());
                } else {
                    $flat->push(collect($sub)->except('subcategories')->all());
                }
            }
        }

        return $flat;
    }

}
