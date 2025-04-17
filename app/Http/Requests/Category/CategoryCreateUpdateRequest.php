<?php
namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryCreateUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch'); // Detecta se é uma atualização

        $rules = [
            'parent_id' => ['nullable', 'exists:categories,id'],
            'name'      => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'max:255',
                Rule::unique('categories')->where(function ($query) {
                    return $query->where('type', $this->request->get('type'))
                        ->where('user_id', Auth::id());
                })->ignore($this->category),
            ],
            'color'     => [
                'nullable',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'icon'      => [
                'nullable',
                'string',
            ],
            'type'      => [
                'nullable',
                Rule::in(['expense', 'income']),
            ],
            'is_system' => ['boolean'],
            'code'      => ['nullable', 'string', 'max:50'],
        ];

        if ($this->filled('parent_id')) {
            $rules['name'] = [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'];
            unset($rules['color'], $rules['icon'], $rules['type']);
        }

        if (! $this->filled('parent_id')) {
            $rules['color'] = ['required_without:parent_id', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'];
            $rules['icon']  = ['required_without:parent_id', 'string'];
            $rules['type']  = ['required_without:parent_id', Rule::in(['expense', 'income'])];
        }

        return $rules;
    }

}
