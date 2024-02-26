<?php

namespace App\Http\Controllers;

use App\Exports\ReporteASDJ;
use App\Exports\ReporteEspecialidad;
use App\Exports\ReporteMedicosExport;
use App\Exports\ReporteMes;
use App\Exports\ReporteProveedoresExport;
use App\Exports\ReporteSinCotizar;
use App\Exports\ReporteMedicamentosExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;
use Carbon\Carbon;

function consultaProveedores($start_date, $end_date, $connection){
	$resultados = DB::connection($connection)->table(
		DB::raw('
		(
			SELECT "cotizaciones" AS tabla, created_at, proveedor AS sub_proveedor
			FROM cotizaciones
			WHERE created_at BETWEEN "'.$start_date.'" AND "'.$end_date.'"
			UNION ALL
			SELECT "adjudicaciones" AS tabla, ad.created_at, ad.adjudicatario AS sub_proveedor
			FROM adjudicaciones ad
			WHERE created_at BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND ad.estado_solicitud_id = 3 
			UNION ALL
			SELECT "autorizaciones" AS tabla, au.created_at, au.autorizado AS sub_proveedor
			FROM autorizaciones au
			WHERE created_at BETWEEN "'.$start_date.'" AND "'.$end_date.'"
		) AS subconsulta'))
		->selectRaw(''.filtro_proveedores('sub_proveedor').',
			YEAR(subconsulta.created_at) AS "Año",
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
			COUNT(CASE WHEN subconsulta.tabla = "autorizaciones" THEN 1 END) AS Finalizadas
		')->groupByRaw('Año, Proveedor, Mes')->orderByRaw('Proveedor, Mes, Año')->get();
	return $resultados;
}

function consultaMedicamentos($start_date, $end_date){
	$resultados = DB::table('pedido_medicamento', 'pm')->selectRaw('
			patologias.nombre, afiliados.apeynombres, afiliados.documento, pm.nroAfiliado, 
			articulosZafiro.presentacion_completa, pedido_medicamento_detail.cantidad, 
			cotizacion_convenio.zona_residencia, pm.created_at,pm.nrosolicitud
		')
			->leftJoin('afiliados','pm.nroAfiliado','=','afiliados.nroAfiliado')
			->leftJoin('pedido_medicamento_detail','pm.id','=','pedido_medicamento_id')
			->leftJoin('articulosZafiro','pedido_medicamento_detail.articuloZafiro_id','=','articulosZafiro.id')
			->leftJoin('cotizacion_convenio','pm.nrosolicitud','=','cotizacion_convenio.nrosolicitud')
			->leftJoin('patologias','pm.patologia','=','patologias.id')
			->whereBetween('pm.created_at', [$start_date, $end_date])
			->orderByRaw('patologias.nombre, articulosZafiro.presentacion_completa, afiliados.apeynombres')
			->get();

	return $resultados;
}

function consultaAdjudicadosAnuladosSA($start_date, $end_date){
	$resultados = DB::table('cotizaciones', 'c')->selectRaw('
		'.filtro_proveedores('c.proveedor').',
		c.created_at AS "fecha_de_carga",
		YEAR(c.created_at) AS "Año",
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
		END AS "mes_de_Carga",
		afiliados.apeynombres AS "Nombre", clinicas.nombre AS "Clínica", c.edad AS "Edad",
		c.nrosolicitud AS "nroSolicitud", medicos.nombremedico AS "Médico", 
		estado_solicitud.estado AS "estado_solicitud"
	')
	->leftJoin('afiliados', 'c.afiliados_id', '=', 'afiliados.id')
	->leftJoin('clinicas', 'c.clinicas_id', '=', 'clinicas.id')
	->leftJoin('medicos', 'c.medicos_id', '=', 'medicos.id')
	->leftJoin('estado_solicitud', 'c.estado_solicitud_id', '=', 'estado_solicitud.id')
	->whereBetween('c.created_at', [$start_date, $end_date])
	->orderByRaw('c.proveedor ,c.created_at DESC, estado_solicitud.estado')
	->get();

	return $resultados;
}

function consultaSinCotizar($start_date, $end_date){
	$resultados = DB::table('entrantes', 'e')->selectRaw('
		e.created_at AS "Fecha",
		YEAR(e.created_at) AS "Año",
		CASE
			WHEN MONTH(e.created_at) = 1 THEN "Enero"
			WHEN MONTH(e.created_at) = 2 THEN "Febrero"
			WHEN MONTH(e.created_at) = 3 THEN "Marzo"
			WHEN MONTH(e.created_at) = 4 THEN "Abril"
			WHEN MONTH(e.created_at) = 5 THEN "Mayo"
			WHEN MONTH(e.created_at) = 6 THEN "Junio"
			WHEN MONTH(e.created_at) = 7 THEN "Julio"
			WHEN MONTH(e.created_at) = 8 THEN "Agosto"
			WHEN MONTH(e.created_at) = 9 THEN "Septiembre"
			WHEN MONTH(e.created_at) = 10 THEN "Octubre"
			WHEN MONTH(e.created_at) = 11 THEN "Noviembre"
			WHEN MONTH(e.created_at) = 12 THEN "Diciembre"
		ELSE "Otros"
		END AS "mes_de_carga",
		e.nrosolicitud AS "nroSolicitud", afiliados.apeynombres AS "Nombre", clinicas.nombre AS "Clínica",
		e.edad AS "Edad", medicos.nombremedico AS "Médico", estado_solicitud.estado AS "estado_solicitud"
		')
			->leftJoin('afiliados', 'e.afiliados_id', '=', 'afiliados.id')
			->leftJoin('clinicas', 'e.clinicas_id', '=', 'clinicas.id')
			->leftJoin('medicos', 'e.medicos_id', '=', 'medicos.id')
			->leftJoin('estado_solicitud', 'e.estado_solicitud_id', '=', 'estado_solicitud.id')
			->whereBetween('e.created_at', [$start_date, $end_date])
			->whereIn('e.estado_solicitud_id', [5,8,9])
			->orderByRaw('e.created_at DESC')
			->get();

	return $resultados;
}

function consultaPorEspecialidad($start_date, $end_date){
	$resultados = DB::table('entrantes', 'e')->selectRaw('
		grupos.des_grupo as Especialidad,
		SUM(CASE WHEN e.created_at BETWEEN "2023-01-01" AND "2023-02-01" THEN 1 END) AS "INGRESO_ENERO_2023",
		SUM(CASE WHEN e.created_at BETWEEN "2023-02-01" AND "2023-03-01" THEN 1 END) AS "INGRESO_FEBRERO_2023",
		SUM(CASE WHEN e.created_at BETWEEN "2023-03-01" AND "2023-04-01" THEN 1 END) AS "INGRESO_MARZO_2023",
		SUM(CASE WHEN e.created_at BETWEEN "2023-04-01" AND "2023-05-01" THEN 1 END) AS "INGRESO_ABRIL_2023",
		SUM(CASE WHEN e.created_at BETWEEN "2023-05-01" AND "2023-06-01" THEN 1 END) AS "INGRESO_MAYO_2023",
		SUM(CASE WHEN e.created_at BETWEEN "2023-06-01" AND "2023-07-01" THEN 1 END) AS "INGRESO_JUNIO_2023",
		SUM(CASE WHEN e.created_at BETWEEN "2023-07-01" AND "2023-08-01" THEN 1 END) AS "INGRESO_JULIO_2023",
		SUM(CASE WHEN e.created_at BETWEEN "2023-08-01" AND "2023-09-01" THEN 1 END) AS "INGRESO_AGOSTO_2023",
		SUM(CASE WHEN e.created_at BETWEEN "2023-09-01" AND "2023-10-01" THEN 1 END) AS "INGRESO_SEPTIEMBRE_2023",
		SUM(CASE WHEN e.created_at BETWEEN "2023-10-01" AND "2023-11-01" THEN 1 END) AS "INGRESO_OCTUBRE_2023",
		SUM(CASE WHEN e.created_at BETWEEN "2023-11-01" AND "2023-12-01" THEN 1 END) AS "INGRESO_NOVIEMBRE_2023",
		SUM(CASE WHEN e.created_at BETWEEN "2023-12-01" AND "2024-01-01" THEN 1 END) AS "INGRESO_DICIEMBRE_2023",

		SUM(CASE WHEN e.created_at BETWEEN "2024-01-01" AND "2024-02-01" THEN 1 END) AS "INGRESO_ENERO_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-02-01" AND "2024-03-01" THEN 1 END) AS "INGRESO_FEBRERO_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-03-01" AND "2024-04-01" THEN 1 END) AS "INGRESO_MARZO_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-04-01" AND "2024-05-01" THEN 1 END) AS "INGRESO_ABRIL_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-05-01" AND "2024-06-01" THEN 1 END) AS "INGRESO_MAYO_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-06-01" AND "2024-07-01" THEN 1 END) AS "INGRESO_JUNIO_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-07-01" AND "2024-08-01" THEN 1 END) AS "INGRESO_JULIO_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-08-01" AND "2024-09-01" THEN 1 END) AS "INGRESO_AGOSTO_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-09-01" AND "2024-10-01" THEN 1 END) AS "INGRESO_SEPTIEMBRE_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-10-01" AND "2024-11-01" THEN 1 END) AS "INGRESO_OCTUBRE_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-11-01" AND "2024-12-01" THEN 1 END) AS "INGRESO_NOVIEMBRE_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-12-01" AND "2025-01-01" THEN 1 END) AS "INGRESO_DICIEMBRE_2024"
	')
		->leftJoin('grupos', 'e.grupo_articulos', '=', 'grupos.id')
		->whereBetween('e.created_at',[$start_date, $end_date])
		->groupBy('e.grupo_articulos')
		->get();

	return $resultados;
}

