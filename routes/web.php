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

//Ver pedido
Route::get('/pedido/{id}/detalle', [\App\Http\Controllers\OficinaAutorizarPedidoMedicamentoController::class, 'verPedido'])->name('pedido.detalle');

//Autorizar pedido
Route::get('/pedido/{id}/autorizar', [\App\Http\Controllers\OficinaAutorizarPedidoMedicamentoController::class, 'autorizarVerPedido'])->name('pedido.autorizar');

Route::post('/pedido/autorizar/guardar', [\App\Http\Controllers\OficinaAutorizarPedidoMedicamentoController::class, 'autorizarGuardarPedido'])->name('pedido.guardar');

//Rechazar pedido
Route::post('/pedido/rechazar', [\App\Http\Controllers\OficinaAutorizarPedidoMedicamentoController::class, 'rechazarPedido'])->name('pedido.rechazar');
//-----------------------------------------------------------------



//NUEVO OFICINA PROVEEDOR (VADA STYLE)
// ----------------------------------------------------------------------------------------
Route::get('/generarpedido_oficina ', [\App\Http\Controllers\ProveedorConvenioOficina::class, 'index'])->name('generarpedido.index');

Route::get('/generarpedido/{id}/detalle', [\App\Http\Controllers\ProveedorConvenioOficina::class, 'verPedido'])->name('generarpedido.detalle');

Route::get('/generarpedido/{id}/detalleprov', [\App\Http\Controllers\ProveedorConvenioOficina::class, 'verPedidoProveedor'])->name('generarpedido.detalleprov');

Route::post('/generarpedido/rechazar', [\App\Http\Controllers\ProveedorConvenioOficina::class, 'rechazarPedido'])->name('generarpedido.rechazar');

Route::get('/generarpedido/{id}/autorizar', [\App\Http\Controllers\ProveedorConvenioOficina::class, 'autorizarVerPedido'])->name('generarpedido.autorizar');


Route::post('/generarpedido/guardar', [\App\Http\Controllers\ProveedorConvenioOficina::class, 'autorizarGuardarPedido'])->name('generarpedido.guardar');


//--------------------------------------------------------------------------------------------------------

//---------- VALIDADOR DE FARMACIAS ------------------



//Entregar pedido (validador farmacia
Route::get('/entregarpedido_farmacia', [\App\Http\Controllers\FarmaciaConvenioController::class, 'index'])->name('entregarpedido_farmacia');

Route::get('/entregarpedido_farmacia/{id}/detalle', [\App\Http\Controllers\FarmaciaConvenioController::class, 'verPedido'])->name('entregarpedido_farmacia.detalle');

Route::post('/entregarpedido_farmacia/rechazar', [\App\Http\Controllers\FarmaciaConvenioController::class, 'rechazarPedido'])->name('entregarpedido_farmacia.rechazar');

Route::get('/entregarpedido_farmacia/{id}/autorizar', [\App\Http\Controllers\FarmaciaConvenioController::class, 'autorizarVerPedido'])->name('entregarpedido_farmacia.autorizar');

Route::post('/entregarpedido_farmacia/guardar', [\App\Http\Controllers\FarmaciaConvenioController::class, 'autorizarGuardarPedido'])->name('entregarpedido_farmacia.guardar');


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
