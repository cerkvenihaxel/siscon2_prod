<?php

use App\Http\Controllers\AdminPedidoMedicamento35Controller;
use App\Http\Controllers\ReporteMedicamentosParticularController;
use App\Http\Controllers\ReportesMedicamentosController;
use App\Http\Controllers\SeguimientoMedicamento\SeguimientoMedicamentoController;
use App\Models\User;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticulosReportController;
use App\Http\Controllers\ProveedoresReportController;
use App\Http\Controllers\MedicosReportController;
use App\Http\Controllers\MedicacionBusquedaController;
use App\Http\Controllers\FullCalendarController;
use App\Http\Controllers\AdjudicacionesController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Http\Controllers\LinPedidoController;
use App\Http\Controllers\EnviarPedidoController;
use App\Http\Controllers\AfiliadoArticuloController;


use App\Http\Controllers\AccidentesReportController;

use App\Http\Controllers\BuscadorAfiliadoConvenioController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\WspPedidosController;
use App\Http\Controllers\ObraSocial\OspladController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    return view('crudbooster::login');

return view('crudbooster::login');

});

Route::get('/comparativa', function () {
    return view('cotizacionesSolicitud2');
});

Route::get('/buscador', function () {
    return view('buscadorApos');
});

Route::get('/asistencia-medico', function () {
    return view('asistenciaMedico');
});


Route::get('/asistencia-salon', function () {
    return view('asistenciaSalon');
});

Route::get('/asistencia-protesis', function () {
    return view('asistenciaProtesis');
});

Route::get('/asistencia-proveedores', function () {
    return view('asistenciaProveedor');
});


Route::get('/informes', function () {
    return view('informeReportes');
});
Route::get('/informesProveedor', function () {
    return view('informeReportesProv');
});

Route::get('/informesMedico', function () {
    return view('informeReportesMed');
});

Route::get('/informesAccidentes', function (){
    return view('informeReportesAccidente');
});



Route::get('/tyc', function () {
    return view('tyc');
});

Route::get('/escritorioAdmin', function(){
    return view('desktopStatistics');
});

Route::post('/dateRange', [ArticulosReportController::class, 'dateRange'])->name('dateRange');
Route::post('/dateRangeProv', [ProveedoresReportController::class, 'dateRangeProv'])->name('dateRangeProv');
Route::post('/dateRangeMed', [MedicosReportController::class, 'dateRangeMed'])->name('dateRangeMed');
Route::post('/dateRangeDeposito', [EnviarPedidoController::class, 'dateRangeDeposito'])->name('dateRangeDeposito');
Route::match(['get', 'post'],'/dateRangeAcc', [\App\Http\Controllers\AccidentesReportController::class, 'dateRangeAcc'])->name('dateRangeAcc');


Route::get('/your-view', function () {
    return view('your-view');
});
Route::get('/proveedorView', function () {
    return view('proveedorViewReport');
});

Route::get('/medicoView', function () {
    return view('medicoViewReport');
});


Route::get('/exportExcel', [ArticulosReportController::class, 'exportExcel'])->name('exportExcel');
//prueba exportar excel completo
Route::post('exportExcelAll', [ArticulosReportController::class, 'exportExcelAll'])->name('exportExcelAll');

Route::get('/exportExcelProv', [ProveedoresReportController::class, 'exportExcelProv'])->name('exportExcelProv');
Route::post('/exportExcelProvAll', [ProveedoresReportController::class, 'exportExcelProvAll'])->name('exportExcelProvAll');

Route::get('/exportExcelMed', [MedicosReportController::class, 'exportExcelMed'])->name('exportExcelMed');
Route::post('/exportExcelMedAll', [MedicosReportController::class, 'exportExcelMedAll'])->name('exportExcelMedAll');

Route::get('/exportExcelAcc', [\App\Http\Controllers\AccidentesReportController::class, 'exportExcelAcc'])->name('exportExcelAcc');
Route::post('/exportExcelAccAll', [\App\Http\Controllers\AccidentesReportController::class, 'exportExcelAccAll'])->name('exportExcelAccAll');

Route::get('/searchFilter', [ArticulosReportController::class, 'searchFilter'])->name('searchFilter');

Route::get('fullcalendar', [FullCalendarController::class, 'index']);
Route::post('fullcalendar/create', [FullCalendarController::class, 'create']);
Route::post('fullcalendar/update', [FullCalendarController::class, 'update']);
Route::post('fullcalendar/delete', [FullCalendarController::class, 'destroy']);
Route::get('/medicacion_requerida/{id}', [MedicacionBusquedaController::class, 'showMeds']);

