<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticulosReportController;
use App\Http\Controllers\FullCalendarController;

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
//<<<<<<< HEAD
    return view('crudbooster::login');
//=======
return view('crudbooster::login');
    
//>>>>>>> 6d0e1d8c3836d65dfd799117255f7a9325487202
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

Route::get('/tyc', function () {
    return view('tyc');
});

Route::get('/form', function () {
    return view('datePicker');
});

Route::post('/dateRange', [ArticulosReportController::class, 'dateRange'])->name('dateRange');

Route::get('/your-view', function () {
    return view('your-view');
});

Route::get('/exportExcel', [ArticulosReportController::class, 'exportExcel'])->name('exportExcel');

//prueba exportar excel completo

Route::post('exportExcelAll', [ArticulosReportController::class, 'exportExcelAll'])->name('exportExcelAll');

Route::get('/searchFilter', [ArticulosReportController::class, 'searchFilter'])->name('searchFilter');

Route::get('fullcalendar', [FullCalendarController::class, 'index']);
Route::post('fullcalendar/create', [FullCalendarController::class, 'create']);
Route::post('fullcalendar/update', [FullCalendarController::class, 'update']);
Route::post('fullcalendar/delete', [FullCalendarController::class, 'destroy']);