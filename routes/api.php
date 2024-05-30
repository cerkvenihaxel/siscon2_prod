<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group( function (){
    // Route::get()
});


Route::get('/solicitudes_protesis', [\App\Http\Controllers\ApiDepositoController::class, 'solicitudesProtesis']);
Route::get('/obtenerpedidos', [\App\Http\Controllers\ApiPedidoController::class, 'obtenerPedidos'])->name('obtenerPedidos');

Route::post('/afiliado_api', [\App\Http\Controllers\API\AfiliadosAPI::class, 'searchAfiliateByDNI'])->name('searchAfiliateByDNI');
Route::post('/afiliado_api_id', [\App\Http\Controllers\API\AfiliadosAPI::class, 'searchAfiliateByID'])->name('searchAfiliateByID');
Route::get('/articulos_oxigeno', [\App\Http\Controllers\API\ArticulosO2Terapia::class, 'getArticles']);