Route::get('/buscador_medicacion', function(){
    return view('buscadorAfiliadoConvenio');
});

Route::post('/buscador_convenio', [BuscadorAfiliadoConvenioController::class, 'buscarAfiliadoMed'])->name('buscador_convenio');




//Metodo para tener el link de la cotizacion adjudicada

Route::get('admin/cotizadas_adjudicadas/{id}', function (Request $request){
    $id = $request->id;
   $nroSolicitud = DB::table('adjudicaciones')->where('id', $id)->value('nrosolicitud');
   $cotizadaAdjudicada = DB::table('cotizaciones')->where('nrosolicitud', $nroSolicitud)->whereIn('estado_solicitud_id', [3, 6])->value('id');
   Redirect::to('admin/cotizaciones19/detail/'.$cotizadaAdjudicada)->send();

});


//Rutas para envío de deposito
Route::get('admin/linpedido', function (){
    return view('envioPedidoDeposito');
});

Route::get('/linpedido_objeto/{id}', [\App\Http\Controllers\AdminCotizacionConvenioController::class, 'enviarPedidoSingular'])->name('enviarPedidoSingular');


Route::get('admin/validador_farmacia', [\App\Http\Controllers\ValidadorFarmaciaController::class, 'index'])->name('validador_farmacia');
Route::post('admin/validar-afiliado', [\App\Http\Controllers\ValidadorFarmaciaController::class, 'validarAfiliado'])->name('validarAfiliado');
Route::post('/actualizar-datos', [\App\Http\Controllers\ValidadorFarmaciaController::class, 'actualizarDatos'])->name('actualizarDatos');

Route::get('/generarPDF_convenio/{id}', [\App\Http\Controllers\PDFController::class, 'generarPDF'])->name('generarPDF');
Route::get('/imprimirPDF_convenio/{id}', [\App\Http\Controllers\PDFController::class, 'imprimirPDF'])->name('imprimirPDF');
Route::get('/printPDF_convenio/{id}', [\App\Http\Controllers\PDFController::class, 'printPDF'])->name('printPDF');
Route::get('/generarPDF_farmacia/{id}', [\App\Http\Controllers\PDFController::class, 'printFarmaciaPDF'])->name('printFarmaciaPDF');


//ESCRITORIO CONVENIO

/*Route::get('admin/escritorioCM', function (){
    return view('escritorioConvenioMedicamentosView');
});
*/
//ESCRITORIO CONVENIO CONTROLLER

Route::get('admin/escritorioConvenioMedicamentos', [\App\Http\Controllers\EscritorioConvenioMedicamentosController::class, 'deskView'])->name('escritorio_cm');

Route::get('/escritorioConvenioMedicamentos/vermas/{id}', [\App\Http\Controllers\EscritorioConvenioMedicamentosController::class, 'verMas'])->name('escritorio_cm.vermas');

Route::get('/escritorioConvenioMedicamentos/search', [\App\Http\Controllers\EscritorioConvenioMedicamentosController::class, 'searchMedicacion'])->name('escritorio_cm.searchMedicacion');

//--------------------------------------------------------------------------------


//NUEVO CREAR SOLICITUD
Route::get('/crearsolicitud_medico', [\App\Http\Controllers\MedicoCrearPedidoMedicamentoController::class, 'index'])->name('crearsolicitud_medico');

//Busqueda de afiliado
Route::post('/crearsolicitud_medico/buscar', [\App\Http\Controllers\MedicoCrearPedidoMedicamentoController::class, 'buscarAfiliado'])->name('buscarAfiliadoPedido');

Route::get('/crearsolicitud_medico/buscar', [\App\Http\Controllers\MedicoCrearPedidoMedicamentoController::class, 'buscarAfiliado'])->name('buscarAfiliadoPedido');


//Ruta para editar en modal
Route::get('/pm/{id}/edit', [\App\Http\Controllers\MedicoCrearPedidoMedicamentoController::class, 'edit'])->name('pm.edit');

// Actualizar datos
Route::put('/pedidomedicamentoupdate/{id}', [\App\Http\Controllers\MedicoCrearPedidoMedicamentoController::class, 'update'])->name('pm.update');

//Eliminar datos
Route::delete('/pedidomedicamentodelete/{id}', [\App\Http\Controllers\MedicoCrearPedidoMedicamentoController::class, 'destroy'])->name('pm.destroy');

//Guardar los datos en pedido_medicamentos
Route::post('/pedidomedicamentostore', [\App\Http\Controllers\MedicoCrearPedidoMedicamentoController::class, 'guardarPedido'])->name('pm.store');


//AUTORIZAR SOLICITUD (OFICINA DE APOS SECTION)

