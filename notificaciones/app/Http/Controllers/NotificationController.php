<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\User;
use App\Models\Notification;

class NotificationController extends Controller
{
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
}