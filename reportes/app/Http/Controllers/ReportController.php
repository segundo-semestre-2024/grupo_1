<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteExport;
use App\Models\Report;

class ReportController extends Controller
{
    public function index()
    {
        $reports= Report::all();
        return $reports;
    }
    
    public function store(Request $request)
    {
        $report= new Report();  
        $report->name= $request->name;
        $report->description= $request->description;
        $report->clasification= $request->clasification;
        $report->save();
        return $report;
    }

    public function update(Request $request, string $id)
    {
        $report= Report::find($id);
        $report->name= $request->name;
        $report->description= $request->description;
        $report->clasification= $request->clasification;
        $report->save();
        return $report;
    }

    public function destroy(string $id)
    {
        $report= Report::find($id);
        $report->delete();
    }

    /*public function generarPDF()
    {
        $data = ['titulo' => 'Reporte de Ejemplo', 'contenido' => 'Este es un reporte en PDF.'];
        $pdf = Pdf::loadView('reportes.pdf', $data);

        return $pdf->download('reporte.pdf');
    }*/

    // Generar PDF
        public function pdf($id)
    {
        $report= Report::find($id);
        $pdf= \PDF::loadView('reportepdf', compact('report'));
        return $pdf->download('unreporte');
    }

    // Generar Excel
    public function generarExcel()
    {
        return Excel::download(new ReportExport, 'reporte.xlsx');
    }
}