//-----------------------------------------------------------------

//Index
Route::get('/autorizarsolicitud_oficina', [\App\Http\Controllers\OficinaAutorizarPedidoMedicamentoController::class, 'index'])->name('autorizarsolicitud_oficina');

//Ver pedido Médico
Route::get('/pedido/{id}/detallemedico', [\App\Http\Controllers\OficinaAutorizarPedidoMedicamentoController::class, 'verPedidoMedico'])->name('pedido.detallemedico');

//Ver pedido
Route::get('/pedido/{id}/detalle', [\App\Http\Controllers\OficinaAutorizarPedidoMedicamentoController::class, 'verPedido'])->name('pedido.detalle');

//Autorizar pedido
Route::get('/pedido/{id}/autorizar', [\App\Http\Controllers\OficinaAutorizarPedidoMedicamentoController::class, 'autorizarVerPedido'])->name('pedido.autorizar');

Route::post('/pedido/autorizar/guardar', [\App\Http\Controllers\OficinaAutorizarPedidoMedicamentoController::class, 'autorizarGuardarPedido'])->name('pedido.guardar');

//Rechazar pedido
Route::post('/pedido/rechazar', [\App\Http\Controllers\OficinaAutorizarPedidoMedicamentoController::class, 'rechazarPedido'])->name('pedido.rechazar');

//Auditar pedido
Route::post('/pedido/auditar', [\App\Http\Controllers\OficinaAutorizarPedidoMedicamentoController::class, 'auditarPedido'])->name('pedido.auditar');
//-----------------------------------------------------------------



//NUEVO OFICINA PROVEEDOR (VADA STYLE)
// ----------------------------------------------------------------------------------------
Route::get('/generarpedido_oficina ', [\App\Http\Controllers\ProveedorConvenioOficina::class, 'index'])->name('generarpedido.index');

Route::get('/generarpedido/{id}/detalle', [\App\Http\Controllers\ProveedorConvenioOficina::class, 'verPedido'])->name('generarpedido.detalle');

Route::get('/generarpedido/{id}/detalleprov', [\App\Http\Controllers\ProveedorConvenioOficina::class, 'verPedidoProveedor'])->name('generarpedido.detalleprov');

Route::post('/generarpedido/rechazar', [\App\Http\Controllers\ProveedorConvenioOficina::class, 'rechazarPedido'])->name('generarpedido.rechazar');

Route::get('/generarpedido/{id}/autorizar', [\App\Http\Controllers\ProveedorConvenioOficina::class, 'autorizarVerPedido'])->name('generarpedido.autorizar');


Route::post('/generarpedido/guardar', [\App\Http\Controllers\ProveedorConvenioOficina::class, 'autorizarGuardarPedido'])->name('generarpedido.guardar');

Route::get('/generarpedido/cargamasiva', [\App\Http\Controllers\ProveedorConvenioOficina::class, 'indexCM'])->name('generarpedido.cargamasiva');

Route::post('/enviar-pedido-masivo', [\App\Http\Controllers\ProveedorConvenioOficina::class, 'cargaMasivaProv'])->name('enviar.pedido.masivo');

Route::post('/generarpedido/guardarmasivo', [\App\Http\Controllers\ProveedorConvenioOficina::class, 'autorizarGuardarPedidoMasivo'])->name('generarpedido.guardarmasivo');


//PEDIDO MASIVO 2

Route::group(['prefix' => 'generar-pedido'], function (){

    Route::get('index', [\App\Http\Controllers\Backoffice\GlobalMedica\PedidosConvenio::class, 'index'])->name('generar-pedido.index');
    Route::post('store', [\App\Http\Controllers\Backoffice\GlobalMedica\PedidosConvenio::class, 'store'])->name('generar-pedido.store');
    Route::get('vaciar', [\App\Http\Controllers\Backoffice\GlobalMedica\PedidosConvenio::class, 'vaciar'])->name('generar-pedido.vaciar');
    Route::post('enviar', [\App\Http\Controllers\Backoffice\GlobalMedica\PedidosConvenio::class, 'enviarPedido'])->name('generar-pedido.enviar');
    Route::post('editar-articulo', [\App\Http\Controllers\Backoffice\GlobalMedica\PedidosConvenio::class, 'editarArticulo'])->name('generar-pedido.editar-articulo');
    Route::get('eliminar-articulo/{index}', [\App\Http\Controllers\Backoffice\GlobalMedica\PedidosConvenio::class, 'eliminarArticulo'])->name('generar-pedido.eliminar-articulo');
    Route::get('buscar-articulos', [\App\Http\Controllers\Backoffice\GlobalMedica\PedidosConvenio::class, 'buscarArticulos'])->name('generar-pedido.buscar-articulos');

});

