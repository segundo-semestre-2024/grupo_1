<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FlaskController;

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

Route::get('/send-sms', [NotificationController::class, 'enviarSms']);
Route::get('/send-sms-all', [NotificationController::class, 'enviarSmsTodos']);

Route::post('/prediction', [FlaskController::class, 'prediction']);