<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\User;
use App\Models\Notification;

class NotificationController extends Controller
{
    // Enviar SMS con Twilio (1)
    public function enviarSms()
    {
        //Obtener las credenciales de Twilio desde el archivo .env
        $sid= env('TWILIO_SID');
        $token= env('TWILIO_AUTH_TOKEN');
        $twilio= new Client($sid,$token);

        $destino= '+573207489489';
        $mensaje= 'Mensaje enviado';

        //Enviar el mensaje de texto
        $twilio->messages->create(
            $destino,
            [
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => $mensaje
            ]
        );
        return response()->json([
            'message'=> 'Mensaje enviado correctamente'
        ]);
    }

    // Enviar SMS con Twilio (Todos los numeros registrados en la BD)
    public function enviarSmsTodos()
    {
        $users= User::all();
        $sid= env('TWILIO_SID');
        $token= env('TWILIO_AUTH_TOKEN');
        $twilio= new Client($sid,$token);
        $mensaje= 'Mensaje enviado';

        foreach ($users as $user){
            $twilio->messages->create(
                $user->phone_number,
                [
                    'from' => env('TWILIO_PHONE_NUMBER'),
                    'body' => $mensaje
                ]
            );
        }
    return 'Mensajes enviados exitosamente';    
    }


    // Enviar correo electrónico
    public function sendEmail(Request $request)
    {
        $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string',
            'body' => 'required|string',
        ]);

        try {
            Mail::raw($request->body, function ($message) use ($request) {
                $message->to($request->to)
                        ->subject($request->subject);
            });

            return response()->json(['message' => 'Correo enviado con éxito'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}