//--------------------------------------------------------------------------------------------------------

//---------- VALIDADOR DE FARMACIAS ------------------



//Entregar pedido (validador farmacia
Route::get('/entregarpedido_farmacia', [\App\Http\Controllers\FarmaciaConvenioController::class, 'index'])->name('entregarpedido_farmacia');

Route::get('/entregarpedido_farmacia/{id}/detalle', [\App\Http\Controllers\FarmaciaConvenioController::class, 'verPedido'])->name('entregarpedido_farmacia.detalle');

Route::post('/entregarpedido_farmacia/rechazar', [\App\Http\Controllers\FarmaciaConvenioController::class, 'rechazarPedido'])->name('entregarpedido_farmacia.rechazar');

Route::get('/entregarpedido_farmacia/{id}/autorizar', [\App\Http\Controllers\FarmaciaConvenioController::class, 'autorizarVerPedido'])->name('entregarpedido_farmacia.autorizar');

Route::post('/entregarpedido_farmacia/guardar', [\App\Http\Controllers\FarmaciaConvenioController::class, 'autorizarGuardarPedido'])->name('entregarpedido_farmacia.guardar');

Route::get('/entregarpedido_farmacia/imprimir/{id}', [\App\Http\Controllers\FarmaciaConvenioController::class, 'printPdf'])->name('entregarpedido_farmacia.imprimir');

// SECCIÓN DE CONTROLLERS-------

// Seccion 1 - MedicoCrearPedidoMedicamento

// Ruta para mostrar la tabla de afiliados_articulos
Route::get('/afiliados_articulos', [\App\Http\Controllers\AfiliadoArticuloController::class, 'index'])->name('afiliados_articulos.index');

// Ruta para almacenar un nuevo registro en afiliados_articulos
Route::post('/afiliados_articulos', [\App\Http\Controllers\AfiliadoArticuloController::class, 'store'])->name('afiliados_articulos.store');

// Ruta para mostrar el formulario de edición de un registro en afiliados_articulos
Route::get('/afiliados_articulos/{id}/edit', [\App\Http\Controllers\AfiliadoArticuloController::class, 'edit'])->name('afiliados_articulos.edit');

// Ruta para actualizar un registro en afiliados_articulos
Route::put('/afiliados_articulos/{id}', [\App\Http\Controllers\AfiliadoArticuloController::class, 'update'])->name('afiliados_articulos.update');

//Ruta para view modal
Route::get('/afiliadosarticulos/{id}', [\App\Http\Controllers\AfiliadoArticuloController::class, 'show'])->name('afiliadosarticulos.show');

//Ruta para editar en modal
Route::get('/afiliadosarticulos/{id}/edit', [\App\Http\Controllers\AfiliadoArticuloController::class, 'edit'])->name('afiliadosarticulos.edit');
//ACTUALIZAR LOS DATOS

Route::put('/afiliadosarticulos/{id}', [AfiliadoArticuloController::class, 'update'])->name('afiliadosarticulos.update');

//Eliminar afiliados_articulos

Route::get('/afiliados_articulos/{id}/delete', [\App\Http\Controllers\AfiliadoArticuloController::class, 'destroy'])->name('afiliados_articulos.destroy');

//Buscador para afiliados_articulos

Route::post('/afiliados_articulos/buscar', [\App\Http\Controllers\AfiliadoArticuloController::class, 'search'])->name('afiliados_articulos.search');
Route::get('/afiliados_articulos/buscar', [\App\Http\Controllers\AfiliadoArticuloController::class, 'search'])->name('afiliados_articulos.search');

//Buscador para el select2 del afiliado

Route::get('/afiliados/search', [AfiliadoArticuloController::class, 'getAfiliados'])->name('afiliados.search');

//Buscador para el select2 del afiliado

Route::get('/articulos/search', [AfiliadoArticuloController::class, 'getArticulos'])->name('articulos.search');

//Guardar Filas

Route::post('/guardar-filas', [AfiliadoArticuloController::class, 'guardarFilas'])->name('guardarfilas');



//BUSQUEDA DE PRUEBA

Route::get('/search', [\App\Http\Controllers\SearchController::class, 'index'])->name('search.index');

Route::get('/searchprueba', function (){
    return view('search');
});

Route::get('/addNewPrecarga', function(){
    return view('addModalPrecarga');
})->name('addNewPrecarga');


//--- SECCION PROTESIS NUEVOS CONVENIOS

Route::get('/protesis/entrantes', function (){
    return view('protesis.entrantes');
})->name('protesis.entrantes');


//-- Sección de reportes generales

