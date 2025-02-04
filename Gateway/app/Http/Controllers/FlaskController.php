<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FlaskController extends Controller
{

    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('MICROSERVICIO_FLASK');
    }
    /**
     * Display a listing of the resource.
     */
    public function prediction()
    {
        $url = $this->apiUrl . '/';
        $response = Http::get($url);
        return $response->json();   
    }

    /**
     * Show the form for creating a new resource.
     */

}
