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
<<<<<<< HEAD
    public function index()
    {
        $url = $this->apiUrl . '/categories/'; 
        $response = Http::withHeaders(['X_API_KEY' => $this->apiKey])->get($url);
        return $response->json();
=======
    public function prediction(Request $request)
    {

        $url = $this->apiUrl . '/';

        $response = Http::post($url, [
            'comment' => $request->input('comment')
        ]);
        return response()->json();
>>>>>>> 05128697a2992a1a3af8e76dada0ac199382c3cc
    }


    /**
     * Show the form for creating a new resource.
     */

}
