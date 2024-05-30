<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ArticulosZafiro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ArticulosO2TerapiaTrait;
use function PHPUnit\Framework\isNull;

class ArticulosO2Terapia extends Controller
{
    use ArticulosO2TerapiaTrait;
    public function getArticles(Request $request){
        // TODO Agregar validaciones

        $query = $request->query('description');

        $articles = $this->filteredArticle($query);

        if($articles->isEmpty()){
           $message = 'Artículo no encontrado';
           $code = 400;
        }
        else {
            $message = 'Artículo encontrado';
            $code = 200;
        }

        return response()->json([
            'articles' => $articles,
            'message' => $message
        ], $code);
    }


}
