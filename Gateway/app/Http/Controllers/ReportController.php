<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReportController extends Controller
{
    private $apiUrl;
    private $apiKey;

    public function __construct()
    {
        // URL del microservicio de reportes
        $this->apiUrl = env('MICROSERVICIO_REP_URL');
        $this->apiKey = env('X_API_KEY');
    }

    // Reenviar petición para generar un PDF
    public function generarPDF()
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}"
        ])->get("{$this->apiUrl}/reportes-pdf");

        return response($response->body(), $response->status())
                ->header('Content-Type', $response->header('Content-Type'))
                ->header('Content-Disposition', $response->header('Content-Disposition'));
    }

    // Reenviar petición para generar un Excel
    public function generarExcel()
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}"
        ])->get("{$this->apiUrl}/reportes-excel");

        return response($response->body(), $response->status())
                ->header('Content-Type', $response->header('Content-Type'))
                ->header('Content-Disposition', $response->header('Content-Disposition'));
    }
}