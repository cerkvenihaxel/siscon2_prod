<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Afiliados;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isNull;

class AfiliadosAPI extends Controller
{
    public function searchAfiliateByDNI(Request $request){

        $rules = [
            'dni' => 'string|required|min:1|max:8'
        ];

        $validation = Validator::make($request->all(), $rules);
        if($validation->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validation->errors()
            ], 422);
        }

        $dni = $request->input('dni');
        $afiliado = Afiliados::where('documento',   $dni)->get();

        if($afiliado->isEmpty()){
            $message = 'Afiliado no encontrado';
            $code = 404;
        }  else {
            $message = 'Afiliado encontrado';
            $code = 200;
        }

        return response()->json([
            'data' => $afiliado,
            'message' => $message
        ], $code);
    }

    public function searchAfiliateByID(Request $request){

        $rules = [
            'id' => 'string|required|min:1|max:8'
        ];

        $validation = Validator::make($request->all(), $rules);
        if($validation->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validation->errors()
            ], 422);
        }

        $id = $request->input('id');
        $afiliado = Afiliados::where('id',   $id)->get();

        if($afiliado->isEmpty()){
            $message = 'Afiliado no encontrado';
            $code = 404;
        }  else {
            $message = 'Afiliado encontrado';
            $code = 200;
        }

        return response()->json([
            'data' => $afiliado,
            'message' => $message
        ], $code);
    }
}
