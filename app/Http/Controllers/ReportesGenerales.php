<?php

namespace App\Http\Controllers;

use App\Exports\ReporteASDJ;
use App\Exports\ReporteMedicosExport;
use App\Exports\ReporteProveedoresExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;


class ReportesGenerales extends Controller
{

    public function index(){
        return view('reportesgrales.index');
    }

    public function reporteProveedoresExcel(){


// Configura la conexión a la base de datos en el archivo .env o en el archivo de configuración de Laravel (config/database.php)
$connection = config('database.default'); // Esto asumirá la conexión predeterminada definida en config/database.php

// Realiza la consulta utilizando Eloquent
$resultados = DB::connection($connection)->table(
        DB::raw('
        (
    SELECT "cotizaciones" AS tabla, created_at, proveedor AS sub_proveedor
    FROM cotizaciones
    WHERE YEAR(created_at) = 2023 AND MONTH(created_at) BETWEEN 1 AND 12
    UNION ALL
    SELECT "adjudicaciones" AS tabla, created_at, adjudicatario AS sub_proveedor
    FROM adjudicaciones
	WHERE YEAR(created_at) = 2023 AND MONTH(created_at) BETWEEN 1 AND 12
    UNION ALL
    SELECT "presentacion" AS tabla, created_at, proveedor AS sub_proveedor
    FROM presentacion
    WHERE YEAR(created_at) = 2023 AND MONTH(created_at) BETWEEN 1 AND 12
) AS subconsulta'))
    ->selectRaw('
            CASE
		WHEN sub_proveedor IN ("Angiocor") THEN "ANGIOCOR"
		WHEN sub_proveedor IN ("Artro", "ARTRO S.A") THEN "ARTRO S.A"
        WHEN sub_proveedor IN ("Aved") THEN "AVED"
        WHEN sub_proveedor IN ("BERNAT.", "Ortopedia Bernat", "ORTOPEDIA BERNAT.", "BERNAT") THEN "ORTOPEDIA BERNAT"
        WHEN sub_proveedor IN ("Biolatina") THEN "BIOLATINA"
        WHEN sub_proveedor IN ("Bionor") THEN "BIONOR"
        WHEN sub_proveedor IN ("CARDIO -PACK","CardioPack") THEN "CARDIO-PACK"
        WHEN sub_proveedor IN ("CARE QUIP","CARE-QUIP") THEN "CARE QUIP."
		WHEN sub_proveedor IN ("CareMedical","CarMedical", "CAR MEDICAL") THEN "CARE MEDICAL"
		WHEN sub_proveedor IN ("CHEMA","ORTOPEDIA CHEMA.") THEN "ORTOPEDIA CHEMA"
		WHEN sub_proveedor IN ("CHIAVASSA.") THEN "ORTOPEDIA CHIAVASSA"
		WHEN sub_proveedor IN ("CONSELGI WIDEX") THEN "WIDEX"
		-- DEBENE SA se cargó bien
		WHEN sub_proveedor IN ("DECADE.") THEN "DECADE"
		WHEN sub_proveedor IN ("DOCTOR PIE.","DR PIE","DR PIE.","PROPIEDAD DR PIE.") THEN "DOCTOR PIE"
		-- DrGLOBAL MEDICA quedó en rojo en el excel de sol, hay que borrarlo?
		-- ELVIRA se cargó bien
		-- ENDOVIA se cargó bien
		-- EXACTECH se elimina? está en rojo en el excel de sol
		WHEN sub_proveedor IN ("Forca", "Forca SRL","FORCA.") THEN "FORCA"
		-- FORUM se cargó bien
		WHEN sub_proveedor IN ("Global Medica","Global Medica  S.A.","Global Medica S.A","Global Medica S.A.","GLOBAL MEDICA.","GLOBAL MEDICAL","GM","GM SA","GMSA") THEN "GLOBAL MEDICA S.A."
		-- GMR S.A. ? lo dejé así
		WHEN sub_proveedor IN ("GD.BIO","GS  BIO.","GS BIO","GS BIO.","GS-BIO","GS.BIO","GSBIO") THEN "GS-BIO"
		WHEN sub_proveedor IN ("GMR S.A.") THEN "GMRS"
		WHEN sub_proveedor IN ("IGMA.") THEN "IGMA"
		WHEN sub_proveedor IN ("IM Salud") THEN "IM SALUD"
		-- IMECO se cargó bien
		WHEN sub_proveedor IN ("IMPLACOR.", "Implacor") THEN "IMPLACOR"
		WHEN sub_proveedor IN ("Implantes Medicos", "Implantes Medicos - cotización sin tornillos solicitados") THEN "IMPLANTES MEDICOS"
		WHEN sub_proveedor IN ("IMTERMED","INTERMED", "Intermed Rehab","INTERMED.") THEN "INTERMED."
		WHEN sub_proveedor IN ("INTERSIL.") THEN "INTERSIL"
		WHEN sub_proveedor IN ("Ip Magna") THEN "IP MAGNA"
		WHEN sub_proveedor IN ("Kineret") THEN "KINERET"
		WHEN sub_proveedor IN ("Mat Medical") THEN "MAT MEDICAL"
		-- MC MEDICAL se cargó bien
		WHEN sub_proveedor IN ("Medcare") THEN "MED CARE"
		WHEN sub_proveedor IN ("Medel") THEN "MED-EL"
		WHEN sub_proveedor IN ("Medical Implants") THEN "MEDICAL IMPLANTS"
		WHEN sub_proveedor IN ("Medical Milenium") THEN "MEDICAL MILENIUM"
		WHEN sub_proveedor IN ("Medical Supplies") THEN "MEDICAL SUPPLIES"
		WHEN sub_proveedor IN ("Medical Team") THEN "MEDICAL TEAM"
		WHEN sub_proveedor IN ("MEDKIT") THEN "MEDKIT SRL"
		WHEN sub_proveedor IN ("Medpro") THEN "MEDPRO"
		WHEN sub_proveedor IN ("Miguel Angel", "ORT. MIGUEL ANGEL","Ortopedia Miguel Angel") THEN "ORTOPEDIA MIGUEL ANGEL"
		-- MT? qué hacer? se deja como está o se elimina?
		WHEN sub_proveedor IN ("Nexo") THEN "NEXO"
		WHEN sub_proveedor IN ("North Medical") THEN "NORTH MEDICAL"
		WHEN sub_proveedor IN ("Nova","Nova Soluciones Quirúrgicas", "Nova Soluciones") THEN "NOVA SOLUCIONES"
		WHEN sub_proveedor IN ("Nowa", "Nowa Portesis", "Nowa Protesis", "Nowa Protesis", "Nowakowski Maria Clara") THEN "NOWA"
		-- OLYMPIA está bien cargado
		WHEN sub_proveedor IN ("Omicron") THEN "OMICRON"
		WHEN sub_proveedor IN ("Ortopedia Mayo") THEN "ORTOPEDIA MAYO"
		WHEN sub_proveedor IN ("Ortopedia Rapalar", "RAPALAR") THEN "ORTOPEDIA RAPALAR"
		WHEN sub_proveedor IN ("Santa Lucia", "SANTA LUCIA.", "SANTA LUCIA") THEN "ORTOPEDIA SANTA LUCIA"
		WHEN sub_proveedor IN ("OSTEORIESTRA.", "Osteoriestra") THEN "OSTEORIESTRA"
		-- PFM? qué es?
		WHEN sub_proveedor IN ("PROMEDICAL.") THEN "PROMEDICAL"
		WHEN sub_proveedor IN ("PROMEDON.") THEN "PROMEDON"
		-- sub_proveedor prueba, sub_proveedorDOS, sub_proveedorTRES, sub_proveedorUNO, Prueba deberían borrarse no?
		WHEN sub_proveedor IN ("QRA INSUMOS") THEN "QRA"
		WHEN sub_proveedor IN ("REHAVITA.") THEN "REHAVITA"
		-- ROFREN está bien cargado
		-- RS MEDICA ? lo dejo como está
		-- SANTA LUCIA lo dejé como ORTOPEDIA SANTA LUCIA
		WHEN sub_proveedor IN ("SbTorres", "SB Torres") THEN "SB TORRES"
		WHEN sub_proveedor IN ("SILFAB.") THEN "SILFAB"
		WHEN sub_proveedor IN ("Silmag") THEN "SILMAG"
		WHEN sub_proveedor IN ("SURGICAL SUPLLY","SURGICAL SUPPLY","SURGICAL SUPPLY.","SURGICALSUPPLY") THEN "SURGICAL SUPPLY"
		WHEN sub_proveedor IN ("SURMESH.") THEN "SURMESH"
		-- SWIPRO está bien cargado
		WHEN sub_proveedor IN ("TECHNO  HEALTH", "Techno Healt", "TECHNO HEALTH", "Tecnosalud","TECNOSALUD.", "TECHNO-HEAT") THEN "TECHNO HEALTH"
		-- UNIFARMA está bien cargado
		WHEN sub_proveedor IN ("Valmi") THEN "VALMI"
        ELSE sub_proveedor
    END AS Proveedor,
    CASE
    	WHEN MONTH(subconsulta.created_at) = 1 THEN "Enero"
		WHEN MONTH(subconsulta.created_at) = 2 THEN "Febrero"
    	WHEN MONTH(subconsulta.created_at) = 3 THEN "Marzo"
		WHEN MONTH(subconsulta.created_at) = 4 THEN "Abril"
    	WHEN MONTH(subconsulta.created_at) = 5 THEN "Mayo"
		WHEN MONTH(subconsulta.created_at) = 6 THEN "Junio"
    	WHEN MONTH(subconsulta.created_at) = 7 THEN "Julio"
		WHEN MONTH(subconsulta.created_at) = 8 THEN "Agosto"
    	WHEN MONTH(subconsulta.created_at) = 9 THEN "Septiembre"
		WHEN MONTH(subconsulta.created_at) = 10 THEN "Octubre"
    	WHEN MONTH(subconsulta.created_at) = 11 THEN "Noviembre"
		WHEN MONTH(subconsulta.created_at) = 12 THEN "Diciembre"
    ELSE "Otros"
    END AS Mes,
    COUNT(CASE WHEN subconsulta.tabla = "cotizaciones" THEN 1 END) AS Cotizadas,
    COUNT(CASE WHEN subconsulta.tabla = "adjudicaciones" THEN 1 END) AS Adjudicadas,
    COUNT(CASE WHEN subconsulta.tabla = "presentacion" THEN 1 END) AS Finalizadas
    ')->groupBy('Proveedor', 'Mes')->orderBy('Proveedor', 'asc')->get();

        return \Maatwebsite\Excel\Facades\Excel::download(new ReporteProveedoresExport($resultados), 'ReporteProveedores.xlsx');
    }

    public function reporteMedicosExcel()
    {

        $connection = config('database.default'); // Esto asumirá la conexión predeterminada definida en config/database.php


        $resultados = DB::table('entrantes', 'e')
            ->selectRaw('
        medicos.nombremedico as Medico,
        SUM(CASE WHEN e.created_at BETWEEN "2023-01-01" AND "2023-02-01" THEN 1 ELSE 0 END) AS ENERO,
        SUM(CASE WHEN e.created_at BETWEEN "2023-02-01" AND "2023-03-01" THEN 1 ELSE 0 END) AS FEBRERO,
        SUM(CASE WHEN e.created_at BETWEEN "2023-03-01" AND "2023-04-01" THEN 1 ELSE 0 END) AS MARZO,
        SUM(CASE WHEN e.created_at BETWEEN "2023-04-01" AND "2023-05-01" THEN 1 ELSE 0 END) AS ABRIL,
        SUM(CASE WHEN e.created_at BETWEEN "2023-05-01" AND "2023-06-01" THEN 1 ELSE 0 END) AS MAYO,
        SUM(CASE WHEN e.created_at BETWEEN "2023-06-01" AND "2023-07-01" THEN 1 ELSE 0 END) AS JUNIO,
        SUM(CASE WHEN e.created_at BETWEEN "2023-07-01" AND "2023-08-01" THEN 1 ELSE 0 END) AS JULIO
    ')
            ->join('medicos', 'e.medicos_id', '=', 'medicos.id')
            ->groupBy('e.medicos_id') // Agregar esta línea para agrupar por medicos_id
            ->get();



        return \Maatwebsite\Excel\Facades\Excel::download(new ReporteMedicosExport($resultados), 'ReporteMedicos.xlsx');
    }

    public function reporteAdjudicadosAnuladosSA(){

        $connection = config('database.default'); // Esto asumirá la conexión predeterminada definida en config/database.php


        $resultados = DB::table('cotizaciones', 'c')->selectRaw('
            c.proveedor AS "Proveedor",
            c.created_at AS "Fecha de carga",
            CASE
	   		WHEN MONTH(c.created_at) = 1 THEN "Enero"
			WHEN MONTH(c.created_at) = 2 THEN "Febrero"
	    	WHEN MONTH(c.created_at) = 3 THEN "Marzo"
			WHEN MONTH(c.created_at) = 4 THEN "Abril"
	    	WHEN MONTH(c.created_at) = 5 THEN "Mayo"
			WHEN MONTH(c.created_at) = 6 THEN "Junio"
	    	WHEN MONTH(c.created_at) = 7 THEN "Julio"
			WHEN MONTH(c.created_at) = 8 THEN "Agosto"
	    	WHEN MONTH(c.created_at) = 9 THEN "Septiembre"
			WHEN MONTH(c.created_at) = 10 THEN "Octubre"
	    	WHEN MONTH(c.created_at) = 11 THEN "Noviembre"
			WHEN MONTH(c.created_at) = 12 THEN "Diciembre"
	    ELSE "Otros"
	    END AS "Mes de Carga",
	    afiliados.apeynombres AS "Nombre", clinicas.nombre AS "Clínica", c.edad AS "Edad",
	   c.nrosolicitud AS "N° de Solicitud", medicos.nombremedico AS "Médico", estado_solicitud.estado AS "Estado Solicitud"


        ')
            ->join('afiliados', 'c.afiliados_id', '=', 'afiliados.id')
            ->join('clinicas', 'c.clinicas_id', '=', 'clinicas.id')
            ->join('medicos', 'c.medicos_id', '=', 'medicos.id')
            ->join('estado_solicitud', 'c.estado_solicitud_id', '=', 'estado_solicitud.id')
            ->orderBy('c.proveedor', 'asc')
            ->get();

       //dd($resultados[0]);

        return \Maatwebsite\Excel\Facades\Excel::download(new ReporteASDJ($resultados), 'ReporteASDJ.xlsx');


    }

}
