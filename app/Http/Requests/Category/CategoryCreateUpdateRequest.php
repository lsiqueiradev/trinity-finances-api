<?php
namespace App\Http\Requests\Category;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryCreateUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate            = $this->isMethod('put') || $this->isMethod('patch');
        $requiredOrSometimes = $isUpdate ? 'sometimes' : 'required';

        $categoryId    = $this->route('categoryId');
        $category      = Category::find($categoryId);
        $currentType   = $this->input('type', $category?->type);
        $currentParent = $this->filled('parent_id')
            ? $this->input('parent_id')
            : $category?->parent_id;

        $rules = [
            'parent_id' => ['nullable', 'exists:categories,id'],

            'name'      => [
                $requiredOrSometimes,
                'string',
                'max:255',
                Rule::unique('categories')->where(function ($query) use ($currentType, $currentParent) {
                    return $query
                        ->where('type', $currentType)
                        ->where('parent_id', $currentParent)
                        ->where('user_id', Auth::id());
                })->ignore($categoryId),
            ],

            'color'     => [
                $requiredOrSometimes,
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],

            'icon'      => [
                $requiredOrSometimes,
                'string',
            ],

            'type'      => [
                $requiredOrSometimes,
                Rule::in(['expense', 'income']),
            ],

            'is_system' => ['boolean'],
            'code'      => ['nullable', 'string', 'max:50'],
        ];

        if ($this->filled('parent_id')) {
            $rules['name'][0] = $isUpdate ? 'sometimes' : 'required';

            unset($rules['color'], $rules['icon'], $rules['type']);
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            if ($this->isMethod('put') || $this->isMethod('patch')) {
                $updatableFields = ['name', 'color', 'icon', 'type', 'parent_id'];

                if ($this->filled('parent_id')) {
                    $updatableFields = ['name', 'parent_id'];
                }

                $anyPresent = false;
                foreach ($updatableFields as $field) {
                    if ($this->has($field)) {
                        $anyPresent = true;
                        break;
                    }
                }

                if (! $anyPresent) {
                    $validator->errors()->add('payload', 'Envie ao menos um campo para atualização.');
                }
            }

            if ($validator->errors()->has('name')) {

                $validator->errors()->forget('name');

                $categoryId = $this->route('categoryId');
                $category   = Category::find($categoryId);

                $isSubcategory = ! is_null($category?->parent_id);
                $typeName      = $category?->type === 'income' ? 'receita' : 'despesa';

                $msg1 = $isSubcategory
                    ? 'Essa categoria possui uma subcategoria com o mesmo nome.'
                    : "Já existe um tipo de {$typeName} com esse nome.";

                $validator->errors()->add('name', $msg1);
            }

        });
    }
}
