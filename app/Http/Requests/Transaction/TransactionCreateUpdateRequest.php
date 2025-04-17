<?php
namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionCreateUpdateRequest extends FormRequest
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
            // 'card_id' => 'nullable|exists:cards,id',
            // 'account_id' => 'required|exists:accounts,id',
            'account_id'         => [
                'required',
                Rule::exists('accounts', 'id')->where(function ($query) {
                    $query->where('is_archived', false);
                }),
            ],
            'category_id'        => 'required|exists:categories,id',
            'is_confirmed'       => 'boolean',
            'amount'             => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'description'        => 'required|string',
            'observations'       => 'nullable|string',
            'type'               => 'required_if:card_id,null|in:income,expense',
            'date'               => 'required|date',
            'is_recurring'       => 'boolean',
            'is_installment'     => 'boolean',
            'total_installments' => 'nullable|integer|min:2|max:99',
            'frequency'          => 'nullable|in:weekly,monthly,yearly',
        ];

        if ($this->isMethod('POST')) {
            // $rules['category_id'] = ['required_if:card_id,null,type,expense|prohibited_if:type,income|exists:categories,id'];
            $rules['frequency']          = 'required_if:is_recurring,true|required_if:is_installment,true|nullable|in:weekly,monthly,yearly';
            $rules['total_installments'] = 'required_if:is_installment,true|nullable|integer|min:2|max:99';
        }

        return $rules;

    }
}
