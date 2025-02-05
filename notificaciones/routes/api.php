<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\NotificationController;
use App\Notifications\TestNotification;

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

Route::middleware(['Filter'])->group(function () {
    Route::get('/send-sms', [NotificationController::class, 'enviarSms']);
    Route::get('/send-sms-all', [NotificationController::class, 'enviarSmsTodos']);
    Route::get('/send-email', [NotificationController::class, 'sendEmail']);
});



