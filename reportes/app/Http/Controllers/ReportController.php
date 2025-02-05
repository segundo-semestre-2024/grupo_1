<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteExport;

class ReportController extends Controller
{
    // Generar PDF
    public function generarPDF()
    {
        $data = ['titulo' => 'Reporte de Ejemplo', 'contenido' => 'Este es un reporte en PDF.'];
        $pdf = Pdf::loadView('reportes.pdf', $data);

        return $pdf->download('reporte.pdf');
    }

    // Generar Excel
    public function generarExcel()
    {
        return Excel::download(new ReportExport, 'reporte.xlsx');
    }
}