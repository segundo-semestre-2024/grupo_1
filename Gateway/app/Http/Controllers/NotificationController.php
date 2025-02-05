<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    private $apiUrl;
    private $apiKey;

    public function __construct()
    {
        // URL del microservicio de notificaciones
        $this->apiUrl = env('MICROSERVICIO_NOT_URL');
        $this->apiKey = env('X_API_KEY');
    }

    // Reenviar petición de SMS a un número específico
    public function enviarSms(Request $request)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}"
        ])->post("{$this->apiUrl}/send-sms", [
            'to' => $request->input('to'),
            'message' => $request->input('message'),
        ]);

        return response()->json($response->json(), $response->status());
    }

    // Reenviar petición de SMS a todos los usuarios registrados
    public function enviarSmsTodos()
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}"
        ])->post("{$this->apiUrl}/send-sms-all");

        return response()->json($response->json(), $response->status());
    }

    // Reenviar petición de envío de correo
    public function sendEmail(Request $request)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}"
        ])->post("{$this->apiUrl}/send-email", [
            'to' => $request->input('to'),
            'subject' => $request->input('subject'),
            'body' => $request->input('body'),
        ]);

        return response()->json($response->json(), $response->status());
    }
}
