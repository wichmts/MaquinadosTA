<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class STPDispersionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
         return [
            'id' => 'required|numeric|digits_between:1,12',
            'empresa' => 'required|string|max:15',
            'folioOrigen' => 'nullable|string|max:50',
            'estado' => 'required|string|max:20',
            'causaDevolucion' => 'nullable|string|max:100',
            'tsLiquidacion' => 'required_if:estado,LQ|required_if:estado,D|string|max:14',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'mensaje' => 'error',
            'error' => $validator->errors()
        ], 422));
    }
}