function consultaPorMes($start_date, $end_date){
	$resultados = DB::table('entrantes', 'e')->selectRaw('
		e.created_at as "fecha_de_carga",
		YEAR(e.created_at) AS "Año",
		CASE
			WHEN MONTH(e.created_at) = 1 THEN "Enero"
			WHEN MONTH(e.created_at) = 2 THEN "Febrero"
			WHEN MONTH(e.created_at) = 3 THEN "Marzo"
			WHEN MONTH(e.created_at) = 4 THEN "Abril"
			WHEN MONTH(e.created_at) = 5 THEN "Mayo"
			WHEN MONTH(e.created_at) = 6 THEN "Junio"
			WHEN MONTH(e.created_at) = 7 THEN "Julio"
			WHEN MONTH(e.created_at) = 8 THEN "Agosto"
			WHEN MONTH(e.created_at) = 9 THEN "Septiembre"
			WHEN MONTH(e.created_at) = 10 THEN "Octubre"
			WHEN MONTH(e.created_at) = 11 THEN "Noviembre"
			WHEN MONTH(e.created_at) = 12 THEN "Diciembre"
		ELSE "Otros"
		END AS "mes_de_carga",
		afiliados.apeynombres AS "Nombre" , afiliados.nroAfiliado AS "NroAfiliado",
		clinicas.nombre AS "Clínica", e.edad AS "Edad", e.nrosolicitud AS "NroSolicitud", 
		medicos.nombremedico as "Médico", estado_paciente.estado AS "estado_paciente", 
		estado_solicitud.estado AS "estado_solicitud", e.fecha_cirugia AS "fecha_de_cirugía",
		e.accidente AS "sufrió_accidente", necesidad.necesidad AS "Necesidad", 
		grupos.des_grupo AS "grupo_articulos", e.fecha_expiracion AS "fecha_de_expiración"
	')
		->leftJoin('afiliados', 'e.afiliados_id', '=', 'afiliados.id')
		->leftJoin('clinicas', 'e.clinicas_id', '=', 'clinicas.id')
		->leftJoin('medicos', 'e.medicos_id', '=', 'medicos.id')
		->leftJoin('estado_paciente', 'e.estado_paciente_id', '=', 'estado_paciente.id')
		->leftJoin('estado_solicitud', 'e.estado_solicitud_id', '=', 'estado_solicitud.id')
		->leftJoin('necesidad', 'e.necesidad', '=', 'necesidad.id')
		->leftJoin('grupos', 'e.grupo_articulos', '=', 'grupos.id')
		->whereBetween('e.created_at', [$start_date, $end_date])
		->orderBy('e.created_at', 'desc')
		->get();

		return $resultados;
}

function consultaPorMedico($start_date, $end_date){
	$resultados = DB::table('entrantes', 'e')->selectRaw('
        medicos.nombremedico as Medico,
        SUM(CASE WHEN e.created_at BETWEEN "2023-01-01" AND "2023-02-01" THEN 1 END) AS "ENERO_2023",
        SUM(CASE WHEN e.created_at BETWEEN "2023-02-01" AND "2023-03-01" THEN 1 END) AS "FEBRERO_2023",
        SUM(CASE WHEN e.created_at BETWEEN "2023-03-01" AND "2023-04-01" THEN 1 END) AS "MARZO_2023",
        SUM(CASE WHEN e.created_at BETWEEN "2023-04-01" AND "2023-05-01" THEN 1 END) AS "ABRIL_2023",
        SUM(CASE WHEN e.created_at BETWEEN "2023-05-01" AND "2023-06-01" THEN 1 END) AS "MAYO_2023",
        SUM(CASE WHEN e.created_at BETWEEN "2023-06-01" AND "2023-07-01" THEN 1 END) AS "JUNIO_2023",
        SUM(CASE WHEN e.created_at BETWEEN "2023-07-01" AND "2023-08-01" THEN 1 END) AS "JULIO_2023",
		SUM(CASE WHEN e.created_at BETWEEN "2023-08-01" AND "2023-09-01" THEN 1 END) AS "AGOSTO_2023",
		SUM(CASE WHEN e.created_at BETWEEN "2023-09-01" AND "2023-10-01" THEN 1 END) AS "SEPTIEMBRE_2023",
		SUM(CASE WHEN e.created_at BETWEEN "2023-10-01" AND "2023-11-01" THEN 1 END) AS "OCTUBRE_2023",
		SUM(CASE WHEN e.created_at BETWEEN "2023-11-01" AND "2023-12-01" THEN 1 END) AS "NOVIEMBRE_2023",
		SUM(CASE WHEN e.created_at BETWEEN "2023-12-01" AND "2024-01-01" THEN 1 END) AS "DICIEMBRE_2023",

		SUM(CASE WHEN e.created_at BETWEEN "2024-01-01" AND "2024-02-01" THEN 1 END) AS "ENERO_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-02-01" AND "2024-03-01" THEN 1 END) AS "FEBRERO_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-03-01" AND "2024-04-01" THEN 1 END) AS "MARZO_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-04-01" AND "2024-05-01" THEN 1 END) AS "ABRIL_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-05-01" AND "2024-06-01" THEN 1 END) AS "MAYO_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-06-01" AND "2024-07-01" THEN 1 END) AS "JUNIO_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-07-01" AND "2024-08-01" THEN 1 END) AS "JULIO_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-08-01" AND "2024-09-01" THEN 1 END) AS "AGOSTO_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-09-01" AND "2024-10-01" THEN 1 END) AS "SEPTIEMBRE_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-10-01" AND "2024-11-01" THEN 1 END) AS "OCTUBRE_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-11-01" AND "2024-12-01" THEN 1 END) AS "NOVIEMBRE_2024",
		SUM(CASE WHEN e.created_at BETWEEN "2024-12-01" AND "2025-01-01" THEN 1 END) AS "DICIEMBRE_2024"
	')
		->leftJoin('medicos', 'e.medicos_id', '=', 'medicos.id')
		->whereBetween('e.created_at', [$start_date, $end_date])
		->groupBy('e.medicos_id') // Agregar esta línea para agrupar por medicos_id
		->get();

	return $resultados;
}


