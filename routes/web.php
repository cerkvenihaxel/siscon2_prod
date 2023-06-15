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



//NUEVO CREAR SOLICITUD
Route::get('/crearsolicitud_medico', function (){
    return view('medicoCrearPedidoMedicamentoview');
});

//NUEVO PEDIDO MEDICO (VADA STYLE)

Route::get('/autorizarsolicitud_oficina', function (){
    return view('oficinaAutorizarPedidoMedicamentoView');
});

//NUEVO OFICINA PROVEEDOR (VADA STYLE)

Route::get('/generarpedido_oficina', function (){
    return view('oficinaProveedorPedidoMedicamentoView');
});

//Entregar pedido (validador farmacia)
Route::get('/entregarpedido_farmacia', function (){
    return view('farmaciaPedidoMedicamentoView');
});

// SECCIÓN DE CONTROLLERS-------

// Seccion 1 - MedicoCrearPedidoMedicamento

//Busqueda

Route::post('/crearsolicitud_medico/buscar', [\App\Http\Controllers\MedicoCrearPedidoMedicamentoController::class, 'buscarAfiliado'])->name('buscarAfiliadoPedido');



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

Route::match(['GET', 'POST', 'DELETE'],'/afiliados_articulos/{id}/delete', [\App\Http\Controllers\AfiliadoArticuloController::class, 'destroy'])->name('afiliados_articulos.destroy');

//Buscador para afiliados_articulos

Route::post('/afiliados_articulos/buscar', [\App\Http\Controllers\AfiliadoArticuloController::class, 'search'])->name('afiliados_articulos.search');

//Buscador para el select2 del afiliado

Route::get('/afiliados/search', [AfiliadoArticuloController::class, 'getAfiliados'])->name('afiliados.search');

//Guardar Filas

Route::post('/guardar-filas', [AfiliadoArticuloController::class, 'guardarFilas'])->name('guardarfilas');
