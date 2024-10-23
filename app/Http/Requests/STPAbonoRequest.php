<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class STPAbonoRequest extends FormRequest
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
            'id' => 'required|numeric|digits_between:1,10',
            'fechaOperacion' => 'required|date_format:Ymd',
            'institucionOrdenante' => 'required|numeric|digits_between:1,5',
            'institucionBeneficiaria' => 'required|numeric|digits_between:1,5',
            'claveRastreo' => 'required|string|max:30',
            'monto' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'nombreOrdenante' => 'nullable|string|max:120',
            'tipoCuentaOrdenante' => 'nullable|numeric|digits_between:1,2',
            'cuentaOrdenante' => 'nullable|string|max:20',
            'rfcCurpOrdenante' => 'nullable|string|max:18',
            'nombreBeneficiario' => 'required|string|max:40',
            'tipoCuentaBeneficiario' => 'required|numeric|digits_between:1,2',
            'cuentaBeneficiario' => 'required|string|max:20',
            'nombreBeneficiario2' => 'nullable|string|max:40',
            'tipoCuentaBeneficiario2' => 'nullable|numeric|digits_between:1,2',
            'cuentaBeneficiario2' => 'nullable|string|max:18',
            'rfcCurpBeneficiario' => 'required|string|max:18',
            'conceptoPago' => 'required|string|max:40',
            'referenciaNumerica' => 'required|numeric|digits_between:1,7',
            'empresa' => 'required|string|max:15',
            'tipoPago' => 'required|numeric|in:1,5,19,20,21,22',
            'tsLiquidacion' => 'required|string|max:14',
            'folioCodi' => 'nullable|string|max:20',
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