Route::get('/reportes_generales', [\App\Http\Controllers\ReportesGenerales::class, 'index'])->name('reportes_generales.index');
// Route::get('/reportes_generales/proveedores', [\App\Http\Controllers\ReportesGenerales::class, 'reporteProveedoresExcel'])->name('reportes_generales.proveedores');
Route::post('/reportes_generales/proveedores', [\App\Http\Controllers\ReportesGenerales::class, 'reporteProveedoresExcel'])->name('reportes_generales.proveedores');
Route::get('/reportes_generales/medicos', [\App\Http\Controllers\ReportesGenerales::class, 'reporteMedicosExcel'])->name('reportes_generales.medicos');
Route::get('/reportes_generales/adj-an-sinadj', [\App\Http\Controllers\ReportesGenerales::class, 'reporteAdjudicadosAnuladosSA'])->name('reportes_generales.adj-an-sinadj');
Route::get('/reportes_generales/sin-cotizar', [\App\Http\Controllers\ReportesGenerales::class, 'reporteSinCotizar'])->name('reportes_generales.sin-cotizar');
Route::get('/reportes_generales/especialidad', [\App\Http\Controllers\ReportesGenerales::class, 'reporteEspecialidad'])->name('reportes_generales.especialidad');
Route::get('/reportes_generales/mes', [\App\Http\Controllers\ReportesGenerales::class, 'reporteMes'])->name('reportes_generales.mes');


//-- Sección de O2 Terapia

Route::get('admin/crear_pedido_o2', [\App\Http\Controllers\O2Terapia\CrearPedido::class, 'newPedido'])->name('newPedido_o2');

Route::post('/reportes_generales/dateRangeInfoProv', [\App\Http\Controllers\ReportesGenerales::class, 'dateRangeInfoProv'])->name('dateRangeInfoProv');

//ADRIAN:
Route::post('/reportes_generales/medicamentos', [\App\Http\Controllers\ReportesGenerales::class, 'reporteMedicamentosExcel'])->name('reportes_generales.medicamentos');
Route::post('/reportes_generales/dateRangeMedicamentos', [\App\Http\Controllers\ReportesGenerales::class, 'dateRangeMedicamentos'])->name('dateRangeMedicamentos');

// Route::get('/reportes_generales/medicos', [\App\Http\Controllers\ReportesGenerales::class, 'reporteMedicosExcel'])->name('reportes_generales.medicos');
Route::post('/reportes_generales/medicos', [\App\Http\Controllers\ReportesGenerales::class, 'reporteMedicosExcel'])->name('reportes_generales.medicos');
Route::post('/reportes_generales/dateRangePorMedico', [\App\Http\Controllers\ReportesGenerales::class, 'dateRangePorMedico'])->name('dateRangePorMedico');


// Route::get('/reportes_generales/adj-an-sinadj', [\App\Http\Controllers\ReportesGenerales::class, 'reporteAdjudicadosAnuladosSA'])->name('reportes_generales.adj-an-sinadj');
Route::post('/reportes_generales/adj-an-sinadj', [\App\Http\Controllers\ReportesGenerales::class, 'reporteAdjudicadosAnuladosSA'])->name('reportes_generales.adj-an-sinadj');
Route::post('/reportes_generales/dateRangeAdjudicadosAnuladosSA', [\App\Http\Controllers\ReportesGenerales::class, 'dateRangeAdjudicadosAnuladosSA'])->name('dateRangeAdjudicadosAnuladosSA');

// Route::get('/reportes_generales/sin-cotizar', [\App\Http\Controllers\ReportesGenerales::class, 'reporteSinCotizar'])->name('reportes_generales.sin-cotizar');
Route::post('/reportes_generales/sin-cotizar', [\App\Http\Controllers\ReportesGenerales::class, 'reporteSinCotizar'])->name('reportes_generales.sin-cotizar');
Route::post('/reportes_generales/dateRangeSinCotizar', [\App\Http\Controllers\ReportesGenerales::class, 'dateRangeSinCotizar'])->name('dateRangeSinCotizar');

// Route::get('/reportes_generales/especialidad', [\App\Http\Controllers\ReportesGenerales::class, 'reporteEspecialidad'])->name('reportes_generales.especialidad');
Route::post('/reportes_generales/especialidad', [\App\Http\Controllers\ReportesGenerales::class, 'reporteEspecialidad'])->name('reportes_generales.especialidad');
Route::post('/reportes_generales/dateRangePorEspecialidad', [\App\Http\Controllers\ReportesGenerales::class, 'dateRangePorEspecialidad'])->name('dateRangePorEspecialidad');

