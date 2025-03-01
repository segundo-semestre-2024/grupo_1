<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GatewayController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
  
// Rutas protegidas por Sanctum y con control de roles
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
//Notificaciones
Route::middleware(['role:Administrador'])->group(function () {
    Route::post('/notificaciones', [GatewayController::class, 'enviarMensaje']);
    Route::get('/list-user', [AuthController::class, 'listarUsuarios']);
});
    //Reportes
    
Route::middleware(['role:User'])->group(function () {
      
    Route::post('/prediction', [GatewayController::class, 'prediction']);
    });
});