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
