<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade as PDF;

use Illuminate\Http\Request;
use App\Models\Report;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = Report::all();
        $data = [
            'titulo' => 'Reporte de Usuarios',
            'datos' => $reports
        ];

        // Cargar la vista y pasar los datos
        $pdf = PDF::loadView('report', $data);

        // Generar el PDF y devolverlo como una descarga
        return $pdf->download('reporte_usuarios.pdf');

    }

    public function categoriesall()
    {

    }
}