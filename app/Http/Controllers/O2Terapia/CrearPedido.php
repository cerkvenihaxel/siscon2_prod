<?php

namespace App\Http\Controllers\O2Terapia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrearPedido extends Controller
{
    public function newPedido(){


        return view('O2Terapia.newPedido');
    }
}
