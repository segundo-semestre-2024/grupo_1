<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GatewayController extends Controller
{
    private $comment;
    private $notificacionesUrl;
    private $apiKey;

    public function __construct()
    {
        // Leer las URLs de los microservicios desde el .env
        $this->comment = env('MICROSERVICIO_FLASK');
        $this->notificacionesUrl = env('MICROSERVICIO_NOT_URL');
        $this->apiKey = env('API_KEY');
        
    }

    /* // 🔹 Redirigir la petición al microservicio de prediccion*/

    public function prediction(Request $request)
    {
        $url = $this->comment . '/prediction'; // Ruta del microservicio
        $data = $request->all(); // Datos recibidos en la solicitud

        // Hacer la solicitud POST al microservicio de detección
        $response = Http::withHeaders([
            'X-API-KEY' => $this->apiKey, // Agregar la clave API en el encabezado
        ])->post($url, $data);

        // Retornar la respuesta del microservicio
        return $response->json();
    }


    // 🔹 Redirigir la petición al microservicio de notificaciones
    /* public function enviarMensaje(Request $request)
    {
        $url = $this->notificacionesUrl . "/enviar_mensaje";
        $response = Http::post($url, $request->all());

        return response()->json($response->json(), $response->status());
    } */
    public function enviarMensaje(Request $request)
    {
        $url = $this->notificacionesUrl . "/send-sms"; // URL del microservicio de notificaciones
    
        // Enviar la solicitud POST con la API Key en los encabezados
        $response = Http::withHeaders([
            'X-API-KEY' => $this->apiKey, // Agregar la clave API en el encabezado
        ])->post($url, $request->all());
    
        // Retornar la respuesta del microservicio de notificaciones
        return response()->json($response->json(), $response->status());
    }
    
}
