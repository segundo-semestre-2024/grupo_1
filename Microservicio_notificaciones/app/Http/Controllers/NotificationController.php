<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\Register;
use Illuminate\Support\Facades\DB;
class NotificationController extends Controller
{
    public function enviar(Request $request)
    {
        try {
            $sid = env('TWILIO_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $twilio = new Client($sid, $token);

            $destino = $request->input('destino');  // Número de destino
            $mensaje = $request->input('mensaje');  // Mensaje dinámico

            // Enviar mensaje de texto
            $twilio->messages->create(
                $destino,
                [
                    'from' => env('TWILIO_PHONE_NUMBER'),
                    'body' => $mensaje
                ]
            );

            // Guardar registro en la base de datos
            // Guardar el registro usando el modelo
            Register::create([
                'destino' => $destino,
                'mensaje' => $mensaje,
                'estado' => 'enviado',
            ]);

            return response()->json(['message' => 'Mensaje enviado correctamente'], 200);
        } catch (\Exception $e) {

            Register::create([
                'destino' => $request->input('destino'),
                'mensaje' => $request->input('mensaje'),
                'estado' => 'error: ' . $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}