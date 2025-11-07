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

// UP Consumos API - Open access for external systems
Route::prefix('up-consumos')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\UpConsumosApiController::class, 'index']);
    Route::get('/stats', [\App\Http\Controllers\Api\UpConsumosApiController::class, 'stats']);
    Route::get('/{id}', [\App\Http\Controllers\Api\UpConsumosApiController::class, 'show']);
    Route::put('/{id}/observaciones', [\App\Http\Controllers\Api\UpConsumosApiController::class, 'updateObservations']);
});
Route::get('/articulos_oxigeno', [\App\Http\Controllers\API\ArticulosO2Terapia::class, 'getArticles']);

/*
|--------------------------------------------------------------------------
| Rutas API - Unión Personal SOAP
|--------------------------------------------------------------------------
| Endpoints para integración con servicios SOAP de Unión Personal
| Protocolo: CA_V20
| 🌟 La operación ELG autocrea afiliados en afiliados_convenio_up
|
*/
Route::prefix('union-personal')->group(function () {
    // Test de conexión
    Route::get('/test', [\App\Http\Controllers\API\UnionPersonalAPIController::class, 'test']);

    // Operaciones SOAP
    Route::post('/elegibilidad', [\App\Http\Controllers\API\UnionPersonalAPIController::class, 'elegibilidad']);
    Route::post('/autorizar-prestacion', [\App\Http\Controllers\API\UnionPersonalAPIController::class, 'autorizarPrestacion']);
    Route::post('/anular-transaccion', [\App\Http\Controllers\API\UnionPersonalAPIController::class, 'anularTransaccion']);

    // Búsqueda de afiliado (desde BD local)
    Route::get('/buscar-afiliado/{codigo}', [\App\Http\Controllers\API\UnionPersonalAPIController::class, 'buscarAfiliado']);

    // Flujo completo (ELG + AP)
    Route::post('/flujo-completo', [\App\Http\Controllers\API\UnionPersonalAPIController::class, 'flujoCompleto']);
});

// Alias para compatibilidad con manual de pruebas
Route::get('/soap/test', [\App\Http\Controllers\API\UnionPersonalAPIController::class, 'test']);
Route::post('/soap/elegibilidad', [\App\Http\Controllers\API\UnionPersonalAPIController::class, 'elegibilidad']);
Route::post('/soap/autorizar-prestacion', [\App\Http\Controllers\API\UnionPersonalAPIController::class, 'autorizarPrestacion']);
Route::post('/soap/anular-transaccion', [\App\Http\Controllers\API\UnionPersonalAPIController::class, 'anularTransaccion']);
