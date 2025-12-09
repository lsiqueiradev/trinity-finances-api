<?php
namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class AvatarPhotoStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'photo' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:512'],
        ];

        return $rules;
    }

}
