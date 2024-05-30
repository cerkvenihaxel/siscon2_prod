<?php

namespace App\Traits;
use App\Models\ArticulosZafiro;
trait ArticulosO2TerapiaTrait {

    protected function filteredArticle($articleQuery)
    {
        $productos2 = [
            "nebulizador",
            "ambu",
            "actos",
            "metformina"
        ];

        $query = ArticulosZafiro::query();

        $query->where(function ($query) use ($productos2) {
            foreach ($productos2 as $producto) {
                $query->orWhere('des_articulo', 'LIKE', '%' . $producto . '%');
            }
        });

        if ($articleQuery !== null) {
            $query->where('presentacion_completa', 'LIKE', '%' . $articleQuery . '%');
        }

        $articles = $query->get();

        return $articles;
    }

}