function filtro_proveedores($tabla){
	$filtro_proveedor = '				
	CASE 
		WHEN '.$tabla.' IN ("Angiocor") THEN "ANGIOCOR"
		WHEN '.$tabla.' IN ("Artro", "ARTRO S.A") THEN "ARTRO S.A"
		WHEN '.$tabla.' IN ("Amplitone") THEN "AMPLITONE"
		WHEN '.$tabla.' IN ("Aved") THEN "AVED"
		WHEN '.$tabla.' IN ("BERNAT.", "Ortopedia Bernat", "ORTOPEDIA BERNAT.", "BERNAT") THEN "ORTOPEDIA BERNAT"
		WHEN '.$tabla.' IN ("Biolap") THEN "BIOLAP"
		WHEN '.$tabla.' IN ("Biolatina") THEN "BIOLATINA"
		WHEN '.$tabla.' IN ("Bionor") THEN "BIONOR"
		WHEN '.$tabla.' IN ("CARDIO -PACK","CardioPack") THEN "CARDIO-PACK"
		WHEN '.$tabla.' IN ("CareMedical","CarMedical", "CAR MEDICAL", "Care Medical") THEN "CARE MEDICAL"
		WHEN '.$tabla.' IN ("CareMedical","CarMedical", "CAR MEDICAL") THEN "CARE MEDICAL"
		WHEN '.$tabla.' IN ("CHEMA","ORTOPEDIA CHEMA.", "Ortopedia Chema") THEN "ORTOPEDIA CHEMA"
		WHEN '.$tabla.' IN ("CHIAVASSA.") THEN "ORTOPEDIA CHIAVASSA"
		WHEN '.$tabla.' IN ("CONSELGI WIDEX") THEN "WIDEX"
		-- DEBENE SA se cargó bien
		WHEN '.$tabla.' IN ("DECADE.", "Decade") THEN "DECADE"
		WHEN '.$tabla.' IN ("DOCTOR PIE.","DR PIE","DR PIE.","PROPIEDAD DR PIE.") THEN "DOCTOR PIE"
		-- DrGLOBAL MEDICA quedó en rojo en el excel de sol, hay que borrarlo?
		WHEN '.$tabla.' IN ("Elvira") THEN "ELVIRA"
		-- ENDOVIA se cargó bien
		-- EXACTECH se elimina? está en rojo en el excel de sol
		WHEN '.$tabla.' IN ("Forca", "Forca SRL","FORCA.", "Forca S.R.L") THEN "FORCA"
		-- FORUM se cargó bien
		WHEN '.$tabla.' IN ("Global Medica - Amplitone") THEN "GLOBAL MEDICA - AMPLITONE"
		WHEN '.$tabla.' IN ("Global Medica - Widex") THEN "GLOBAL MEDICA - WIDEX"
		WHEN '.$tabla.' IN ("Global Medica","Global Medica  S.A.","Global Medica S.A","Global Medica S.A.","GLOBAL MEDICA.","GLOBAL MEDICAL","GM","GM SA","GMSA") THEN "GLOBAL MEDICA S.A."
		-- GMR S.A. ? lo dejé así
		WHEN '.$tabla.' IN ("GD.BIO","GS  BIO.","GS BIO","GS BIO.","GS-BIO","GS.BIO","GSBIO","Gsbio") THEN "GS-BIO"
		WHEN '.$tabla.' IN ("GM","GM SA","GMR S.A.","GMSA") THEN "GMRS"
		WHEN '.$tabla.' IN ("IGMA.", "Igma") THEN "IGMA"
		WHEN '.$tabla.' IN ("IM Salud") THEN "IM SALUD"
		-- IMECO se cargó bien
		WHEN '.$tabla.' IN ("IMPLACOR.", "Implacor") THEN "IMPLACOR"
		WHEN '.$tabla.' IN ("Implantes Medicos", "Implantes Medicos - cotización sin tornillos solicitados") THEN "IMPLANTES MEDICOS"
		WHEN '.$tabla.' IN ("IMTERMED","INTERMED", "Intermed Rehab","INTERMED.") THEN "INTERMED."
		WHEN '.$tabla.' IN ("INTERSIL.", "Intersil") THEN "INTERSIL"
		WHEN '.$tabla.' IN ("Ip Magna") THEN "IP MAGNA"
		WHEN '.$tabla.' IN ("Kineret") THEN "KINERET"
		WHEN '.$tabla.' IN ("Mat Medical") THEN "MAT MEDICAL"
		-- MC MEDICAL se cargó bien
		WHEN '.$tabla.' IN ("Medcare") THEN "MED CARE"
		WHEN '.$tabla.' IN ("Medel","MEDEL") THEN "MED-EL"
		WHEN '.$tabla.' IN ("Medical Implants") THEN "MEDICAL IMPLANTS"
		WHEN '.$tabla.' IN ("Medical Milenium") THEN "MEDICAL MILENIUM"
		WHEN '.$tabla.' IN ("Medical Supplies") THEN "MEDICAL SUPPLIES"
		WHEN '.$tabla.' IN ("Medical Team", "Medical Team SIN S") THEN "MEDICAL TEAM"
		WHEN '.$tabla.' IN ("MEDKIT") THEN "MEDKIT SRL"
		WHEN '.$tabla.' IN ("Medpro") THEN "MEDPRO"
		WHEN '.$tabla.' IN ("MG Asistencia e Ingenieria Clinica") THEN "MG ASISTENCIA E INGENIERIA CLINICA"
		WHEN '.$tabla.' IN ("Miguel Angel", "ORT. MIGUEL ANGEL","Ortopedia Miguel Angel") THEN "ORTOPEDIA MIGUEL ANGEL"
		-- MT? qué hacer? se deja como está o se elimina? 
		WHEN '.$tabla.' IN ("Mobility") THEN "MOBILITY"
		WHEN '.$tabla.' IN ("Nexo") THEN "NEXO"
		WHEN '.$tabla.' IN ("North Medical") THEN "NORTH MEDICAL"
		WHEN '.$tabla.' IN ("Nova","Nova Soluciones Quirúrgicas","Nova Soluciones Quirurgicas", "Nova Soluciones Quirugicas", "Nova Soluciones") THEN "NOVA SOLUCIONES" 
		WHEN '.$tabla.' IN ("Nowa", "Nowa Portesis", "Nowa Protesis", "Nowa Protesis", "Nowakowski Maria Clara") THEN "NOWA"
		-- OLYMPIA está bien cargado
		WHEN '.$tabla.' IN ("Omicron") THEN "OMICRON"
		WHEN '.$tabla.' IN ("Ortopedia Camino") THEN "ORTOPEDIA CAMINO"
		WHEN '.$tabla.' IN ("Ortopedia Mayo") THEN "ORTOPEDIA MAYO"
		WHEN '.$tabla.' IN ("Ortopedia Rapalar", "RAPALAR") THEN "ORTOPEDIA RAPALAR"
		WHEN '.$tabla.' IN ("Santa Lucia", "SANTA LUCIA.", "SANTA LUCIA", "Sta Lucia") THEN "ORTOPEDIA SANTA LUCIA"
		WHEN '.$tabla.' IN ("Silfab") THEN "SILFAB"
		WHEN '.$tabla.' IN ("OSTEORIESTRA.", "Osteoriestra") THEN "OSTEORIESTRA"
		-- PFM? qué es?
		WHEN '.$tabla.' IN ("PROMEDICAL.") THEN "PROMEDICAL"
		WHEN '.$tabla.' IN ("PROMEDON.", "Promedon") THEN "PROMEDON"
		-- '.$tabla.' prueba, '.$tabla.'DOS, '.$tabla.'TRES, '.$tabla.'UNO, Prueba deberían borrarse no?
		WHEN '.$tabla.' IN ("QRA INSUMOS") THEN "QRA"
		WHEN '.$tabla.' IN ("REHAVITA.") THEN "REHAVITA"
		WHEN '.$tabla.' IN ("Rofren") THEN "ROFREN"
		-- RS MEDICA ? lo dejo como está
		-- SANTA LUCIA lo dejé como ORTOPEDIA SANTA LUCIA
		WHEN '.$tabla.' IN ("SbTorres", "SB Torres") THEN "SB TORRES"
		WHEN '.$tabla.' IN ("SILFAB.") THEN "SILFAB"
		WHEN '.$tabla.' IN ("Silmag") THEN "SILMAG"
		WHEN '.$tabla.' IN ("SURGICAL SUPLLY","SURGICAL SUPPLY","SURGICAL SUPPLY.","SURGICALSUPPLY") THEN "SURGICAL SUPPLY"
		WHEN '.$tabla.' IN ("SURMESH.") THEN "SURMESH"
		WHEN '.$tabla.' IN ("Swipro") THEN "SWIPRO"
		WHEN '.$tabla.' IN ("TECHNO  HEALTH", "Techno Healt", "TECHNO HEALTH", "Tecnosalud","TECNOSALUD.", "TECHNO-HEAT") THEN "TECHNO HEALTH"
		-- UNIFARMA está bien cargado
		WHEN '.$tabla.' IN ("Valmi") THEN "VALMI"
	ELSE '.$tabla.'
	END AS Proveedor
	';
	return $filtro_proveedor;
}






