<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticulosReportController;
use App\Http\Controllers\ProveedoresReportController;
use App\Http\Controllers\MedicosReportController;
use App\Http\Controllers\MedicacionBusquedaController;
use App\Http\Controllers\FullCalendarController;
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


Route::get('/tyc', function () {
    return view('tyc');
});

Route::get('/escritorioAdmin', function(){
    return view('desktopStatistics');
});

Route::post('/dateRange', [ArticulosReportController::class, 'dateRange'])->name('dateRange');
Route::post('/dateRangeProv', [ProveedoresReportController::class, 'dateRangeProv'])->name('dateRangeProv');
Route::post('/dateRangeMed', [MedicosReportController::class, 'dateRangeMed'])->name('dateRangeMed');


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