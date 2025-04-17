<?php
namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class AccountCreateUpdateRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'description'    => ['required', 'string', 'max:255'],
            'institution_id' => ['required', 'exists:institutions,id'],
            'color'          => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];

        if ($this->isMethod('POST')) {
            $rules['initial_balance'] = ['required', 'numeric', 'regex:/^-?\d+(\.\d{1,2})?$/'];
        }

        return $rules;
    }
}
