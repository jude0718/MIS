<?php

namespace App\Http\Controllers\ReportController\StudentDevelopment;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\FileArchive;
use App\Models\StudentOrganizations;

class IndexController extends Controller
{
    public function index(){
        $organizations = StudentOrganizations::get();
      
        $pdf = PDF::loadView('admin.reports.student_development.student_development', compact('organizations'));

        $fileName = 'STUDENT_DEVELOPMENT_' . date('Y_m_d_H_i_s') . '.pdf';
        
        $filePath = public_path('reports/' . $fileName);

        $pdf->save($filePath);
        FileArchive::create([
            'filename' => $fileName, 
            'module_id' => 4,
            'created_by' => auth()->user()->id,
        ]);

        return response()->json(['message' => 'Report generated successfully'], 200);
        // return $pdf->stream('STUDENT_DEVELOPMENT_.pdf');
    }
}