// Route::get('/reportes_generales/mes', [\App\Http\Controllers\ReportesGenerales::class, 'reporteMes'])->name('reportes_generales.mes');
Route::post('/reportes_generales/mes', [\App\Http\Controllers\ReportesGenerales::class, 'reporteMes'])->name('reportes_generales.mes');
Route::post('/reportes_generales/dateRangePorMes', [\App\Http\Controllers\ReportesGenerales::class, 'dateRangePorMes'])->name('dateRangePorMes');


// Reportes grales con gráficos
Route::prefix('reportes_nuevo')->group(function (){
    Route::get('/convenio', function (){
        $id = CRUDBooster::myId();
        $privilegio = User::where('id', $id)->value('id_cms_privileges');

        $nroEntrantes = DB::table('pedido_medicamento')->where('estado_solicitud_id', 1)->count();
        $nroAutorizados = DB::table('pedido_medicamento')->where('estado_solicitud_id', 3)->count();
        $nroRechazados = DB::table('pedido_medicamento')->whereIn('estado_solicitud_id', [5, 9])->count();
        $nroAuditados = DB::table('pedido_medicamento')->where('estado_solicitud_id', 8)->count();

        $nroAsignados = DB::table('convenio_oficina_os')->where('proveedor', 2)->count();
        $nroProcesados = DB::table('cotizacion_convenio')->where('proveedor', 'LIKE', 'Global Médica')->count();
        $nroEntregados = DB::table('cotizacion_convenio')->where('proveedor', 'LIKE', 'Global Médica')->where('estado_pedido_id', 1)->count();
        $nroRechazadosGlobal = DB::table('cotizacion_convenio')->where('proveedor', 'LIKE', 'Global Médica')->whereIn('estado_solicitud_id', [10, 5])->count();

        $patologiasName = DB::table('patologias')->get();
        return view ('reports_graphs.convenio', compact('nroEntrantes', 'nroAutorizados', 'nroRechazados', 'privilegio','nroAsignados', 'nroAuditados','nroProcesados', 'nroRechazadosGlobal','nroEntregados', 'patologiasName'));
    });
});

Route::get('/reportes-medicamentos', [ReportesMedicamentosController::class, 'obtenerReportes'])->name('reportes.obtener');
Route::get('/exportar-excel', [ReportesMedicamentosController::class, 'exportarExcel'])->name('reportes.exportar');

Route::get('/reportes-medicamentos-particular', [ReporteMedicamentosParticularController::class, 'obtenerReportes'])->name('reportes.obtener.particular');
Route::get('/exportar-excel-particular', [ReporteMedicamentosParticularController::class, 'exportarExcel'])->name('reportes.exportar.particular');

Route::post('/seguimiento-medicamentos', [SeguimientoMedicamentoController::class, 'index'])->name('seguimiento.obtener');
Route::get('/seguimientos-medicamentos', function (){
    return view('seguimientoMedicamentos.seguimientoMedicamento');
});

// Manual de Usuario - Flujo UP
Route::get('/admin/manual-flujo-up', 'App\Http\Controllers\ManualFlujoUpController@getIndex');

// Configuración de Ambiente UP
Route::get('/admin/up_ambiente', 'App\Http\Controllers\UpAmbienteController@getIndex');
Route::post('/admin/up_ambiente/cambiar-ambiente', 'App\Http\Controllers\UpAmbienteController@postCambiarAmbiente');
Route::get('/admin/up_ambiente/crear-datos-prueba', 'App\Http\Controllers\UpAmbienteController@getCrearDatosPrueba');
Route::get('/admin/up_ambiente/limpiar-datos-prueba', 'App\Http\Controllers\UpAmbienteController@getLimpiarDatosPrueba');

// Vista Personalizada Consumos UP V2
Route::get('/admin/consumos_up_v2', 'App\Http\Controllers\ConsumosUpV2Controller@index');
Route::get('/admin/consumos_up_v2/{id}', 'App\Http\Controllers\ConsumosUpV2Controller@show');
Route::get('/admin/consumos_up_v2/{id}/elegibilidad', 'App\Http\Controllers\ConsumosUpV2Controller@consultarElegibilidad');
Route::post('/admin/consumos_up_v2/{id}/elegibilidad', 'App\Http\Controllers\ConsumosUpV2Controller@actualizarElegibilidad');
Route::post('/admin/consumos_up_v2/{id}/aprobar', 'App\Http\Controllers\ConsumosUpV2Controller@aprobarPrestacion');
Route::post('/admin/consumos_up_v2/{id}/aprobar-elegibilidad', 'App\Http\Controllers\ConsumosUpV2Controller@aprobarConElegibilidad');
Route::post('/admin/consumos_up_v2/{id}/aprobar-directo', 'App\Http\Controllers\ConsumosUpV2Controller@aprobarDirecto');
Route::post('/admin/consumos_up_v2/{id}/validar', 'App\Http\Controllers\ConsumosUpV2Controller@generarValidacion');
Route::get('/admin/consumos_up_v2/{id}/validacion', 'App\Http\Controllers\ConsumosUpV2Controller@mostrarValidacion');
Route::post('/admin/consumos_up_v2/{id}/procesar-validacion', 'App\Http\Controllers\ConsumosUpV2Controller@procesarValidacion');
Route::get('/admin/consumos_up_v2/entregas', 'App\Http\Controllers\ConsumosUpV2Controller@mostrarEntregas');

