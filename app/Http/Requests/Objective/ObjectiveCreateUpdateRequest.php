<?php
namespace App\Http\Requests\Objective;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ObjectiveCreateUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name'           => [
                'required',
                'string',
                'max:255',
            ],
            'description'    => [
                'sometimes',
                'string',
                'max:255',
            ],
            'color'          => [
                'nullable',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'icon'           => [
                'nullable',
                'string',
            ],
            'initial_amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'target_amount'  => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'date'           => ['required', 'date'],
            'status'         => ['nullable', Rule::in(['active', 'paused', 'finished'])],

        ];

        return $rules;
    }

}
