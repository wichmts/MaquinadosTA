<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nombre' => ['required', 'string', 'min:3', 'max:255'],
            'ap_paterno' => ['required', 'string', 'min:3', 'max:255'],
            'ap_materno' => ['required', 'string', 'min:3', 'max:255'],
            'celular' => ['nullable', 'numeric', 'digits:10'],
            'email' => ['required', 'email', 'max:255', Rule::unique((new User)->getTable())->ignore(auth()->id())],
        ];
    }
}