// Consumos UP - Nueva Vista
Route::middleware(['crocodicstudio\crudbooster\middlewares\CBBackend', 'farmacias.up'])->group(function () {
    Route::get('/admin/consumos-up', 'App\Http\Controllers\ConsumosUpController@index');
    Route::get('/admin/consumos-up/{id}/imprimir', 'App\Http\Controllers\ConsumosUpController@imprimir');
    Route::get('/admin/consumos-up/{id}/xml', 'App\Http\Controllers\ConsumosUpController@getXML');
    Route::get('/admin/consumos-up/{id}', 'App\Http\Controllers\ConsumosUpController@show');
    Route::post('/admin/consumos-up/marcar-entregado', 'App\Http\Controllers\ConsumosUpController@marcarEntregado');
    Route::post('/admin/consumos-up/anular', 'App\Http\Controllers\ConsumosUpController@anular');
});

// Entregas UP
Route::middleware(['crocodicstudio\crudbooster\middlewares\CBBackend', 'farmacias.up'])->group(function () {
    Route::get('/admin/entregas-up', 'App\Http\Controllers\EntregasUpController@index');
    Route::get('/admin/entregas-up/add/{consumo_id}', 'App\Http\Controllers\EntregasUpController@add');
    Route::get('/admin/entregas-up/imprimir-formulario/{consumo_id}', 'App\Http\Controllers\EntregasUpController@imprimirFormulario');
    Route::post('/admin/entregas-up/store', 'App\Http\Controllers\EntregasUpController@store');
    Route::get('/admin/entregas-up/{id}/consentimiento', 'App\Http\Controllers\EntregasUpController@consentimiento');
    Route::post('/admin/entregas-up/{id}/generar-pdf', 'App\Http\Controllers\EntregasUpController@generarPDF');
});

// UP Ambiente Controller
Route::middleware(['crocodicstudio\crudbooster\middlewares\CBBackend'])->group(function () {
    Route::post('/admin/up_ambiente/cambiar-ambiente', 'App\Http\Controllers\UpAmbienteController@cambiarAmbiente');
    Route::get('/admin/up_ambiente/ambiente-actual', 'App\Http\Controllers\UpAmbienteController@getAmbienteActual');
});

// Transacción AP (Consumo de prestaciones)
Route::middleware(['crocodicstudio\crudbooster\middlewares\CBBackend', 'farmacias.up'])->group(function () {
    Route::get('/admin/transaccion-ap', 'App\Http\Controllers\TransaccionApController@index');
    Route::post('/transaccion-ap/elegibilidad', 'App\Http\Controllers\TransaccionApController@consultarElegibilidad');
    Route::post('/transaccion-ap/procesar', 'App\Http\Controllers\TransaccionApController@procesarTransaccionAp');
    Route::post('/transaccion-ap/procesar-multiples', 'App\Http\Controllers\TransaccionApController@procesarMultiplesAP');
    Route::get('/transaccion-ap/buscar-articulos', 'App\Http\Controllers\TransaccionApController@buscarArticulos');
    Route::post('/transaccion-ap/validar-medicamento', 'App\Http\Controllers\TransaccionApController@validarMedicamento');
    Route::post('/transaccion-ap/guardar-consumo', 'App\Http\Controllers\TransaccionApController@guardarConsumo');
    Route::get('/transaccion-ap/ticket-rechazo', 'App\Http\Controllers\TransaccionApController@imprimirTicketRechazo');
    Route::get('/admin/transaccion-ap/{idtran}/xml', 'App\Http\Controllers\TransaccionApController@getXML');
});

// Anulación UP (ATR)
Route::middleware(['crocodicstudio\crudbooster\middlewares\CBBackend', 'farmacias.up'])->group(function () {
    Route::get('/admin/anulacion-up', 'App\Http\Controllers\AnulacionUpController@index')->name('anulacion-up.index');
    Route::post('/anulacion-up/procesar', 'App\Http\Controllers\AnulacionUpController@procesarAnulacion')->name('anulacion-up.procesar');
    Route::get('/anulacion-up/xml/{idtran}', 'App\Http\Controllers\AnulacionUpController@getXML');
});

