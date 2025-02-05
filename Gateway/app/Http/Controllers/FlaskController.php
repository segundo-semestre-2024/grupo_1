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

        $url = $this->apiUrl . '/prediction';

        $response = Http::post($url, [
            'comment' => $request->input('comment')
        ]);
        return response()->json();
    }



    /**
     * Show the form for creating a new resource.
     */

}
