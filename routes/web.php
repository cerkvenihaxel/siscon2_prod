<?php

use App\Http\Controllers\AdminPedidoMedicamento35Controller;
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

// Route::get('/search', [\App\Http\Controllers\SearchController::class, 'index'])->name('search.index');

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

// Rutas para Oxigenoterapia
Route::prefix('admin/oxigenoterapia')->group(function () {
    Route::get('/get-afiliado/{id}', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'getAfiliado']);
    Route::get('/get-medico/{id}', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'getMedico']);
    Route::get('/get-equipo/{id}', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'getEquipo']);
    Route::get('/autorizar/{id}', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'autorizar']);
    Route::get('/prestamo/{id}', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'realizarPrestamo']);
    Route::get('/ver-prestamo/{id}', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'verPrestamo']);
    Route::get('/renovar/{id}', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'renovar']);
    Route::get('/finalizar/{id}', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'finalizar']);
    Route::get('/imprimir/{id}', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'imprimirDocumentos']);
    Route::get('/ver-documento/{id}', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'verDocumento']);
    Route::get('/materiales/{id}', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'materiales']);
    Route::post('/prestamo/store', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'storePrestamo'])->name('oxigenoterapia.prestamo.store');
});

// Rutas para documentos PDF
Route::get('/documentos/consentimiento/{id}', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'generarConsentimiento'])->name('oxigenoterapia.documentos.consentimiento');
Route::get('/documentos/terminos/{id}', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'generarTerminos'])->name('oxigenoterapia.documentos.terminos');
Route::get('/documentos/contrato/{id}', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'generarContrato'])->name('oxigenoterapia.documentos.contrato');
Route::get('/documentos/instrucciones/{id}', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'generarInstrucciones'])->name('oxigenoterapia.documentos.instrucciones');
Route::get('/documentos/checklist/{id}', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'generarChecklist'])->name('oxigenoterapia.documentos.checklist');
Route::get('/documentos/resumen/{id}', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'generarResumen'])->name('oxigenoterapia.documentos.resumen');
Route::get('/equipos-disponibles', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'getEquiposDisponibles']);
Route::get('/estadisticas', [\App\Http\Controllers\AdminOxigenoterapiaController::class, 'estadisticas']);

// Rutas para Préstamos de Oxigenoterapia
Route::prefix('admin/prestamo-oxigenoterapia')->group(function () {
    Route::get('/ver-prestamo/{id}', [\App\Http\Controllers\AdminPrestamoOxigenoterapiaController::class, 'verPrestamo']);
    Route::get('/renovar/{id}', [\App\Http\Controllers\AdminPrestamoOxigenoterapiaController::class, 'renovar']);
    Route::post('/renovar/{id}', [\App\Http\Controllers\AdminPrestamoOxigenoterapiaController::class, 'procesarRenovacion']);
    Route::get('/finalizar/{id}', [\App\Http\Controllers\AdminPrestamoOxigenoterapiaController::class, 'finalizar']);
    Route::post('/finalizar/{id}', [\App\Http\Controllers\AdminPrestamoOxigenoterapiaController::class, 'procesarFinalizacion']);
    Route::get('/documentos/{id}', [\App\Http\Controllers\AdminPrestamoOxigenoterapiaController::class, 'documentos']);
    Route::post('/documentos/{id}/subir', [\App\Http\Controllers\AdminPrestamoOxigenoterapiaController::class, 'subirDocumento']);
    Route::get('/documentos/descargar/{documentoId}', [\App\Http\Controllers\AdminPrestamoOxigenoterapiaController::class, 'descargarDocumento'])->name('documentos.descargar');
    Route::get('/documentos/ver/{documentoId}', [\App\Http\Controllers\AdminPrestamoOxigenoterapiaController::class, 'verDocumento'])->name('documentos.ver');
    Route::delete('/documentos/{documentoId}', [\App\Http\Controllers\AdminPrestamoOxigenoterapiaController::class, 'eliminarDocumento']);
});

// Rutas para Préstamos
Route::prefix('prestamo')->group(function () {
    Route::post('/store', [\App\Http\Controllers\PrestamoOxigenoterapiaController::class, 'store'])->name('prestamo.store');
    Route::post('/entregar/{id}', [\App\Http\Controllers\PrestamoOxigenoterapiaController::class, 'entregar'])->name('prestamo.entregar');
    Route::post('/renovar/{id}', [\App\Http\Controllers\PrestamoOxigenoterapiaController::class, 'renovar'])->name('prestamo.renovar');
    Route::post('/finalizar/{id}', [\App\Http\Controllers\PrestamoOxigenoterapiaController::class, 'finalizar'])->name('prestamo.finalizar');
    Route::post('/firmar-documento/{id}', [\App\Http\Controllers\PrestamoOxigenoterapiaController::class, 'firmarDocumento'])->name('prestamo.firmar-documento');
    Route::get('/ver-documento/{id}', [\App\Http\Controllers\PrestamoOxigenoterapiaController::class, 'verDocumento'])->name('prestamo.ver-documento');
    Route::post('/generar-documento/{id}', [\App\Http\Controllers\PrestamoOxigenoterapiaController::class, 'generarDocumento'])->name('prestamo.generar-documento');
});

// Rutas para Equipos de Oxigenoterapia
Route::prefix('admin/equipos-oxigenoterapia')->group(function () {
    Route::get('/prestamos/{id}', [\App\Http\Controllers\AdminEquiposOxigenoterapiaController::class, 'prestamos']);
    Route::get('/mantenimiento/{id}', [\App\Http\Controllers\AdminEquiposOxigenoterapiaController::class, 'mantenimiento']);
    Route::post('/registrar-mantenimiento/{id}', [\App\Http\Controllers\AdminEquiposOxigenoterapiaController::class, 'registrarMantenimiento']);
    Route::get('/historial/{id}', [\App\Http\Controllers\AdminEquiposOxigenoterapiaController::class, 'historial']);
});

// Rutas para Cliente (Portal del Cliente) - Comentadas hasta implementar controladores
/*
Route::prefix('cliente/oxigenoterapia')->group(function () {
    Route::get('/mis-pedidos', 'ClienteOxigenoterapiaController@misPedidos')->name('cliente.mis-pedidos');
    Route::get('/pedido/{id}', 'ClienteOxigenoterapiaController@verPedido')->name('cliente.ver-pedido');
    Route::get('/prestamo/{id}', 'ClienteOxigenoterapiaController@verPrestamo')->name('cliente.ver-prestamo');
    Route::post('/firmar-documento/{id}', 'ClienteOxigenoterapiaController@firmarDocumento')->name('cliente.firmar-documento');
    Route::get('/documentos/{id}', 'ClienteOxigenoterapiaController@documentos')->name('cliente.documentos');
});
*/

// Rutas para API (si se necesita) - Comentadas hasta implementar controladores
/*
Route::prefix('api/oxigenoterapia')->group(function () {
    Route::get('/pedidos', 'ApiOxigenoterapiaController@pedidos');
    Route::get('/prestamos', 'ApiOxigenoterapiaController@prestamos');
    Route::get('/equipos', 'ApiOxigenoterapiaController@equipos');
    Route::post('/notificar-vencimiento', 'ApiOxigenoterapiaController@notificarVencimiento');
});
*/
