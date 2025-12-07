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
        $currentParent = $this->input('parent_id', $category?->parent_id);

        $rules = [
            'parent_id' => ['nullable', 'exists:categories,id'],

            'name'      => [
                $requiredOrSometimes,
                'string',
                'max:255',
                Rule::unique('categories')->where(function ($query) use ($currentType, $currentParent) {
                    return $query
                        ->where('type', $currentType
                        )
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
            $rules['name'] = [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'];
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

            // Se já existe erro de unique
            if ($validator->errors()->has('name')) {

                // Limpa a mensagem padrão
                $validator->errors()->forget('name');

                // Carrega a categoria original
                $categoryId = $this->route('categoryId');
                $category   = Category::find($categoryId);

                // Agora você tem type e parent_id mesmo que NÃO venham no request
                $isSubcategory = ! is_null($category?->parent_id);
                $typeName      = $category?->type === 'income' ? 'receita' : 'despesa';

                // Suas duas mensagens
                $msg1 = $isSubcategory
                    ? 'Essa categoria possui uma subcategoria com o mesmo nome.'
                    : "Já existe um tipo de {$typeName} com esse nome.";

                $validator->errors()->add('name', $msg1);
            }

//             if ($this->filled('name')) {
//                 $exists = Category::where('name', $this->input('name'))
//                     ->where('user_id', Auth::id())
//                     ->where('parent_id', $this->input('parent_id'))
//                     ->when($this->filled('type'), fn($q) => $q->where('type', $this->input('type')))
//                     ->where(function ($q) {
//                         if ($this->route('category') ?? $this->category) {
//                             $q->where('id', '!=', $this->route('category') ?? $this->category);
//                         }
//                     })
//                     ->exists();
//
//                 if ($exists) {
//                     $category      = Category::find($this->categoryId);
//                     $isSubcategory = $this->filled('parent_id');
//                     $typeName      = $category->type === 'income' ? 'receita' : 'despesa';
//
//                     $message = $isSubcategory
//                         ? 'Essa categoria possui uma subcategoria com o mesmo nome.'
//                         : "Já existe um tipo de {$typeName} com esse nome.";
//
//                     $validator->errors()->add('name', $message);
//                 }
//
//             }
        });
    }
}
