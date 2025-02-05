<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FlaskController extends Controller
{
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = env('MICROSERVICIO_FLASK');
        $this->apiKey = env('X_API_KEY');
    }
    /**
     * Display a listing of the resource.
     */

     public function prediction(Request $request)
     {
         $comment = $request->input('comment');
 
         $response = Http::post(env('MICROSERVICIO_FLASK') . '/prediction', [
             'comment' => $comment
         ]);
 
         if ($response->successful()) {
             $prediction = $response->json('prediction'); // Ajusta esto según la estructura de tu respuesta de Flask
             return response()->json(['prediction' => $prediction]);
         } else {
             return response()->json(['error' => 'Error al comunicarse con el microservicio Flask'], $response->status());
         }
     }


    /**
     * Show the form for creating a new resource.
     */

}