class ReportesGenerales extends Controller{
    public function index(){
        return view('reportesgrales.index');
    }

    public function reporteProveedoresExcel(Request $request){
		//recibo las fechas, desde el método HTTP POST tomado del formulario:
		$dateRange = $request->input('daterange');
		list($start_date, $end_date) = explode(' - ', $dateRange);
		$start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
		$end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();

		// Configura la conexión a la base de datos en el archivo .env o en el archivo de configuración de Laravel (config/database.php)
		$connection = config('database.default'); // Esto asumirá la conexión predeterminada definida en config/database.php

		// Realiza la consulta utilizando Eloquent
		$resultados = consultaProveedores($start_date, $end_date, $connection);

		// dd($resultados);
		return \Maatwebsite\Excel\Facades\Excel::download(new ReporteProveedoresExport($resultados), 'ReporteProveedorestado_solicitud.xlsx');
    }
	
	public function dateRangeInfoProv(Request $request){	
		$dateRange = $request->input('daterange');
	
		list($start_date, $end_date) = explode(' - ', $dateRange);
		$start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
		$end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();
		
		// $date = Carbon::parse($date)->format('Y-m-d');

		// Configura la conexión a la base de datos en el archivo .env o en el archivo de configuración de Laravel (config/database.php)
		$connection = config('database.default'); // Esto asumirá la conexión predeterminada definida en config/database.php
		// Realiza la consulta utilizando Eloquent
		$data = consultaProveedores($start_date, $end_date, $connection);
		
		$fecha = date('d-m-Y H:i:s');

		// dd($data[0]);
		return view('reportesgrales/RGViewInfPorProveedores', compact('data', 'start_date', 'end_date', 'fecha'));
	}

    
	
	
	public function reporteMedicamentosExcel(Request $request){
		//recibo las fechas, desde el método HTTP POST tomado del formulario:
		$dateRange = $request->input('daterange');
		list($start_date, $end_date) = explode(' - ', $dateRange);
		$start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
		$end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();

		// Configura la conexión a la base de datos en el archivo .env o en el archivo de configuración de Laravel (config/database.php)
		$connection = config('database.default'); // Esto asumirá la conexión predeterminada definida en config/database.php

		// Realiza la consulta utilizando Eloquent
		$resultados = consultaMedicamentos($start_date, $end_date);

			// dd($resultados);

        return \Maatwebsite\Excel\Facades\Excel::download(new ReporteMedicamentosExport($resultados), 'ReportePorMedicacion.xlsx');
    }
	
