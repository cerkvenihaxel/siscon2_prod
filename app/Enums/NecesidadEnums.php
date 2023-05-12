<?php

namespace App\Enums;

use Illuminate\Http\Request;

class NecesidadEnums
{
    public function fromValue($value){
        $necesidad = $value;
        switch ($necesidad) {
            case 1:
                return 'URGENTE';
                break;
            case 2:
                return 'PROGRAMADO';
                break;
            case 3:
                return 'DEVOLUCION';
                break;
            default:
                return ' ';
                break;
        }
    }
}
