<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProveedoresProtesisSeeder extends Seeder
{



    public function run()
    {
        $names = [
            "AMPLITONE",
            "ANGIOCOR",
            "ARCONTECH",
            "ARTRO S.A",
            "BERNAT",
            "BIOLATINA",
            "BIONOR",
            "CARDIO -PACK",
            "CARE MEDICAL",
            "CARE QUIP.",
            "CHEMA",
            "DEBENE SA",
            "DECADE",
            "DOCTOR PIE",
            "ELVIRA",
            "ENDOVIA",
            "FORCA",
            "FORUM",
            "GLOBAL MEDICA S.A",
            "GMRS",
            "GS-BIO",
            "IGMA",
            "IM SALUD",
            "IMECO",
            "IMPLACOR",
            "IMPLANTES MEDICOS",
            "INTERMED.",
            "INTERSIL",
            "IP MAGNA",
            "KINERET",
            "MAT MEDICAL",
            "MC MEDICAL",
            "MED CARE",
            "MEDICAL IMPLANTS",
            "MEDICAL MILENIUM",
            "MEDICAL SUPPLIES",
            "MEDICAL TEAM",
            "MEDKIT SRL",
            "MEDPRO",
            "NEXO",
            "NORTH MEDICAL",
            "NOVA SOLUCIONES",
            "NOWA",
            "OLYMPIA",
            "OMICRON",
            "ORT. MIGUEL ANGEL",
            "ORTOPEDIA BERNAT",
            "ORTOPEDIA CHEMA",
            "ORTOPEDIA CHIAVASSA",
            "ORTOPEDIA MAYO",
            "ORTOPEDIA SANTA LUCIA",
            "OSTEORIESTRA",
            "PROMEDICAL",
            "PROMEDON",
            "Prueba",
            "QRA",
            "RAPALAR",
            "REHAVITA",
            "ROFREN",
            "SANTA LUCIA",
            "SB TORRES",
            "SILFAB",
            "SURGICAL SUPPLY",
            "SURMESH",
            "SWIPRO",
            "TECHNO HEALTH",
            "UNIFARMA",
            "UNLAR",
            "VALMI",
            "WIDEX"
        ];

        $convenio = "Protesis";

        foreach ($names as $name) {
            DB::table('proveedores_protesis')->insert([
                [
                    "name" => $name,
                    "convenio" => $convenio,
                ],
            ]);
        }

    }
}