	public function dateRangeMedicamentos(Request $request){
		$dateRange = $request->input('daterange');
	
		list($start_date, $end_date) = explode(' - ', $dateRange);
		$start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
		$end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();
		
		// $date = Carbon::parse($date)->format('Y-m-d');

		// Realiza la consulta utilizando Eloquent
		$data = consultaMedicamentos($start_date, $end_date);
		$fecha = date('d-m-Y H:i:s');

		// dd($data[0]);
		return view('reportesgrales/RGViewInfSolicitudesCargadas', compact('data', 'start_date', 'end_date', 'fecha'));
	}


    
	
	public function reporteAdjudicadosAnuladosSA(Request $request){
		//recibo las fechas, desde el método HTTP POST tomado del formulario:
		$dateRange = $request->input('daterange');
		list($start_date, $end_date) = explode(' - ', $dateRange);
		$start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
		$end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();
        
		$connection = config('database.default'); // Esto asumirá la conexión predeterminada definida en config/database.php

        $resultados = consultaAdjudicadosAnuladosSA($start_date, $end_date);

		// dd($resultados[0]);

        return \Maatwebsite\Excel\Facades\Excel::download(new ReporteASDJ($resultados), 'ReporteASDJ.xlsx');
    }

	public function dateRangeAdjudicadosAnuladosSA(Request $request){
		$dateRange = $request->input('daterange');
	
		list($start_date, $end_date) = explode(' - ', $dateRange);
		$start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
		$end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();
		
		// $date = Carbon::parse($date)->format('Y-m-d');

		// Realiza la consulta utilizando Eloquent
		$data = consultaAdjudicadosAnuladosSA($start_date, $end_date);
		$fecha = date('d-m-Y H:i:s');

		// dd($data[0]);
		return view('reportesgrales/RGViewAnuAdjSA', compact('data', 'start_date', 'end_date', 'fecha'));
	}




