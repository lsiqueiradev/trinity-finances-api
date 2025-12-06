<?php
namespace App\Http\Requests\Category;

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

        $rules = [
            'parent_id' => ['nullable', 'exists:categories,id'],

            'name'      => [
                $requiredOrSometimes,
                'string',
                'max:255',
                Rule::unique('categories')->where(function ($query) {
                    return $query
                        ->where('type', $this->input('type'))
                        ->where('parent_id', $this->input('parent_id'))
                        ->where('user_id', Auth::id());
                })->ignore($this->route('category') ?? $this->category),
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
        });
    }
}
