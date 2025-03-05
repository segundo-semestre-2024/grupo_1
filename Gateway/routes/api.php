<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GatewayController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas por Sanctum y con control de roles
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::middleware(['role:admin'])->group(function () {
        Route::post('/', [GatewayController::class, 'enviarMensaje']);
        Route::get('/list-user', [AuthController::class, 'listarUsuarios']);
    });

    Route::middleware(['role:user'])->group(function () {
        Route::post('/prediction', [GatewayController::class, 'prediction']);
    });

    // Nueva Ruta para el Análisis de Sentimientos
    Route::post('/analyze', function (Request $request) {
        // Validar que el texto venga en la petición
        $request->validate([
            'text' => 'required|string'
        ]);

        // Enviar la petición a Flask
        $response = Http::post('http://flask_microservice:5000/analyze', [
            'text' => $request->input('text')
        ]);

        // Devolver la respuesta de Flask al frontend
        return $response->json();
    });
});