    public function reporteSinCotizar(Request $request){

			//recibo las fechas, desde el método HTTP POST tomado del formulario:
			$dateRange = $request->input('daterange');
			list($start_date, $end_date) = explode(' - ', $dateRange);
			$start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
			$end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();

            $connection = config('database.default'); // Esto asumirá la conexión predeterminada definida en config/database.php

            $resultados = consultaSinCotizar($start_date, $end_date);

           // dd($resultados);
        return \Maatwebsite\Excel\Facades\Excel::download(new ReporteSinCotizar($resultados), 'reporte-solicitudes-sin-cotizar'.now().'.xlsx');
    }

	public function dateRangeSinCotizar(Request $request){
		$dateRange = $request->input('daterange');
	
		list($start_date, $end_date) = explode(' - ', $dateRange);
		$start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
		$end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();
		
		// $date = Carbon::parse($date)->format('Y-m-d');

		// Realiza la consulta utilizando Eloquent
		$data = consultaSinCotizar($start_date, $end_date);
		$fecha = date('d-m-Y H:i:s');

		// dd($data[0]);
		return view('reportesgrales/RGViewInfSolicitudesSinCotizar', compact('data', 'start_date', 'end_date', 'fecha'));
	}


    public function reporteEspecialidad(Request $request){

		//recibo las fechas, desde el método HTTP POST tomado del formulario:
		$dateRange = $request->input('daterange');
		list($start_date, $end_date) = explode(' - ', $dateRange);
		$start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
		$end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();

        $connection = config('database.default'); // Esto asumirá la conexión predeterminada definida en config/database.php

        $resultados = consultaporEspecialidad($start_date, $end_date);
        //ddd($resultados[0]);
        return \Maatwebsite\Excel\Facades\Excel::download(new ReporteEspecialidad($resultados), 'reporte-especialidad'.now().'.xlsx');
    }