// Anulaciones UP (Listado)
Route::middleware(['crocodicstudio\crudbooster\middlewares\CBBackend', 'farmacias.up'])->group(function () {
    Route::get('/admin/anulaciones-up', 'App\Http\Controllers\AnulacionesUpController@index');
    Route::get('/admin/anulaciones-up/detalle/{id}', 'App\Http\Controllers\AnulacionesUpController@detalle');
    Route::get('/admin/anulaciones-up/imprimir/{id}', 'App\Http\Controllers\AnulacionesUpController@imprimir');
    Route::get('/admin/anulaciones-up/xml/{id}', 'App\Http\Controllers\AnulacionesUpController@verXml');
});

// Flujo Obras Sociales — OSPLAD (drogueria.osplad_consumos), escalable multi-OS
// PENDIENTES -> En tránsito -> Entregas. Filtra por farmacia (id_cliente) según rol.
Route::middleware(['crocodicstudio\crudbooster\middlewares\CBBackend', 'obra.social'])->group(function () {
    Route::get('/admin/osplad', function () { return redirect('/admin/osplad/pendientes'); });
    Route::get('/admin/osplad/pendientes', [OspladController::class, 'pendientes']);
    Route::get('/admin/osplad/transito',   [OspladController::class, 'transito']);
    Route::get('/admin/osplad/entregas',   [OspladController::class, 'entregas']);
    // Entrega unificada (varios pedidos, mismo afiliado + mismo remito) + consentimiento
    Route::post('/admin/osplad/entregar',       [OspladController::class, 'entregar']);
    Route::get('/admin/osplad/consentimiento',  [OspladController::class, 'consentimiento']);
    // Reporte Excel de entregas (solo admin): semana | mes | rango
    Route::get('/admin/osplad/export/{periodo}', [OspladController::class, 'exportarExcel'])
        ->where('periodo', 'semana|mes|rango');
    // Ver / imprimir detalle de un consumo (debe ir último por el comodín {id})
    Route::get('/admin/osplad/{id}/detalle', [OspladController::class, 'detalle'])->whereNumber('id');
});

// Pedidos WhatsApp Agent
Route::middleware(['crocodicstudio\crudbooster\middlewares\CBBackend', 'wsp.pedidos'])->group(function () {
    Route::get('/wsp/pedidos',                     [WspPedidosController::class, 'index'])->name('wsp.pedidos.index');
    Route::get('/wsp/pedidos/{pedidoId}/detalle',  [WspPedidosController::class, 'show'])->name('wsp.pedidos.show');
    Route::post('/wsp/pedidos/{pedidoId}/estado',  [WspPedidosController::class, 'actualizarEstado'])->name('wsp.pedidos.estado');
    Route::post('/wsp/pedidos/{pedidoId}/notas',   [WspPedidosController::class, 'actualizarNotas'])->name('wsp.pedidos.notas');
});

// Tracking de envíos — Droguería Global Médica (DB remota)
Route::middleware(['crocodicstudio\crudbooster\middlewares\CBBackend'])->group(function () {
    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
    Route::get('/api/tracking/envios', [TrackingController::class, 'apiEnvios'])->name('tracking.api');
});

// Pasarela de pagos
Route::get('/pagar/{pagoId}', [PagoController::class, 'show'])->name('pagos.show');
Route::get('/confirmar/{pagoId}', [PagoController::class, 'confirmar'])->name('pagos.confirmar');
Route::post('/api/pagos/crear', [PagoController::class, 'crear'])->name('pagos.crear');
Route::get('/api/pagos/estado/{pagoId}', [PagoController::class, 'estado'])->name('pagos.estado');

// Redirección automática para usuarios Farmacias UP
Route::middleware(['crocodicstudio\crudbooster\middlewares\CBBackend'])->group(function () {
    Route::get('/admin', function() {
        // Usuarios de farmacia: redirigir a su módulo correspondiente
        if (CRUDBooster::myPrivilegeName() == 'Farmacias OSPLAD') {
            return redirect('/admin/osplad/pendientes');
        }
        // Si es usuario "Farmacias UP", redirigir a transaccion-ap
        if (CRUDBooster::myPrivilegeName() == 'Farmacias UP') {
            return redirect('/admin/transaccion-ap');
        }
        // Para otros usuarios, mostrar dashboard normal
        return redirect('/admin/statistic_builder/dashboard');
    });
});
