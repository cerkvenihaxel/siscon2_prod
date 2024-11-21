<?php

namespace App\Http\Requests\SeguimientoMedicamento;

use Illuminate\Foundation\Http\FormRequest;

class SeguimientoMedicamentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return TRUE;
    }

    public function rules()
    {
        return [
            'nro_solicitud' => 'required|string',
        ];
    }

    public function keyList(){
        return [
            'nro_solicitud',
        ];
    }
}