	public function dateRangePorEspecialidad(Request $request){
		$dateRange = $request->input('daterange');
	
		list($start_date, $end_date) = explode(' - ', $dateRange);
		$start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
		$end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();
		
		// $date = Carbon::parse($date)->format('Y-m-d');

		// Realiza la consulta utilizando Eloquent
		$data = consultaporEspecialidad($start_date, $end_date);
		$fecha = date('d-m-Y H:i:s');

		// dd($data[0]);
		return view('reportesgrales/RGViewInfPorEpecialidad', compact('data', 'start_date', 'end_date', 'fecha'));
	}





    public function reporteMes(Request $request){
		//recibo las fechas, desde el método HTTP POST tomado del formulario:
		$dateRange = $request->input('daterange');
		list($start_date, $end_date) = explode(' - ', $dateRange);
		$start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
		$end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();

        $resultados = consultaPorMes($start_date, $end_date);

        //dd($resultados);

        return \Maatwebsite\Excel\Facades\Excel::download(new ReporteMes($resultados), 'reporte-por-mes'.now().'.xlsx');
    }

	public function dateRangePorMes(Request $request){
		$dateRange = $request->input('daterange');
	
		list($start_date, $end_date) = explode(' - ', $dateRange);
		$start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
		$end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();
		
		// $date = Carbon::parse($date)->format('Y-m-d');

		// Realiza la consulta utilizando Eloquent
		$data = consultaPorMes($start_date, $end_date);
		$fecha = date('d-m-Y H:i:s');

		// dd($data[0]);
		return view('reportesgrales/RGViewInfPorMes', compact('data', 'start_date', 'end_date', 'fecha'));
	}




	public function reporteMedicosExcel(Request $request){
        //recibo las fechas, desde el método HTTP POST tomado del formulario:
		$dateRange = $request->input('daterange');
		list($start_date, $end_date) = explode(' - ', $dateRange);
		$start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
		$end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();
		
		$connection = config('database.default'); // Esto asumirá la conexión predeterminada definida en config/database.php
		$resultados = consultaPorMedico($start_date, $end_date);

        return \Maatwebsite\Excel\Facades\Excel::download(new ReporteMedicosExport($resultados), 'ReporteMedicos.xlsx');
    }

	public function dateRangePorMedico(Request $request){
		$dateRange = $request->input('daterange');
	
		list($start_date, $end_date) = explode(' - ', $dateRange);
		$start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
		$end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();
		
		// $date = Carbon::parse($date)->format('Y-m-d');

		// Realiza la consulta utilizando Eloquent
		$data = consultaPorMedico($start_date, $end_date);
		$fecha = date('d-m-Y H:i:s');

		// dd($data[0]);
		return view('reportesgrales/RGViewInfPorMedico', compact('data', 'start_date', 'end_date', 'fecha'));
	}

}
