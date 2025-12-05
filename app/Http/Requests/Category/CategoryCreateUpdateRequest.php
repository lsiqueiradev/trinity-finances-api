<?php
// app/Http/Requests/Category/CategoryCreateUpdateRequest.php

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

        // Descobre type efetivo: se tiver parent_id, herdamos do pai; senão, usamos o input
        $effectiveType = $this->input('type');
        if ($this->filled('parent_id')) {
            $parent = Category::find($this->input('parent_id'));
            // Se parent não existir, o exists já trata; aqui só pega o type se houver
            if ($parent) {
                $effectiveType = $parent->type;
            }
        }

        $uniqueNameRule = Rule::unique('categories', 'name')
            ->where(function ($query) use ($effectiveType) {
                return $query
                    ->where('type', $effectiveType)
                    ->where('user_id', Auth::id());
            })
            ->ignore($this->route('category') ?? $this->category);

        $rules = [
            'parent_id' => ['nullable', 'exists:categories,id'],

            'name'      => [
                $requiredOrSometimes,
                'string',
                'max:255',
                $uniqueNameRule,
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

        // Quando há parent_id, você quer herdar color, icon, type e não exigir esses campos no payload
        if ($this->filled('parent_id')) {
            // Mantemos a validação de name e a UNIQUE usando o type do pai (effectiveType)
            $rules['name'] = [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255', $uniqueNameRule];

            // Não exigimos color/icon/type no corpo pois serão herdados
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

    public function messages(): array
    {
        return [
            'name.unique'      => 'Já existe uma categoria com este nome para este tipo.',
            'name.required'    => 'Informe o nome da categoria.',
            'name.max'         => 'O nome da categoria deve ter no máximo 255 caracteres.',
            'color.required'   => 'Informe a cor.',
            'color.regex'      => 'A cor deve estar no formato hexadecimal, ex: #AABBCC.',
            'icon.required'    => 'Informe o ícone.',
            'type.required'    => 'Informe o tipo.',
            'type.in'          => 'O tipo deve ser "expense" ou "income".',
            'parent_id.exists' => 'Categoria pai inválida.',
        ];
    }
}
