<?php

namespace App\Http\Controllers\ReportController\Linkages;
use App\Http\Controllers\Controller;
use App\Models\FileArchive;
use App\Models\Linkages;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(){
        $linkages = Linkages::get();
        $pdf = PDF::loadView('admin.reports.linkages.linkages',  compact('linkages'));
      
        $fileName = 'LINKAGES_' . date('Y_m_d_H_i_s') . '.pdf';
        
        $filePath = public_path('reports/' . $fileName);

        $pdf->save($filePath);
        FileArchive::create([
            'filename' => $fileName, 
            'module_id' => 7,
            'created_by' => auth()->user()->id,
        ]);

        return response()->json(['message' => 'Report generated successfully'], 200);
        // return $pdf->stream('RESEARCH_AND_EXTENSION_.pdf');
    }
}
